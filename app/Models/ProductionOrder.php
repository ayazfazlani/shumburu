<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionOrder extends Model
{
  use HasFactory;

  protected $fillable = [
    'order_number',
    'customer_id',
    'product_id',
    'quantity',
    'status',
    'requested_date',
    'production_start_date',
    'production_end_date',
    'delivery_date',
    'requested_by',
    'approved_by',
    'plant_manager_id',
    'notes',
  ];

  protected $casts = [
    'quantity' => 'decimal:2',
    'requested_date' => 'date',
    'production_start_date' => 'date',
    'production_end_date' => 'date',
    'delivery_date' => 'date',
  ];

  public function customer(): BelongsTo
  {
    return $this->belongsTo(Customer::class);
  }

  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class);
  }

  public function requestedBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'requested_by');
  }

  public function approvedBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'approved_by');
  }

  public function plantManager(): BelongsTo
  {
    return $this->belongsTo(User::class, 'plant_manager_id');
  }

  public function payments(): HasMany
  {
    return $this->hasMany(Payment::class);
  }
}
