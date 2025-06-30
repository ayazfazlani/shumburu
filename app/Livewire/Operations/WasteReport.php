<?php

namespace App\Livewire\Operations;

use App\Models\ScrapWaste;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class WasteReport extends Component
{
  use WithPagination;

  public function render()
  {
    $user = Auth::user();

    // if (!$user->hasRole(['Admin', 'operations', 'plant_manager'])) {
    //   abort(403, 'Unauthorized access to waste reports.');
    // }

    $wasteRecords = ScrapWaste::with(['materialStockOutLine.materialStockOut', 'materialStockOutLine.productionLine', 'recordedBy'])
      ->latest()
      ->paginate(15);

    return view('livewire.operations.waste-report', [
      'wasteRecords' => $wasteRecords,
    ]);
  }
}