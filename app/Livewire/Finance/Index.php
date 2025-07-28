<?php

namespace App\Livewire\Finance;

use App\Models\FinishedGood;
use App\Models\MaterialStockIn;
use App\Models\ScrapWaste;
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

    // Check if user has finance access
    // if (!$user->hasRole(['admin', 'finance'])) {
    //   abort(403, 'Unauthorized access to finance.');
    // }

    $finishedGoods = FinishedGood::with(['product', 'customer', 'producedBy'])
      ->latest()
      ->paginate(10);

    $stockIns = MaterialStockIn::with(['rawMaterial', 'receivedBy'])
      ->latest()
      ->paginate(10);

    $scrapWaste = ScrapWaste::with(['rawMaterial', 'recordedBy'])
      ->latest()
      ->paginate(10);

    $deliveries = Delivery::with(['customer', 'product', 'productionOrder', 'deliveredBy'])
      ->latest()
      ->paginate(10);

    $payments = Payment::with(['customer', 'order', 'recordedBy'])
      ->latest()
      ->paginate(10);

    return view('livewire.finance.index', [
      'finishedGoods' => $finishedGoods,
      'stockIns' => $stockIns,
      'scrapWaste' => $scrapWaste,
      'deliveries' => $deliveries,
      'payments' => $payments,
    ]);
  }
}