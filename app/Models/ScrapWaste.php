<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScrapWaste extends Model
{
  use HasFactory;
  protected $table = 'scrap_waste';

  // protected $fillable = [
  //   'raw_material_id',
  //   'quantity',
  //   'reason',
  //   'waste_date',
  //   'recorded_by',
  //   'notes',
  //   'type',
  //   'material_id',
  //   'product_id',
  //   'unit',
  //   'disposal_method',
  //   'cost',
  //   'status',
  //   'material_stock_out_line_id'
  // ];

  protected $guarded = [];

  protected $casts = [
    'quantity' => 'decimal:3',

  ];

  public function rawMaterial(): BelongsTo
  {
    return $this->belongsTo(RawMaterial::class);
  }

  public function recordedBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'recorded_by');
  }

  public function materialStockOutLine()
  {
    return $this->belongsTo(\App\Models\MaterialStockOutLine::class, 'material_stock_out_line_id');
  }
}