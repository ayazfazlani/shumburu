<?php

namespace App\Livewire\Warehouse;

use App\Models\FgStock;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class StockOverview extends Component
{
    use WithPagination;

    public $search = '';

    #[Layout('components.layouts.app')]
    public function render()
    {
        $stocks = FgStock::with('product')
            ->when($this->search, function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })->orWhere('batch_number', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(20);

        return view('livewire.warehouse.stock-overview', [
            'stocks' => $stocks,
        ]);
    }
}
