<?php

namespace App\Models;

use App\Models\FinishedGood;
use Illuminate\Database\Eloquent\Model;

class MaterialStockOutLine extends Model
{
    protected $guarded = [];

    public function materialStockOut()
    {
        return $this->belongsTo(MaterialStockOut::class, 'material_stock_out_id');
    }

    public function productionLine()
    {
        return $this->belongsTo(ProductionLine::class, 'production_line_id');
    }

    public function scrapWastes()
    {
        return $this->hasMany(ScrapWaste::class, 'material_stock_out_line_id');
    }

    public function finishedGoods()
    {
        return $this->hasMany(FinishedGood::class);
    }
}
