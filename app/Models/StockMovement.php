<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id', 'type',
        'quantity_before', 'quantity_change', 'quantity_after',
        'reference_type', 'reference_id', 'notes',
    ];

    protected $casts = [
        'quantity_before' => 'integer',
        'quantity_change' => 'integer',
        'quantity_after'  => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'order_placed'       => 'Order Placed',
            'order_cancelled'    => 'Order Cancelled',
            'purchase_received'  => 'Purchase Received',
            'manual_addition'    => 'Manual Addition',
            'manual_adjustment'  => 'Manual Adjustment',
            'initial_stock'      => 'Initial Stock',
            default              => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }
}
