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
    // dd($user);
    $this->userId = $user->id;
    $this->name = $user->name;
    $this->email = $user->email;
    $this->selectedRoles = $user->roles->pluck('name')->toArray();
    // dd($this->selectedRoles);
    $this->isEdit = true;
    $this->showModal = true;
    // Do not allow editing name/email/password in edit mode
  }

  public function saveUser()
  {
    // dd();
    // $this->validate();

    if ($this->isEdit && $this->userId) {
      $user = User::findOrFail($this->userId);
      // Only sync roles, do not update name/email/password
    } else {
      $user = User::create([
        'name' => $this->name,
        'email' => $this->email,
        'password' => Hash::make($this->password),
      ]);
    }
    $user->syncRoles($this->selectedRoles);
    $this->showModal = false;
    $this->resetForm();
    session()->flash('message', $this->isEdit ? 'User updated.' : 'User created.');
  }

  public function confirmDelete($id)
  {
    $this->deleteId = $id;
    $this->showDeleteModal = true;
  }

  public function deleteUser()
  {
    $user = User::findOrFail($this->deleteId);
    $user->delete();
    $this->showDeleteModal = false;
    $this->deleteId = null;
    session()->flash('message', 'User deleted.');
  }

  public function resetForm()
  {
    $this->userId = null;
    $this->name = '';
    $this->email = '';
    $this->password = '';
    $this->password_confirmation = '';
    $this->selectedRoles = [];
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
