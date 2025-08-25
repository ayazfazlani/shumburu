<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesCrud extends Component
{
  use WithPagination;

  public $search = '';
  public $perPage = 5;

  public $showModal = false;
  public $isEdit = false;
  public $roleId = null;
  public $name = '';
  public $selectedPermissions = [];

  public $showDeleteModal = false;
  public $deleteId = null;

  protected $rules = [
    'name' => 'required|string|max:255',
    'selectedPermissions' => 'array',
  ];

  public function render()
  {
    $roles = Role::with('permissions')
      ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
      ->orderBy('id', 'asc')
      ->paginate($this->perPage);
    $permissions = Permission::all();
    return view('livewire.admin.roles-crud', [
      'roles' => $roles,
      'permissions' => $permissions,
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
    $role = Role::with('permissions')->findOrFail($id);
    $this->roleId = $role->id;
    $this->name = $role->name;
    $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
    $this->isEdit = true;
    $this->showModal = true;
  }

  public function saveRole()
  {
    $this->validate();
    if ($this->isEdit && $this->roleId) {
      $role = Role::findOrFail($this->roleId);
      $role->update(['name' => $this->name]);
    } else {
      $role = Role::create(['name' => $this->name]);
    }
    // dd($this->selectedPermissions);
    $role->syncPermissions($this->selectedPermissions);
    $this->showModal = false;
    $this->resetForm();
    session()->flash('message', $this->isEdit ? 'Role updated.' : 'Role created.');
  }

  public function confirmDelete($id)
  {
    $this->deleteId = $id;
    $this->showDeleteModal = true;
  }

  public function deleteRole()
  {
    $role = Role::findOrFail($this->deleteId);
    $role->delete();
    $this->showDeleteModal = false;
    $this->deleteId = null;
    session()->flash('message', 'Role deleted.');
  }

  public function resetForm()
  {
    $this->roleId = null;
    $this->name = '';
    $this->selectedPermissions = [];
  }

  public function updatingSearch()
  {
    $this->resetPage();
  }
  public function updatingPerPage()
  {
    $this->resetPage();
  }
}
