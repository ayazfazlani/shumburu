<?php

namespace App\Livewire\Warehouse;

use App\Models\MaterialStockIn;
use App\Models\MaterialStockOut;
use App\Models\FinishedGood;
use App\Models\ScrapWaste;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
  use WithPagination;

  public function render()
  {
    $user = Auth::user();

    // Check if user has warehouse access
    // if (!$user->hasRole(['admin', 'warehouse_manager', 'visitor'])) {
    //   abort(403, 'Unauthorized access to warehouse.');
    // }

    $stockIns = MaterialStockIn::with(['rawMaterial', 'receivedBy'])
      ->latest()
      ->paginate(10);

    $stockOuts = MaterialStockOut::with(['rawMaterial', 'issuedBy'])
      ->latest()
      ->paginate(10);

    $finishedGoods = FinishedGood::with(['product', 'customer', 'producedBy'])
      ->latest()
      ->paginate(10);

    $scrapWaste = ScrapWaste::with(['materialStockOutLine.materialStockOut', 'materialStockOutLine.productionLine', 'recordedBy'])
      ->latest()
      ->paginate(10);

    return view('livewire.warehouse.index', [
      'stockIns' => $stockIns,
      'stockOuts' => $stockOuts,
      'finishedGoods' => $finishedGoods,
      'scrapWaste' => $scrapWaste,
    ]);
  }
}
