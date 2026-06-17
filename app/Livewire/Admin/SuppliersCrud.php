<?php

namespace App\Livewire\Admin;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class SuppliersCrud extends Component
{
    use WithPagination;

    public function mount()
    {
        abort_unless(auth()->user()->can('admin.suppliers'), 403);
    }

    #[Layout('components.layouts.app')]

    public $search = '';
    public $perPage = 10;

    public $showModal = false;
    public $isEdit = false;
    public $supplierId = null;
    public $code = '';
    public $name = '';
    public $contact_person = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $payment_terms = '';
    public $is_active = true;

    public $showDeleteModal = false;
    public $deleteId = null;

    protected function rules()
    {
        $uniqueCode = $this->isEdit && $this->supplierId
            ? 'unique:suppliers,code,' . $this->supplierId
            : 'unique:suppliers,code';
        return [
            'code'          => ['required', 'string', 'max:50', $uniqueCode],
            'name'          => 'required|string|max:255',
            'contact_person'=> 'nullable|string|max:255',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'nullable|string|max:32',
            'address'       => 'nullable|string|max:500',
            'payment_terms' => 'nullable|string|max:100',
            'is_active'     => 'boolean',
        ];
    }

    public function render()
    {
        $suppliers = Supplier::when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.suppliers-crud', ['suppliers' => $suppliers]);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $supplier = Supplier::findOrFail($id);
        $this->supplierId     = $supplier->id;
        $this->code           = $supplier->code;
        $this->name           = $supplier->name;
        $this->contact_person = $supplier->contact_person;
        $this->email          = $supplier->email;
        $this->phone          = $supplier->phone;
        $this->address        = $supplier->address;
        $this->payment_terms  = $supplier->payment_terms;
        $this->is_active      = $supplier->is_active;
        $this->isEdit  = true;
        $this->showModal = true;
    }

    public function saveSupplier()
    {
        $this->validate();
        $data = [
            'code'          => $this->code,
            'name'          => $this->name,
            'contact_person'=> $this->contact_person,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'address'       => $this->address,
            'payment_terms' => $this->payment_terms,
            'is_active'     => $this->is_active,
        ];

        if ($this->isEdit && $this->supplierId) {
            Supplier::findOrFail($this->supplierId)->update($data);
        } else {
            Supplier::create($data);
        }
        $this->showModal = false;
        $this->resetForm();
        session()->flash('message', $this->isEdit ? 'Supplier updated.' : 'Supplier created.');
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteSupplier()
    {
        Supplier::findOrFail($this->deleteId)->delete();
        $this->showDeleteModal = false;
        $this->deleteId = null;
        session()->flash('message', 'Supplier deleted.');
    }

    public function resetForm()
    {
        $this->supplierId     = null;
        $this->code           = '';
        $this->name           = '';
        $this->contact_person = '';
        $this->email          = '';
        $this->phone          = '';
        $this->address        = '';
        $this->payment_terms  = '';
        $this->is_active      = true;
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }
}
