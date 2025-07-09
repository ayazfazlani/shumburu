<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\MaterialStockOutLine;

use function Pest\Laravel\json;

class ViewProduction extends Component
{
  public $productionId;
  public  $production = [];

  public function mount($id)
  {
    $this->productionId = $id;
    $this->production = MaterialStockOutLine::with([
      'materialStockOut.rawMaterial',
      'finishedGoods.product',
      'scrapWastes',
      'productionLine',
    ])->findOrFail($id);
    // return dd($this->production)->tojson();
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
    return view('livewire.warehouse.view-production', [
      'production' => $this->production,
    ]);
  }
}
