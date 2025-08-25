<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinishedGood extends Model
{
  use HasFactory;

  protected $fillable = [
    'product_id',
    'quantity',
    'batch_number',
    'production_date',
    'purpose',
    'customer_id',
    'produced_by',
    'notes',
    // 'material_stock_out_line_id',
    'type',
    'length_m',
    'outer_diameter',
    'quantity',
    'total_weight',
    'size',
    'surface',
    'thickness',
    'ovality',
    'stripe_color',
  ];

  // pro
  protected $casts = [
    'quantity' => 'decimal:2',
    'production_date' => 'date',
  ];

  public function fyaMovements()
  {
    return $this->hasMany(FyaWarehouse::class);
  }

  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class);
  }

  public function customer(): BelongsTo
  {
    return $this->belongsTo(Customer::class);
  }

  public function producedBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'produced_by');
  }

  public function materialStockOutLine()
  {
    return $this->belongsTo(MaterialStockOutLine::class, 'material_stock_out_line_id');
  }

  public function materialStockOutLines()
  {
    return $this->belongsToMany(
      MaterialStockOutLine::class,
      'finished_good_material_stock_out_line',
      'finished_good_id',
      'material_stock_out_line_id'
    )->withPivot('quantity_used')->withTimestamps();
  }
}
