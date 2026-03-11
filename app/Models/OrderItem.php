<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id',
        'product_name', 'product_sku',
        'product_price', 'product_cost',
        'quantity', 'variant',
        'line_total', 'line_cost', 'line_profit',
    ];

    protected $casts = [
        'product_price' => 'decimal:2',
        'product_cost'  => 'decimal:2',
        'line_total'    => 'decimal:2',
        'line_cost'     => 'decimal:2',
        'line_profit'   => 'decimal:2',
        'variant'       => 'array',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ── Accessors ─────────────────────────────────────────────────

    /** Human-readable variant string — e.g. "Size: M / Color: Black" */
    public function getVariantLabelAttribute(): string
    {
        $v = $this->variant;
        if (!is_array($v) || empty($v)) return '';
        return implode(' / ', array_map(
            fn ($k, $val) => "{$k}: {$val}",
            array_keys($v),
            array_values($v)
        ));
    }
}
