<?php

namespace App\Livewire\Sales;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductionOrder;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class OrderItems extends Component
{
    use WithPagination;

    public $productionOrderId;
    public $productionOrder;
    
    // Form fields
    public $orderItemId;
    public $productId;
    public $quantity;
    public $unit = 'meter';
    public $unitPrice;
    public $totalPrice;
    
    // Search and filters
    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    // Modal states
    public $showModal = false;
    public $isEditing = false;
    public $showDeleteModal = false;
    public $itemToDelete = null;

    protected $rules = [
        'productId' => 'required|exists:products,id',
        'quantity' => 'required|numeric|min:0.01',
        'unit' => 'required|string|max:20',
        'unitPrice' => 'required|numeric|min:0.01',
        'totalPrice' => 'required|numeric|min:0.01',
    ];

    public function mount($productionOrderId = null): void
    {
        $this->productionOrderId = $productionOrderId;
        if ($this->productionOrderId) {
            $this->productionOrder = ProductionOrder::with(['customer'])->findOrFail($this->productionOrderId);
        }
    }

    #[Layout('components.layouts.app')]
    public function render(): View
    {
        $orderItems = OrderItem::with(['product'])
            ->where('production_order_id', $this->productionOrderId)
            ->when($this->search, function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.sales.order-items', [
            'orderItems' => $orderItems,
            'products' => $products,
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function edit($orderItemId)
    {
        $orderItem = OrderItem::findOrFail($orderItemId);
        
        $this->orderItemId = $orderItem->id;
        $this->productId = $orderItem->product_id;
        $this->quantity = $orderItem->quantity;
        $this->unit = $orderItem->unit;
        $this->unitPrice = $orderItem->unit_price;
        $this->totalPrice = $orderItem->total_price;
        
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        // Calculate total price
        $this->totalPrice = $this->quantity * $this->unitPrice;

        if ($this->isEditing) {
            $orderItem = OrderItem::findOrFail($this->orderItemId);
            $orderItem->update([
                'product_id' => $this->productId,
                'quantity' => $this->quantity,
                'unit' => $this->unit,
                'unit_price' => $this->unitPrice,
                'total_price' => $this->totalPrice,
            ]);
            
            session()->flash('message', 'Order item updated successfully.');
        } else {
            OrderItem::create([
                'production_order_id' => $this->productionOrderId,
                'product_id' => $this->productId,
                'quantity' => $this->quantity,
                'unit' => $this->unit,
                'unit_price' => $this->unitPrice,
                'total_price' => $this->totalPrice,
            ]);
            
            session()->flash('message', 'Order item added successfully.');
        }

        $this->closeModal();
        $this->resetForm();
    }

    public function confirmDelete($orderItemId)
    {
        $this->itemToDelete = $orderItemId;
        $this->showDeleteModal = true;
    }

    public function deleteOrderItem()
    {
        if ($this->itemToDelete) {
            $orderItem = OrderItem::findOrFail($this->itemToDelete);
            $orderItem->delete();
            
            session()->flash('message', 'Order item deleted successfully.');
            $this->showDeleteModal = false;
            $this->itemToDelete = null;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->orderItemId = null;
        $this->productId = '';
        $this->quantity = '';
        $this->unit = 'meter';
        $this->unitPrice = '';
        $this->totalPrice = '';
    }

    public function updatedQuantity()
    {
        $this->calculateTotalPrice();
    }

    public function updatedUnitPrice()
    {
        $this->calculateTotalPrice();
    }

    public function calculateTotalPrice()
    {
        if ($this->quantity && $this->unitPrice) {
            $this->totalPrice = $this->quantity * $this->unitPrice;
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
