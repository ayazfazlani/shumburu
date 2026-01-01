<?php

namespace App\Models;

use App\Models\FinishedGood;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MaterialStockOutLine extends Model
{
    protected $guarded = [];

    protected $casts = [
        'quantity_consumed' => 'decimal:2',
        'quantity_returned' => 'decimal:2',
    ];

    public function materialStockOut()
    {
        return $this->belongsTo(MaterialStockOut::class, 'material_stock_out_id');
    }

    public function productionLine()
    {
        return $this->belongsTo(ProductionLine::class, 'production_line_id');
    }

    public function returnedBy()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    public function scrapWastes()
    {
        return $this->hasMany(ScrapWaste::class, 'material_stock_out_line_id');
    }

    public function finishedGoods()
    {
        return $this->belongsToMany(
            FinishedGood::class,
            'finished_good_material_stock_out_line',
            'material_stock_out_line_id',
            'finished_good_id'
        )->withPivot('quantity_used')->withTimestamps();
    }

    public function materialStockOutLines()
    {
        return $this->belongsToMany(
            MaterialStockOutLine::class,
            'finished_good_material_stock_out_line'
        )->withPivot('quantity_used')->withTimestamps();
    }

    /**
     * Get total quantity used from this stock out line in finished goods
     */
    public function getTotalUsedQuantityAttribute()
    {
        return $this->finishedGoods()->sum('finished_good_material_stock_out_line.quantity_used') ?? 0;
    }

    /**
     * Get available quantity that can still be used
     * Available = quantity_consumed - total_used - quantity_returned
     */
    public function getAvailableQuantityAttribute()
    {
        $used = $this->total_used_quantity;
        $returned = $this->quantity_returned ?? 0;
        $available = $this->quantity_consumed - $used - $returned;
        return max(0, $available);
    }

    /**
     * Get the total quantity used across all lines for a specific MaterialStockOut
     */
    public static function getTotalUsedForStockOut($materialStockOutId)
    {
        return self::where('material_stock_out_id', $materialStockOutId)
            ->get()
            ->sum(function ($line) {
                return $line->total_used_quantity;
            });
    }

    /**
     * Get total returned for a specific MaterialStockOut
     */
    public static function getTotalReturnedForStockOut($materialStockOutId)
    {
        return self::where('material_stock_out_id', $materialStockOutId)
            ->sum('quantity_returned') ?? 0;
    }

    /**
     * Get available quantity for a MaterialStockOut (can be used in new lines)
     * Available = MaterialStockOut.quantity - total_used_in_all_lines - total_returned
     */
    public static function getAvailableForStockOut($materialStockOutId)
    {
        $stockOut = MaterialStockOut::find($materialStockOutId);
        if (!$stockOut) {
            return 0;
        }

        $totalUsed = self::getTotalUsedForStockOut($materialStockOutId);
        $totalReturned = self::getTotalReturnedForStockOut($materialStockOutId);
        
        $available = $stockOut->quantity - $totalUsed - $totalReturned;
        return max(0, $available);
    }
}
