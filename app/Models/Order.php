<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'customer_id',
        'customer_name', 'customer_email', 'customer_phone',
        'shipping_address', 'shipping_city', 'shipping_state',
        'shipping_postal_code', 'shipping_country',
        'subtotal', 'shipping_cost', 'discount', 'total',
        'coupon_id', 'coupon_code', 'coupon_discount',
        'cost_total', 'status', 'payment_status', 'payment_method', 'notes',
    ];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'shipping_cost'   => 'decimal:2',
        'discount'        => 'decimal:2',
        'total'           => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'cost_total'      => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Accessors ─────────────────────────────────────────────────

    public function getProfitAttribute(): float
    {
        return (float) $this->total - (float) $this->cost_total;
    }

    public function getProfitMarginAttribute(): float
    {
        if ((float) $this->total <= 0) return 0;
        return round(($this->profit / (float) $this->total) * 100, 1);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'pending'    => ['class' => 'badge-warning', 'label' => 'Pending'],
            'confirmed'  => ['class' => 'badge-info',    'label' => 'Confirmed'],
            'processing' => ['class' => 'badge-info',    'label' => 'Processing'],
            'shipped'    => ['class' => 'badge-success', 'label' => 'Shipped'],
            'delivered'  => ['class' => 'badge-success', 'label' => 'Delivered'],
            'cancelled'  => ['class' => 'badge-danger',  'label' => 'Cancelled'],
            default      => ['class' => 'badge-neutral', 'label' => ucfirst($this->status)],
        };
    }

    public function getPaymentBadgeAttribute(): array
    {
        return match ($this->payment_status) {
            'paid'     => ['class' => 'badge-success', 'label' => 'Paid'],
            'refunded' => ['class' => 'badge-warning', 'label' => 'Refunded'],
            default    => ['class' => 'badge-muted',   'label' => 'Unpaid'],
        };
    }

    // ── Helpers ───────────────────────────────────────────────────

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . strtoupper(Str::random(8));
        } while (static::where('order_number', $number)->exists());

        return $number;
    }
}