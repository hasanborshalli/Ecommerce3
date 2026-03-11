<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'sku',
        'short_description', 'description',
        'price', 'sale_price', 'cost_price',
        'stock', 'low_stock_threshold', 'show_when_out_of_stock',
        'main_image', 'gallery', 'variants',
        'is_active', 'is_featured', 'is_new', 'is_on_sale',
        'review_count', 'review_avg',
        'meta_title', 'meta_description', 'meta_keywords',
        'sort_order',
    ];

    protected $casts = [
        'price'                  => 'decimal:2',
        'sale_price'             => 'decimal:2',
        'cost_price'             => 'decimal:2',
        'review_avg'             => 'decimal:2',
        'gallery'                => 'array',
        'variants'               => 'array',
        'is_active'              => 'boolean',
        'is_featured'            => 'boolean',
        'is_new'                 => 'boolean',
        'is_on_sale'             => 'boolean',
        'show_when_out_of_stock' => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('status', 'approved')->latest();
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_active', true)
                     ->where(function ($q) {
                         $q->where('stock', '>', 0)
                           ->orWhere('show_when_out_of_stock', true);
                     });
    }

    public function scopeOrderable($query)
    {
        return $query->where('is_active', true)->where('stock', '>', 0);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->where('is_active', true);
    }

    public function scopeNewArrivals($query)
    {
        return $query->where('is_new', true)->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'low_stock_threshold')
                     ->where('stock', '>', 0);
    }

    public function scopeOnSale($query)
    {
        return $query->where('is_on_sale', true)
                     ->whereNotNull('sale_price')
                     ->where('is_active', true);
    }

    // ── Accessors ─────────────────────────────────────────────────

    /** Current selling price — sale price if on sale, otherwise regular price */
    public function getCurrentPriceAttribute(): float
    {
        return ($this->is_on_sale && $this->sale_price !== null)
            ? (float) $this->sale_price
            : (float) $this->price;
    }

  

    /** Alias — views may use $product->effective_price */
    public function getEffectivePriceAttribute(): float
    {
        return $this->current_price;
    }

    /** Profit margin % based on cost_price vs current selling price */
    public function getMarginPercentAttribute(): float
    {
        $price = $this->current_price;
        $cost  = (float) $this->cost_price;
        if ($price <= 0) return 0;
        return round((($price - $cost) / $price) * 100, 1);
    }

    /** Star rating rounded to nearest 0.5 for display */
    public function getStarRatingAttribute(): float
    {
        return round((float) $this->review_avg * 2) / 2;
    }

    // ── Stock Helpers ─────────────────────────────────────────────

    /**
     * Add stock and log a StockMovement.
     *
     * @param int    $qty
     * @param float  $unitCost       updates cost_price if > 0
     * @param string $refType        'purchase_order' | 'manual' | 'initial'
     * @param int|null $refId
     * @param string $notes
     */
    public function addStock(
        int $qty,
        float $unitCost = 0,
        string $refType = 'manual',
        ?int $refId = null,
        string $notes = ''
    ): void {
        $before = $this->stock;
        $this->stock += $qty;
        if ($unitCost > 0) {
            $this->cost_price = $unitCost;
        }
        $this->save();

        StockMovement::create([
            'product_id'      => $this->id,
            'type'            => $refType === 'purchase_order' ? 'purchase_received' : 'manual_addition',
            'quantity_before' => $before,
            'quantity_change' => $qty,
            'quantity_after'  => $this->stock,
            'reference_type'  => $refType,
            'reference_id'    => $refId,
            'notes'           => $notes,
        ]);
    }

    /**
     * Deduct stock and log a StockMovement.
     *
     * @param int    $qty
     * @param string $refType  'order' | 'manual'
     * @param int|null $refId
     * @param string $notes
     */
    public function deductStock(
        int $qty,
        string $refType = 'order',
        ?int $refId = null,
        string $notes = ''
    ): void {
        $before = $this->stock;
        $this->stock = max(0, $this->stock - $qty);
        $this->save();

        StockMovement::create([
            'product_id'      => $this->id,
            'type'            => $refType === 'order' ? 'order_placed' : 'manual_adjustment',
            'quantity_before' => $before,
            'quantity_change' => -$qty,
            'quantity_after'  => $this->stock,
            'reference_type'  => $refType,
            'reference_id'    => $refId,
            'notes'           => $notes,
        ]);
    }

    // ── Review Aggregate Refresh ──────────────────────────────────

    /**
     * Recalculate review_count and review_avg from approved reviews.
     * Call after any review is approved, rejected, or deleted.
     */
    public function refreshReviewAggregates(): void
    {
        $approved = $this->reviews()->where('status', 'approved');
        $count    = $approved->count();
        $avg      = $count > 0 ? $approved->avg('rating') : 0;

        $this->update([
            'review_count' => $count,
            'review_avg'   => round($avg, 2),
        ]);
    }

    /**
     * True if the given customer has a non-cancelled order containing this product.
     * Used for the "Verified Purchase" badge on the storefront review list.
     */
    public function hasBeenOrdered(int $customerId): bool
    {
        if (!$customerId) return false;

        return \App\Models\OrderItem::whereHas('order', function ($q) use ($customerId) {
            $q->where('customer_id', $customerId)
              ->where('status', '!=', 'cancelled');
        })->where('product_id', $this->id)->exists();
    }
}