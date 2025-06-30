<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

class PermissionsCrud extends Component
{
  use WithPagination;

  public $search = '';
  public $perPage = 10;

  public $showModal = false;
  public $isEdit = false;
  public $permissionId = null;
  public $name = '';

  public $showDeleteModal = false;
  public $deleteId = null;

  protected $rules = [
    'name' => 'required|string|max:255',
  ];

  public function render()
  {
    $permissions = Permission::when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
      ->orderBy('id', 'desc')
      ->paginate($this->perPage);
    return view('livewire.admin.permissions-crud', [
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
    $permission = Permission::findOrFail($id);
    $this->permissionId = $permission->id;
    $this->name = $permission->name;
    $this->isEdit = true;
    $this->showModal = true;
  }

  public function savePermission()
  {
    $this->validate();
    if ($this->isEdit && $this->permissionId) {
      $permission = Permission::findOrFail($this->permissionId);
      $permission->update(['name' => $this->name]);
    } else {
      Permission::create(['name' => $this->name]);
    }
    $this->showModal = false;
    $this->resetForm();
    session()->flash('message', $this->isEdit ? 'Permission updated.' : 'Permission created.');
  }

  public function confirmDelete($id)
  {
    $this->deleteId = $id;
    $this->showDeleteModal = true;
  }

  public function deletePermission()
  {
    $permission = Permission::findOrFail($this->deleteId);
    $permission->delete();
    $this->showDeleteModal = false;
    $this->deleteId = null;
    session()->flash('message', 'Permission deleted.');
  }

  public function resetForm()
  {
    $this->permissionId = null;
    $this->name = '';
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
