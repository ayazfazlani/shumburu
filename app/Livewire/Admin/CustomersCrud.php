<?php

namespace App\Livewire\Admin;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class CustomersCrud extends Component
{
    use WithPagination;

    public function mount()
    {
        abort_unless(auth()->user()->can('admin.customers-crud'), 403);
    }

    public $search = '';
    public $perPage = 10;

    public $showModal = false;
    public $isEdit = false;
    public $customerId = null;
    public $code = '';
    public $name = '';
    public $contact_person = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $is_active = true;

    public $showDeleteModal = false;
    public $deleteId = null;

    protected function rules()
    {
        $uniqueEmail = $this->isEdit && $this->customerId
            ? 'unique:customers,email,' . $this->customerId
            : 'unique:customers,email';
        $uniqueCode = $this->isEdit && $this->customerId
            ? 'unique:customers,code,' . $this->customerId
            : 'unique:customers,code';
        return [
            'code' => ['required', 'string', 'max:255', $uniqueCode],
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => ['required', 'email', 'max:255', $uniqueEmail],
            'phone' => 'nullable|string|max:32',
            'address' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    public function render()
    {
        $customers = Customer::when($this->search, function ($q) {
            $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
                ->orWhere('phone', 'like', "%{$this->search}%");
        })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);
        return view('livewire.admin.customers-crud', [
            'customers' => $customers,
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
        $customer = Customer::findOrFail($id);
        $this->customerId = $customer->id;
        $this->code = $customer->code;
        $this->name = $customer->name;
        $this->contact_person = $customer->contact_person;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
        $this->address = $customer->address;
        $this->is_active = $customer->is_active;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function saveCustomer()
    {
        $this->validate();
        $data = [
            'code' => $this->code,
            'name' => $this->name,
            'contact_person' => $this->contact_person,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'is_active' => $this->is_active,
        ];

        if ($this->isEdit && $this->customerId) {
            Customer::findOrFail($this->customerId)->update($data);
            session()->flash('message', 'Customer updated successfully.');
        } else {
            Customer::create($data);
            session()->flash('message', 'Customer created successfully.');
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

    public function deleteCustomer()
    {
        if ($this->deleteId) {
            $customer = Customer::find($this->deleteId);
            if ($customer) {
                $customer->delete();
                session()->flash('message', 'Customer deleted successfully.');
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
        $this->customerId = null;
        $this->code = '';
        $this->name = '';
        $this->contact_person = '';
        $this->email = '';
        $this->phone = '';
        $this->address = '';
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