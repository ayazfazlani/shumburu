<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseRequest extends Model
{
    protected $fillable = [
        'raw_material_id',
        'production_request_id',
        'quantity',
        'status',
        'requested_by',
        'notes',
        'supplier_id',
        'po_number',
        'unit_price',
        'expected_delivery_date',
        'po_issued_at',
        'approved_by',
        'approved_at',
        'delivered_at',
        'received_at',
    ];

    protected $casts = [
        'expected_delivery_date' => 'date',
        'po_issued_at'           => 'datetime',
        'approved_at'            => 'datetime',
        'delivered_at'           => 'datetime',
        'received_at'            => 'datetime',
        'unit_price'             => 'decimal:4',
        'quantity'               => 'decimal:2',
    ];

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function productionRequest(): BelongsTo
    {
        return $this->belongsTo(ProductionRequest::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchasePayments(): HasMany
    {
        return $this->hasMany(PurchasePayment::class);
    }

    public function getTotalAmountAttribute(): float
    {
        return round((float)$this->quantity * (float)($this->unit_price ?? 0), 2);
    }

    public function getTotalPaidAttribute(): float
    {
        return (float)$this->purchasePayments()->sum('amount');
    }

    public function getBalanceDueAttribute(): float
    {
        return max(0, $this->total_amount - $this->total_paid);
    }
}
