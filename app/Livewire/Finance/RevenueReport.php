<?php

namespace App\Livewire\Finance;

use App\Models\Payment;
use App\Models\Delivery;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class RevenueReport extends Component
{
  use WithPagination;

  public function render()
  {
    $user = Auth::user();

    // if (!$user->hasRole(['admin', 'finance'])) {
    //   abort(403, 'Unauthorized access to revenue reports.');
    // }

    $payments = Payment::with(['customer', 'productionOrder'])
      ->latest()
      ->paginate(15);

    $deliveries = Delivery::with(['customer', 'product', 'productionOrder'])
      ->latest()
      ->paginate(15);

    return view('livewire.finance.revenue-report', [
      'payments' => $payments,
      'deliveries' => $deliveries,
    ]);
  }
}
