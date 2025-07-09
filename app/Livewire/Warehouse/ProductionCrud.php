<?php

namespace App\Livewire\Warehouse;

use App\Models\Product;
use Livewire\Component;
use App\Models\ScrapWaste;
use App\Models\ProductionLine;
use Livewire\Attributes\Layout;
use App\Models\MaterialStockOut;
use App\Models\MaterialStockOutLine;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class ProductionCrud extends Component
{
  use WithPagination;

  public $search = '';
  public $selectedLine = null;
  public $selectedProduct = null;
  public $dateFrom = '';
  public $dateTo = '';
  public $perPage = 10;

  protected $queryString = [
    'search' => ['except' => ''],
    'selectedLine' => ['except' => ''],
    'selectedProduct' => ['except' => ''],
    'dateFrom' => ['except' => ''],
    'dateTo' => ['except' => ''],
  ];

  public function updatingSearch()
  {
    $this->resetPage();
  }

  public function deleteProduction($id)
  {
    $line = MaterialStockOutLine::findOrFail($id);

    // Delete related records
    $line->scrapWastes()->delete();
    $line->delete();

    session()->flash('success', 'Production record deleted successfully!');
  }

  public function viewProduction($id)
  {
    return redirect()->route('warehouse.production.view', $id);
  }

  public function editProduction($id)
  {
    return redirect()->route('warehouse.production.edit', $id);
  }

  #[Layout('components.layouts.app')]
  public function render()
  {
    $query = MaterialStockOutLine::with([
      'materialStockOut.rawMaterial',
      'finishedGoods.product',
      'scrapWastes',
      'productionLine'
    ]);

    // Apply filters
    if ($this->search) {
      $query->whereHas('materialStockOut', function ($q) {
        $q->where('reference_number', 'like', '%' . $this->search . '%');
      })->orWhereHas('productionLine', function ($q) {
        $q->where('name', 'like', '%' . $this->search . '%');
      });
    }

    if ($this->selectedLine) {
      $query->where('production_line_id', $this->selectedLine);
    }

    if ($this->selectedProduct) {
      $query->whereHas('materialStockOut', function ($q) {
        $q->where('product_id', $this->selectedProduct);
      });
    }

    if ($this->dateFrom) {
      $query->whereDate('created_at', '>=', $this->dateFrom);
    }

    if ($this->dateTo) {
      $query->whereDate('created_at', '<=', $this->dateTo);
    }

    $productions = $query->orderBy('created_at', 'desc')
      ->paginate($this->perPage);

    return view('livewire.warehouse.production-crud', [
      'productions' => $productions,
      'productionLines' => ProductionLine::all(),
      'products' => Product::all(),
    ]);
  }
}
