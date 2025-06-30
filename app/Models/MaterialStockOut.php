<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialStockOut extends Model
{
  use HasFactory;

  protected $fillable = [
    'raw_material_id',
    'quantity',
    'batch_number',
    'issued_date',
    'issued_by',
    'status',
    'notes',
  ];

  protected $casts = [
    'quantity' => 'decimal:3',
    'issued_date' => 'date',
  ];

  public function rawMaterial(): BelongsTo
  {
    return $this->belongsTo(RawMaterial::class);
  }

  public function issuedBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'issued_by');
  }

  public function productionLines()
  {
    return $this->belongsToMany(ProductionLine::class, 'material_stock_out_line')
      ->withPivot('quantity_consumed')
      ->withTimestamps();
  }
}