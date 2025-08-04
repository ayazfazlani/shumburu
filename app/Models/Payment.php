<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
  use HasFactory;

  protected $fillable = [
    'delivery_id',
    'customer_id',
    'amount',
    'payment_method',
    'bank_slip_reference',
    'proforma_invoice_number',
    'payment_date',
    'recorded_by',
    'notes',
    'production_order_id',
  ];
  protected $guarded = [];
  protected $casts = [
    'amount' => 'decimal:2',
    'payment_date' => 'date',
  ];

  public function productionOrder(): BelongsTo
  {
    return $this->belongsTo(ProductionOrder::class,'production_order_id');
  }

  public function customer(): BelongsTo
  {
    return $this->belongsTo(Customer::class);
  }

  public function recordedBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'recorded_by');
  }

 
}
