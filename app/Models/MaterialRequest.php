<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialRequest extends Model
{
    protected $fillable = [
        'production_request_id',
        'production_plan_id',
        'raw_material_id',
        'quantity',
        'status', // pending, approved, issued, purchase_raised
        'requested_by',
        'notes',
    ];

    public function productionRequest(): BelongsTo
    {
        return $this->belongsTo(ProductionRequest::class);
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function productionPlan(): BelongsTo
    {
        return $this->belongsTo(ProductionPlan::class);
    }

    public function getOrderNumberAttribute(): string
    {
        if ($this->productionRequest && $this->productionRequest->orderItem && $this->productionRequest->orderItem->productionOrder) {
            return $this->productionRequest->orderItem->productionOrder->order_number;
        }
        if ($this->productionPlan && $this->productionPlan->productionOrder) {
            return $this->productionPlan->productionOrder->order_number;
        }
        return 'Manual Planning';
    }

    public function getCustomerNameAttribute(): string
    {
        if ($this->productionRequest && $this->productionRequest->orderItem && $this->productionRequest->orderItem->productionOrder && $this->productionRequest->orderItem->productionOrder->customer) {
            return $this->productionRequest->orderItem->productionOrder->customer->name;
        }
        if ($this->productionPlan && $this->productionPlan->productionOrder && $this->productionPlan->productionOrder->customer) {
            return $this->productionPlan->productionOrder->customer->name;
        }
        return 'N/A';
    }

    public function getProductNameAttribute(): string
    {
        if ($this->productionRequest && $this->productionRequest->product) {
            return $this->productionRequest->product->name;
        }
        if ($this->productionPlan && $this->productionPlan->productionOrder) {
            $firstItem = $this->productionPlan->productionOrder->orderItems->first();
            if ($firstItem && $firstItem->product) {
                return $firstItem->product->name;
            }
        }
        return 'Unknown Product';
    }

    public function getPlanReferenceIdAttribute()
    {
        return $this->production_plan_id ?? $this->production_request_id ?? 'N/A';
    }
}

