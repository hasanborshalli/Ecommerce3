<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;

class Customer extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone',
        'password', 'email_verified_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // ── Accessors ─────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    // ── Relationships ─────────────────────────────────────────────

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function defaultAddress(): ?CustomerAddress
    {
        return $this->addresses()->where('is_default', true)->first()
            ?? $this->addresses()->latest()->first();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->latest();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // ── Helpers ───────────────────────────────────────────────────

    /** Check whether this customer has already reviewed a product */
    public function hasReviewed(int $productId): bool
    {
        return $this->reviews()->where('product_id', $productId)->exists();
    }

    /** Check whether this customer has ordered a product (for verified purchase badge) */
    public function hasOrdered(int $productId): bool
    {
        return $this->orders()
            ->whereHas('items', fn ($q) => $q->where('product_id', $productId))
            ->whereIn('status', ['delivered', 'shipped', 'processing', 'confirmed'])
            ->exists();
    }
}
