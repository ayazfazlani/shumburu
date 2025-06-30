<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
  use HasFactory;

  protected $fillable = [
    'code',
    'name',
    'contact_person',
    'phone',
    'email',
    'address',
    'is_active',
  ];

  protected $casts = [
    'is_active' => 'boolean',
  ];

  public function productionOrders(): HasMany
  {
    return $this->hasMany(ProductionOrder::class);
  }

  public function deliveries(): HasMany
  {
    return $this->hasMany(Delivery::class);
  }

  public function payments(): HasMany
  {
    return $this->hasMany(Payment::class);
  }

  public function finishedGoods(): HasMany
  {
    return $this->hasMany(FinishedGood::class);
  }

  // Access control methods
  public function getDisplayNameAttribute(): string
  {
    $user = auth()->user();

    // Only admins, sales team, and finance can see full customer names
    if ($user && ($user->hasRole(['admin', 'sales', 'finance']) || $user->hasPermissionTo('view customer names'))) {
      return $this->name;
    }

    return $this->code;
  }
}
