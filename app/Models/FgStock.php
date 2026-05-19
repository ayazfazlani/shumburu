<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FgStock extends Model
{
    protected $table = 'fg_stock';

    protected $fillable = [
        'product_id',
        'batch_number',
        'quantity',
        'status',
        'location',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(StockReservation::class, 'fg_stock_id')->where('status', 'active');
    }

    public function getReservedQuantityAttribute()
    {
        return $this->reservations()->sum('quantity');
    }

    public function getAvailableQuantityAttribute()
    {
        return $this->quantity - $this->reserved_quantity;
    }
}
