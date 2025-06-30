<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Delivery extends Model
{
  use HasFactory;

  protected $fillable = [
    'production_order_id',
    'customer_id',
    'product_id',
    'quantity',
    'batch_number',
    'unit_price',
    'total_amount',
    'delivery_date',
    'delivered_by',
    'notes',
  ];

  protected $casts = [
    'quantity' => 'decimal:2',
    'unit_price' => 'decimal:2',
    'total_amount' => 'decimal:2',
    'delivery_date' => 'date',
  ];

  public function productionOrder(): BelongsTo
  {
    return $this->belongsTo(ProductionOrder::class);
  }

  public function customer(): BelongsTo
  {
    return $this->belongsTo(Customer::class);
  }

  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class);
  }

  public function deliveredBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'delivered_by');
  }

  public function payments(): HasMany
  {
    return $this->hasMany(Payment::class);
  }
}
