<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionLine extends Model
{
    protected $guarded = [];
    public function materialStockOuts()
    {
        return $this->belongsToMany(MaterialStockOut::class, 'material_stock_out_line')
            ->withPivot('quantity_consumed')
            ->withTimestamps();
    }
}