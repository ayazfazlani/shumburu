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
}
