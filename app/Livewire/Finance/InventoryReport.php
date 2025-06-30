<?php

namespace App\Livewire\Finance;

use App\Models\Product;
use App\Models\MaterialStockIn;
use App\Models\MaterialStockOut;
use App\Models\FinishedGood;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryReport extends Component
{
  use WithPagination;

  public $perPage = 15;
  public $search = '';

  public function render()
  {
    $products = Product::query()
      ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
      ->orderBy('name')
      ->paginate($this->perPage);

    $inventory = $products->map(function ($product) {
      $stockIn = $product->materialStockIns()->sum('quantity');
      $stockOut = $product->materialStockOuts()->sum('quantity');
      $finishedGoods = FinishedGood::where('product_id', $product->id)->sum('quantity');
      $current = $stockIn - $stockOut + $finishedGoods;
      return [
        'product' => $product,
        'stock_in' => $stockIn,
        'stock_out' => $stockOut,
        'finished_goods' => $finishedGoods,
        'current' => $current,
      ];
    });

    return view('livewire.finance.inventory-report', [
      'products' => $products,
      'inventory' => $inventory,
    ]);
  }
}