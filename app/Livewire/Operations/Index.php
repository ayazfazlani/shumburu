<?php

namespace App\Livewire\Operations;

use App\Livewire\Operations\DowntimeRecord as OperationsDowntimeRecord;
use App\Models\MaterialStockOut;
use App\Models\ScrapWaste;
use App\Models\FinishedGood;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
  use WithPagination;

  public function render()
  {
    $user = Auth::user();

    // Check if user has operations access
    // if (!$user->hasRole(['Admin', 'operations', 'plant_manager'])) {
    //   abort(403, 'Unauthorized access to operations.');
    // }

    $materialUsage = MaterialStockOut::with(['rawMaterial', 'issuedBy'])
      ->where('status', 'material_on_process')
      ->latest()
      ->paginate(10);

    $wasteRecords = ScrapWaste::with(['rawMaterial', 'recordedBy'])
      ->latest()
      ->paginate(10);

    // $downtimeRecords = OperationsDowntimeRecord::with('recordedBy')
    //   ->latest()
    //   ->paginate(10);
    $downtimeRecords = [];
    $finishedGoods = FinishedGood::with(['product', 'customer', 'producedBy'])
      ->latest()
      ->paginate(10);

    return view('livewire.operations.index', [
      'materialUsage' => $materialUsage,
      'wasteRecords' => $wasteRecords,
      'downtimeRecords' => $downtimeRecords,
      'finishedGoods' => $finishedGoods,
    ]);
  }
}