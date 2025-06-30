<?php

namespace App\Livewire\Operations;

use App\Models\FinishedGood;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ProductionReport extends Component
{
  use WithPagination;

  public function render()
  {
    $user = Auth::user();

    // if (!$user->hasRole(['admin', 'operations', 'plant_manager'])) {
    //   abort(403, 'Unauthorized access to production reports.');
    // }

    $finishedGoods = FinishedGood::with(['product', 'customer', 'producedBy'])
      ->latest()
      ->paginate(15);

    return view('livewire.operations.production-report', [
      'finishedGoods' => $finishedGoods,
    ]);
  }
}