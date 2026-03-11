<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'product_id', 'customer_id', 'order_id',
        'author_name', 'author_email',
        'rating', 'title', 'body',
        'status', 'approved_at',
    ];

    protected $casts = [
        'rating'      => 'integer',
        'approved_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // ── Accessors ─────────────────────────────────────────────────

    /** Array of filled/empty star booleans for blade loops */
    public function getStarsAttribute(): array
    {
        return array_map(fn ($i) => $i <= $this->rating, range(1, 5));
    }

    /**
     * Unified name accessor used by admin views.
     * Returns author_name (frozen at submission time).
     */
    public function getCustomerNameAttribute(): string
    {
        return $this->author_name;
    }

    // ── Lifecycle ─────────────────────────────────────────────────

    /** Approve and refresh product aggregates */
    public function approve(): void
    {
        $this->update([
            'status'      => 'approved',
            'approved_at' => now(),
        ]);
        $this->product?->refreshReviewAggregates();
    }

    /** Reject and refresh product aggregates */
    public function reject(): void
    {
        $this->update(['status' => 'rejected', 'approved_at' => null]);
        $this->product?->refreshReviewAggregates();
    }

    protected static function booted(): void
    {
        static::deleted(function (Review $review) {
            $review->product?->refreshReviewAggregates();
        });
    }
}
