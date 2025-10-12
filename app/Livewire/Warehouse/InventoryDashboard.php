<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use App\Models\Product;
use App\Models\FinishedGood;
use App\Models\RawMaterial;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class InventoryDashboard extends Component
{
    use WithPagination;

    public $search = '';
    public $filter_type = 'all'; // 'all', 'raw_materials', 'finished_goods'
    public $filter_status = 'all'; // 'all', 'in_stock', 'low_stock', 'out_of_stock'

    public function render()
    {
        $rawMaterials = $this->getRawMaterials();
        $finishedGoods = $this->getFinishedGoods();
        $inventorySummary = $this->getInventorySummary();

        return view('livewire.warehouse.inventory-dashboard', [
            'rawMaterials' => $rawMaterials,
            'finishedGoods' => $finishedGoods,
            'inventorySummary' => $inventorySummary,
        ]);
    }

    private function getRawMaterials()
    {
        $query = RawMaterial::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->filter_type === 'raw_materials' || $this->filter_type === 'all') {
            return $query->orderBy('name')->get();
        }

        return collect();
    }

    private function getFinishedGoods()
    {
        $query = FinishedGood::with(['product', 'customer'])
            ->where('purpose', 'for_stock');

        if ($this->search) {
            $query->whereHas('product', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filter_type === 'finished_goods' || $this->filter_type === 'all') {
            return $query->orderBy('created_at', 'desc')->get();
        }

        return collect();
    }

    private function getInventorySummary()
    {
        $totalRawMaterials = RawMaterial::count();
        $totalFinishedGoods = FinishedGood::where('purpose', 'for_stock')->count();
        
        // Calculate total value
        $rawMaterialValue = RawMaterial::sum(DB::raw('quantity * unit_price'));
        $finishedGoodsValue = FinishedGood::where('purpose', 'for_stock')
            ->sum(DB::raw('quantity * (SELECT unit_price FROM products WHERE products.id = finished_goods.product_id)'));

        // Low stock items (less than 100kg for raw materials, less than 10 units for finished goods)
        $lowStockRawMaterials = RawMaterial::where('quantity', '<', 100)->count();
        $lowStockFinishedGoods = FinishedGood::where('purpose', 'for_stock')
            ->where('quantity', '<', 10)->count();

        return [
            'total_raw_materials' => $totalRawMaterials,
            'total_finished_goods' => $totalFinishedGoods,
            'raw_material_value' => $rawMaterialValue,
            'finished_goods_value' => $finishedGoodsValue,
            'low_stock_raw_materials' => $lowStockRawMaterials,
            'low_stock_finished_goods' => $lowStockFinishedGoods,
            'total_value' => $rawMaterialValue + $finishedGoodsValue,
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    // Get stock balance for a specific product
    public function getStockBalance($productId, $size = null)
    {
        $query = FinishedGood::where('product_id', $productId)
            ->where('purpose', 'for_stock');

        if ($size) {
            $query->where('size', $size);
        }

        return $query->sum('quantity');
    }

    // Get stock by size for a product
    public function getStockBySize($productId)
    {
        return FinishedGood::where('product_id', $productId)
            ->where('purpose', 'for_stock')
            ->select('size', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('size')
            ->get()
            ->pluck('total_quantity', 'size');
    }
}
