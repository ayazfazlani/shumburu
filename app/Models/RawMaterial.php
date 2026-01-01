<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'unit',
        'is_active',
        'quantity',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'quantity' => 'decimal:3',
    ];

    public function stockIns(): HasMany
    {
        return $this->hasMany(MaterialStockIn::class);
    }

    public function stockOuts(): HasMany
    {
        return $this->hasMany(MaterialStockOut::class);
    }

    public function scrapWaste(): HasMany
    {
        return $this->hasMany(ScrapWaste::class);
    }

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function getBalanceAtDate($date)
    {
        $lastTransaction = $this->transactions()
            ->whereDate('transaction_date', '<=', $date)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        return $lastTransaction ? $lastTransaction->balance_after : 0;
    }

    public function getDailyMovements($date)
    {
        return $this->transactions()
            ->whereDate('transaction_date', $date)
            ->get()
            ->groupBy('type')
            ->map(function ($transactions) {
                return $transactions->sum('quantity');
            });
    }
}
