<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinishedGoodMaterialStockOutLine extends Model
{
    use HasFactory;

    protected $table = 'finished_good_material_stock_out_line';
    // protected $table = 'finished_good_stock_out_line';

    protected $fillable = [
        'finished_good_id',
        'material_stock_out_line_id',
        'quantity_consumed',
        'quantity_used'
    ];

    public function finishedGood()
    {
        return $this->belongsTo(FinishedGood::class);
    }

    public function materialStockOutLine()
    {
        return $this->belongsTo(MaterialStockOutLine::class);
    }
} 