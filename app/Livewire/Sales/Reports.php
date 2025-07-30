<?php

namespace App\Livewire\Sales;

use App\Models\ProductionOrder;
use App\Models\Delivery;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Reports extends Component
{
  use WithPagination;

  public function render()
  {
    $user = Auth::user();

    // if (!$user->hasRole(['admin', 'sales'])) {
    //   abort(403, 'Unauthorized access to sales reports.');
    // }

    $productionOrders = ProductionOrder::with(['customer',
    // , 'product',
     'requestedBy'])
      ->latest()
      ->paginate(15);

    $deliveries = Delivery::with(['customer', 
    // 'product',
     'productionOrder'])
      ->latest()
      ->paginate(15);

    $payments = Payment::with(['customer', 'order'])
      ->latest()
      ->paginate(15);

    return view('livewire.sales.reports', [
      'productionOrders' => $productionOrders,
      'deliveries' => $deliveries,
      'payments' => $payments,
    ]);
  }
}
