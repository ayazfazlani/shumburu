<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'size',
        'pn',
        'meter_length',
        'description',
        'is_active',
        'weight_per_meter',
    ];

    protected $casts = [
        'meter_length' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function finishedGoods(): HasMany
    {
        return $this->hasMany(FinishedGood::class);
    }

    public function productionOrders(): HasMany
    {
        return $this->hasMany(ProductionOrder::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    public function fyaWarehouses(): HasMany
    {
        return $this->hasMany(FyaWarehouse::class);
    }
}
