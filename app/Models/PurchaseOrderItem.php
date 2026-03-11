<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id', 'product_id', 'product_name',
        'quantity_ordered', 'quantity_received',
        'cost_per_unit', 'total_cost',
    ];

    protected $casts = [
        'cost_per_unit' => 'decimal:2',
        'total_cost'    => 'decimal:2',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getQuantityPendingAttribute(): int
    {
        return max(0, $this->quantity_ordered - $this->quantity_received);
    }
}
