<?php

namespace App\Models;

use App\Services\NotificationService;
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
    // 'product_id',
    // 'quantity',
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
    'requested_date' => 'date',
    'production_start_date' => 'date',
    'production_end_date' => 'date',
    'delivery_date' => 'date',
  ];

  protected static function boot()
  {
    parent::boot();

    // Trigger notifications when status changes
    static::updated(function ($productionOrder) {
      \Log::info("ProductionOrder updated event fired for order #{$productionOrder->order_number}");
      
      if ($productionOrder->wasChanged('status')) {
        $oldStatus = $productionOrder->getOriginal('status');
        $newStatus = $productionOrder->status;
        
        // Debug logging
        \Log::info("ProductionOrder status changed from '{$oldStatus}' to '{$newStatus}' for order #{$productionOrder->order_number}");
        
        // Send notification after the update
        $notificationService = app(NotificationService::class);
        $notificationService->notifyStatusChanged($productionOrder, $oldStatus, $newStatus, auth()->id() ?? null);
        
        \Log::info("Notification sent for status change from '{$oldStatus}' to '{$newStatus}'");
      } else {
        \Log::info("ProductionOrder updated but status did not change for order #{$productionOrder->order_number}");
      }
    });

    // Trigger notifications when a new order is created
    static::created(function ($productionOrder) {
      $notificationService = app(NotificationService::class);
      $notificationService->notifyOrderCreated($productionOrder);
    });
  }

  public function customer(): BelongsTo
  {
    return $this->belongsTo(Customer::class);
  }

  // public function product(): BelongsTo
  // {
  //   return $this->belongsTo(Product::class);
  // }

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

  public function deliveries(): HasMany
  {
    return $this->hasMany(Delivery::class);
  }

  public function orderItems(): HasMany
  {
    return $this->hasMany(OrderItem::class);
  }

  public function items(): HasMany
  {
    return $this->hasMany(OrderItem::class, 'production_order_id');
  }

  /**
   * Get the total price of all items in this order.
   */
  public function getTotalPriceAttribute(): float
  {
    return $this->items->sum('total_price');
  }

  /**
   * Get the total quantity of all items in this order.
   */
  public function getTotalQuantityAttribute(): float
  {
    return $this->items->sum('quantity');
  }

  /**
   * Get formatted total price.
   */
  public function getFormattedTotalPriceAttribute(): string
  {
    return number_format($this->total_price, 2);
  }
}
