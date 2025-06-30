<?php

namespace App\Livewire\Sales;

use App\Models\ProductionOrder;
use App\Models\Delivery;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
  use WithPagination;

  public function render()
  {
    $user = Auth::user();

    // Check if user has sales access
    if (!$user->hasRole(['Admin', 'Sales'])) {
      abort(403, 'Unauthorized access to sales.');
    }

    $productionOrders = ProductionOrder::with(['customer', 'product', 'requestedBy', 'approvedBy', 'plantManager'])
      ->latest()
      ->paginate(10);

    $deliveries = Delivery::with(['customer', 'product', 'productionOrder', 'deliveredBy'])
      ->latest()
      ->paginate(10);

    $payments = Payment::with(['customer', 'delivery', 'recordedBy'])
      ->latest()
      ->paginate(10);

    return view('livewire.sales.index', [
      'productionOrders' => $productionOrders,
      'deliveries' => $deliveries,
      'payments' => $payments,
    ]);
  }
}