<?php

namespace App\Livewire\Finance;

use App\Models\FinishedGood;
use App\Models\Product;
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
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate($this->perPage);

        $inventory = $products->map(function ($product) {
            // Get all finished goods for this product
            $finishedGoods = FinishedGood::where('product_id', $product->id)->get();

            // Sum quantities
            $totalFinishedGoods = $finishedGoods->sum('quantity');

            // Sum stock in/out from related fyaMovements
            $stockIn = 0;
            $stockOut = 0;

            foreach ($finishedGoods as $fg) {
                $stockIn += $fg->fyaMovements()->where('movement_type', 'in')->sum('quantity');
                $stockOut += $fg->fyaMovements()->where('movement_type', 'out')->sum('quantity');
            }

            // Current stock calculation
            $current = ($stockIn - $stockOut) + $totalFinishedGoods;

            return [
                'product' => $product,
                'stock_in' => $stockIn,
                'stock_out' => $stockOut,
                'finished_goods' => $totalFinishedGoods,
                'current' => $current,
            ];
        });

        return view('livewire.finance.inventory-report', [
            'products' => $products,
            'inventory' => $inventory,
        ]);
    }
}
