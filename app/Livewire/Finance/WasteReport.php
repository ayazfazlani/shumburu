<?php

namespace App\Livewire\Finance;

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

    if (!$user->hasRole(['admin', 'finance'])) {
      abort(403, 'Unauthorized access to waste reports.');
    }

    $scrapWaste = ScrapWaste::with(['rawMaterial', 'recordedBy'])
      ->latest()
      ->paginate(15);

    return view('livewire.finance.waste-report', [
      'scrapWaste' => $scrapWaste,
    ]);
  }
}
