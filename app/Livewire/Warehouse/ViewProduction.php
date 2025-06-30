<?php

namespace App\Livewire\Warehouse;

use App\Models\MaterialStockOutLine;
use Livewire\Component;
use Livewire\Attributes\Layout;

class ViewProduction extends Component
{
  public MaterialStockOutLine $production;
  public $productionId;

  public function mount($id)
  {
    $this->productionId = $id;
    $this->production = MaterialStockOutLine::with([
      'materialStockOut.product',
      'productionLine',
      'productionLengths',
      'scrapWastes'
    ])->findOrFail($id);
  }

  public function goBack()
  {
    return redirect()->route('warehouse.production.index');
  }

  public function edit()
  {
    return redirect()->route('warehouse.production.edit', $this->productionId);
  }

  #[Layout('components.layouts.app')]
  public function render()
  {
    return view('livewire.warehouse.view-production');
  }
}
