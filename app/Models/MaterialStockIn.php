<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialStockIn extends Model
{
  use HasFactory;

  protected $fillable = [
    'raw_material_id',
    'quantity',
    'batch_number',
    'received_date',
    'received_by',
    'notes',
  ];

  protected $casts = [
    'quantity' => 'decimal:3',
    'received_date' => 'date',
  ];

  public function rawMaterial(): BelongsTo
  {
    return $this->belongsTo(RawMaterial::class);
  }

  public function receivedBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'received_by');
  }
}
