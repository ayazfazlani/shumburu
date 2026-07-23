<?php

namespace App\Livewire\Admin;

use App\Models\RawMaterial;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class RawMaterialsCrud extends Component
{
    use WithPagination;

    // ─── Mount ───
    public function mount()
    {
        abort_unless(auth()->user()->can('admin.raw-materials-crud'), 403);
        $this->resetForm();
    }

    // ─── Search & Pagination ───
    public $search = '';
    public $perPage = 10;

    // ─── Modal Controls ───
    public $showModal = false;
    public $isEdit = false;
    public $showDeleteModal = false;
    public $deleteId = null;

    // ─── Form Fields ───
    public $rawMaterialId = null;
    public $name = '';
    public $code = '';
    public $description = '';
    public $unit = 'kg';
    public $quantity = 0;
    public $is_active = true;

    // ─── Validation Rules ───
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('raw_materials', 'code')->ignore($this->rawMaterialId),
            ],
            'description' => 'nullable|string',
            'unit' => 'required|string|max:32',
            'quantity' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ];
    }

    // ─── Custom Validation Messages ───
    protected function messages()
    {
        return [
            'name.required' => 'Please enter the raw material name.',
            'code.required' => 'Please enter the raw material code.',
            'code.unique' => 'This code already exists. Please use a different code.',
            'unit.required' => 'Please enter a unit of measurement.',
        ];
    }

    // ─── Render ───
    public function render()
    {
        $rawMaterials = RawMaterial::when($this->search, function ($q) {
            $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('code', 'like', "%{$this->search}%");
        })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.raw-materials-crud', [
            'rawMaterials' => $rawMaterials,
        ]);
    }

    // ─── Create ───
    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
        $this->code = $this->generateCode();
    }

    // ─── Edit ───
    public function openEditModal($id)
    {
        $rawMaterial = RawMaterial::findOrFail($id);
        $this->rawMaterialId = $rawMaterial->id;
        $this->name = $rawMaterial->name;
        $this->code = $rawMaterial->code;
        $this->description = $rawMaterial->description;
        $this->unit = $rawMaterial->unit;
        $this->quantity = $rawMaterial->quantity;
        $this->is_active = $rawMaterial->is_active;
        $this->isEdit = true;
        $this->showModal = true;
    }

    // ─── Save ───
    public function saveRawMaterial()
    {
        $validated = $this->validate();

        if ($this->isEdit && $this->rawMaterialId) {
            $rawMaterial = RawMaterial::findOrFail($this->rawMaterialId);
            $rawMaterial->update($validated);
            session()->flash('message', 'Raw material updated successfully.');
        } else {
            RawMaterial::create($validated);
            session()->flash('message', 'Raw material created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
        $this->resetPage();
    }

    // ─── Delete ───
    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteRawMaterial()
    {
        if ($this->deleteId) {
            $rawMaterial = RawMaterial::find($this->deleteId);
            if ($rawMaterial) {
                $rawMaterial->delete();
                session()->flash('message', 'Raw material deleted successfully.');
            }
            $this->deleteId = null;
        }
        $this->showDeleteModal = false;
        $this->resetPage();
    }

    // ─── Close Modals ───
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

    // ─── Reset Form ───
    public function resetForm()
    {
        $this->rawMaterialId = null;
        $this->name = '';
        $this->code = '';
        $this->description = '';
        $this->unit = 'kg';
        $this->quantity = 0;
        $this->is_active = true;
        $this->isEdit = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    // ─── Generate Code ───
    private function generateCode()
    {
        $last = RawMaterial::latest('id')->first();
        $number = $last ? intval(substr($last->code, 3)) + 1 : 1;
        return 'RM-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    // ─── Live Updates ───
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
}
