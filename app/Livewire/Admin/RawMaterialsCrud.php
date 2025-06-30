<?php

namespace App\Livewire\Admin;

use App\Models\RawMaterial;
use Livewire\Component;
use Livewire\WithPagination;

class RawMaterialsCrud extends Component
{
  use WithPagination;

  public $search = '';
  public $perPage = 10;

  public $showModal = false;
  public $isEdit = false;
  public $rawMaterialId = null;
  public $name = '';
  public $code = '';
  public $description = '';
  public $unit = 'kg';
  public $is_active = true;

  public $showDeleteModal = false;
  public $deleteId = null;

  protected function rules()
  {
    $uniqueCode = $this->isEdit && $this->rawMaterialId ? ',code,' . $this->rawMaterialId : '';
    return [
      'name' => 'required|string|max:255',
      'code' => 'required|string|max:255|unique:raw_materials,code' . $uniqueCode,
      'description' => 'nullable|string',
      'unit' => 'required|string|max:32',
      'is_active' => 'boolean',
    ];
  }

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

  public function openCreateModal()
  {
    $this->resetForm();
    $this->isEdit = false;
    $this->showModal = true;
  }

  public function openEditModal($id)
  {
    $rawMaterial = RawMaterial::findOrFail($id);
    $this->rawMaterialId = $rawMaterial->id;
    $this->name = $rawMaterial->name;
    $this->code = $rawMaterial->code;
    $this->description = $rawMaterial->description;
    $this->unit = $rawMaterial->unit;
    $this->is_active = $rawMaterial->is_active;
    $this->isEdit = true;
    $this->showModal = true;
  }

  public function saveRawMaterial()
  {
    $this->validate();
    if ($this->isEdit && $this->rawMaterialId) {
      $rawMaterial = RawMaterial::findOrFail($this->rawMaterialId);
      $rawMaterial->update([
        'name' => $this->name,
        'code' => $this->code,
        'description' => $this->description,
        'unit' => $this->unit,
        'is_active' => $this->is_active,
      ]);
    } else {
      RawMaterial::create([
        'name' => $this->name,
        'code' => $this->code,
        'description' => $this->description,
        'unit' => $this->unit,
        'is_active' => $this->is_active,
      ]);
    }
    $this->showModal = false;
    $this->resetForm();
    session()->flash('message', $this->isEdit ? 'Raw material updated.' : 'Raw material created.');
  }

  public function confirmDelete($id)
  {
    $this->deleteId = $id;
    $this->showDeleteModal = true;
  }

  public function deleteRawMaterial()
  {
    $rawMaterial = RawMaterial::findOrFail($this->deleteId);
    $rawMaterial->delete();
    $this->showDeleteModal = false;
    $this->deleteId = null;
    session()->flash('message', 'Raw material deleted.');
  }

  public function resetForm()
  {
    $this->rawMaterialId = null;
    $this->name = '';
    $this->code = '';
    $this->description = '';
    $this->unit = 'kg';
    $this->is_active = true;
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