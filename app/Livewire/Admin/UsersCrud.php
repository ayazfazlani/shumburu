<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsersCrud extends Component
{
  use WithPagination;

  public $search = '';
  public $perPage = 10;

  public $showModal = false;
  public $isEdit = false;
  public $userId = null;
  public $name = '';
  public $email = '';
  public $password = '';
  public $password_confirmation = '';
  public $selectedRoles = [];

  public $showDeleteModal = false;
  public $deleteId = null;

  protected function rules()
  {
    $rules = [
      'name' => 'required|string|max:255',
      'email' => 'required|email|max:255|unique:users,email' . ($this->isEdit && $this->userId ? ',' . $this->userId : ''),
      'selectedRoles' => 'nullable',
    ];
    if (!$this->isEdit) {
      // $rules['password'] = 'required|string|min:6|confirmed';
    } elseif ($this->password) {
      $rules['password'] = 'nullable|string|min:6|confirmed';
    }
    return $rules;
  }

  public function render()
  {
    $users = User::with('roles')
      ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
      ->orderBy('id', 'desc')
      ->paginate($this->perPage);
    $roles = Role::all();
    return view('livewire.admin.users-crud', [
      'users' => $users,
      'roles' => $roles,
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
    $user = User::with('roles')->findOrFail($id);
    $this->userId = $user->id;
    $this->name = $user->name;
    $this->email = $user->email;
    $this->selectedRoles = $user->roles->pluck('name')->toArray();
    $this->isEdit = true;
    $this->showModal = true;
  }

  public function saveUser()
  {
    if ($this->isEdit && $this->userId) {
      $user = User::findOrFail($this->userId);
      session()->flash('message', 'User updated successfully.');
    } else {
      $user = User::create([
        'name' => $this->name,
        'email' => $this->email,
        'password' => Hash::make($this->password),
      ]);
      session()->flash('message', 'User created successfully.');
    }
    $user->syncRoles($this->selectedRoles);
    $this->showModal = false;
    $this->resetForm();
    $this->resetPage();
  }

  public function confirmDelete($id)
  {
    $this->deleteId = $id;
    $this->showDeleteModal = true;
  }

  public function deleteUser()
  {
    if ($this->deleteId) {
      $user = User::find($this->deleteId);
      if ($user) {
        $user->delete();
        session()->flash('message', 'User deleted successfully.');
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
    $this->userId = null;
    $this->name = '';
    $this->email = '';
    $this->password = '';
    $this->password_confirmation = '';
    $this->selectedRoles = [];
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
