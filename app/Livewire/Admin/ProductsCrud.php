<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductsCrud extends Component
{
    use WithPagination;

    public function mount()
    {
        abort_unless(auth()->user()->can('admin.products-crud'), 403);
    }

    public $search = '';

    public $perPage = 10;

    public $showModal = false;

    public $isEdit = false;

    public $productId = null;

    public $name = '';

    public $code = '';

    public $size = '';

    public $pn = '';

    public $WeightPerMeter = '';

    public $meter_length = '';

    public $description = '';

    public $is_active = true;

    public $showDeleteModal = false;

    public $deleteId = null;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'size' => 'required|string|max:255',
            'pn' => 'required|string|max:255',
            'WeightPerMeter' => 'required|numeric|min:0',
            'meter_length' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function render()
    {
        $products = Product::when($this->search, function ($q) {
            $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('code', 'like', "%{$this->search}%");
        })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.products-crud', [
            'products' => $products,
        ]);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $product = Product::findOrFail($id);
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->code = $product->code;
        $this->size = $product->size;
        $this->pn = $product->pn;
        $this->WeightPerMeter = $product->weight_per_meter;
        $this->meter_length = $product->meter_length;
        $this->description = $product->description;
        $this->is_active = $product->is_active;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function saveProduct()
    {
        $this->validate();
        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'size' => $this->size,
            'pn' => $this->pn,
            'weight_per_meter' => $this->WeightPerMeter,
            'meter_length' => $this->meter_length,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        if ($this->isEdit && $this->productId) {
            Product::findOrFail($this->productId)->update($data);
            session()->flash('message', 'Product updated successfully.');
        } else {
            Product::create($data);
            session()->flash('message', 'Product created successfully.');
        }
        $this->showModal = false;
        $this->resetForm();
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteProduct()
    {
        if ($this->deleteId) {
            $product = Product::find($this->deleteId);
            if ($product) {
                $product->delete();
                session()->flash('message', 'Product deleted successfully.');
            }
            $this->deleteId = null;
        }
        $this->showDeleteModal = false;
        $this->resetPage();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function resetForm()
    {
        $this->productId = null;
        $this->name = '';
        $this->code = '';
        $this->size = '';
        $this->pn = '';
        $this->WeightPerMeter = '';
        $this->meter_length = '';
        $this->description = '';
        $this->is_active = true;
        $this->isEdit = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }
}
