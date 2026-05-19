<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockReservation extends Model
{
    protected $fillable = [
        'order_item_id',
        'fg_stock_id',
        'quantity',
        'status',
    ];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function fgStock(): BelongsTo
    {
        return $this->belongsTo(FgStock::class, 'fg_stock_id');
    }
}
