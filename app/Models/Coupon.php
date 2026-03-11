<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'description', 'type', 'value',
        'min_order_amount', 'max_uses', 'used_count',
        'max_uses_per_customer', 'is_active', 'expires_at',
    ];

    protected $casts = [
        'value'                  => 'decimal:2',
        'min_order_amount'       => 'decimal:2',
        'is_active'              => 'boolean',
        'expires_at'             => 'datetime',
        'max_uses'               => 'integer',
        'used_count'             => 'integer',
        'max_uses_per_customer'  => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function uses(): HasMany
    {
        return $this->hasMany(CouponUse::class);
    }

    // ── Accessors ─────────────────────────────────────────────────

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getIsUsedUpAttribute(): bool
    {
        return $this->max_uses !== null && $this->used_count >= $this->max_uses;
    }

    // ── Validation ───────────────────────────────────────────────

    /**
     * Validate a coupon against the current cart state.
     *
     * @param  float       $cartSubtotal   pre-discount cart total
     * @param  int|null    $customerId     null for guest
     * @param  string|null $customerEmail  for guest per-email tracking
     * @return array{valid: bool, error: string|null}
     */
    public function validate(
        float $cartSubtotal,
        ?int $customerId = null,
        ?string $customerEmail = null
    ): array {
        if (!$this->is_active) {
            return ['valid' => false, 'error' => 'This coupon is no longer active.'];
        }
        if ($this->is_expired) {
            return ['valid' => false, 'error' => 'This coupon has expired.'];
        }
        if ($this->is_used_up) {
            return ['valid' => false, 'error' => 'This coupon has reached its usage limit.'];
        }
        if ($cartSubtotal < (float) $this->min_order_amount) {
            return [
                'valid' => false,
                'error' => 'Minimum order of ' . number_format($this->min_order_amount, 2) . ' required for this coupon.',
            ];
        }

        // Per-customer usage check
        if ($this->max_uses_per_customer > 0) {
            $usedByCustomer = 0;
            if ($customerId) {
                $usedByCustomer = $this->uses()->where('customer_id', $customerId)->count();
            } elseif ($customerEmail) {
                $usedByCustomer = $this->uses()->where('customer_email', $customerEmail)->count();
            }
            if ($usedByCustomer >= $this->max_uses_per_customer) {
                return ['valid' => false, 'error' => 'You have already used this coupon.'];
            }
        }

        return ['valid' => true, 'error' => null];
    }

    /**
     * Calculate the discount amount for a given subtotal and shipping.
     *
     * @param  float $subtotal
     * @param  float $shippingCost
     * @return float  discount to subtract (never exceeds subtotal + shipping)
     */
    public function calculateDiscount(float $subtotal, float $shippingCost = 0): float
    {
        return match ($this->type) {
            'percentage'   => round($subtotal * ((float) $this->value / 100), 2),
            'fixed'        => min((float) $this->value, $subtotal),
            'free_shipping' => $shippingCost,
            default        => 0,
        };
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
