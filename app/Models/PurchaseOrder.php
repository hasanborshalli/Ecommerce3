<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'supplier_id', 'reference_number',
        'order_date', 'expected_date', 'received_date',
        'total_cost', 'status', 'notes',
    ];

    protected $casts = [
        'order_date'    => 'date',
        'expected_date' => 'date',
        'received_date' => 'date',
        'total_cost'    => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    // ── Accessors ─────────────────────────────────────────────────

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'ordered'   => ['class' => 'po-status-ordered',   'label' => 'Ordered'],
            'received'  => ['class' => 'po-status-received',  'label' => 'Received'],
            'cancelled' => ['class' => 'po-status-cancelled', 'label' => 'Cancelled'],
            default     => ['class' => 'po-status-draft',     'label' => 'Draft'],
        };
    }

    // ── Helpers ───────────────────────────────────────────────────

    public static function generateReference(): string
    {
        $year = date('Y');
        $last = static::whereYear('created_at', $year)->count() + 1;
        return 'PO-' . $year . '-' . str_pad($last, 4, '0', STR_PAD_LEFT);
    }

    public function recalculateTotal(): void
    {
        $this->update(['total_cost' => $this->items()->sum('total_cost')]);
    }

    /**
     * Mark as received: update each item's received qty,
     * call product->addStock(), update received_date + status.
     */
    public function markReceived(): void
    {
        foreach ($this->items as $item) {
            $pending = $item->quantity_ordered - $item->quantity_received;
            if ($pending > 0 && $item->product) {
                $item->product->addStock(
                    $pending,
                    (float) $item->cost_per_unit,
                    'purchase_order',
                    $this->id,
                    "PO {$this->reference_number}"
                );
                $item->update(['quantity_received' => $item->quantity_ordered]);
            }
        }

        $this->update([
            'status'        => 'received',
            'received_date' => now()->toDateString(),
        ]);
    }
}
