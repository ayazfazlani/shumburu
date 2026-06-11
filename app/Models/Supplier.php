<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'payment_terms',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function purchaseRequests(): HasMany
    {
        return $this->hasMany(PurchaseRequest::class);
    }

    public function purchasePayments(): HasMany
    {
        return $this->hasMany(PurchasePayment::class);
    }

    /**
     * Total value of all received POs from this supplier
     */
    public function getTotalPurchaseValueAttribute(): float
    {
        return $this->purchaseRequests()
            ->whereIn('status', ['received'])
            ->get()
            ->sum(fn($pr) => $pr->quantity * ($pr->unit_price ?? 0));
    }
}
