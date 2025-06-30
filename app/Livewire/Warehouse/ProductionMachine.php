<?php

namespace App\Livewire\Warehouse;

use App\Models\ProductionLine;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class ProductionMachine extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    public $showModal = false;
    public $isEdit = false;
    public $lineId = null;
    public $name = '';
    public $min_size = '';
    public $max_size = '';
    public $capacity_kg_hr = '';
    public $description = '';

    public $showDeleteModal = false;
    public $deleteId = null;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'min_size' => 'nullable|integer|min:0',
            'max_size' => 'nullable|integer|min:0',
            'capacity_kg_hr' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    public function mount(): void
    {
        //
    }

    #[Layout('components.layouts.app')]
    public function render(): View
    {
        $lines = ProductionLine::when($this->search, function ($q) {
            $q->where('name', 'like', "%{$this->search}%");
        })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);
        return view('livewire.ware-house.production-machine', [
            'lines' => $lines,
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
        $line = ProductionLine::findOrFail($id);
        $this->lineId = $line->id;
        $this->name = $line->name;
        $this->min_size = $line->min_size;
        $this->max_size = $line->max_size;
        $this->capacity_kg_hr = $line->capacity_kg_hr;
        $this->description = $line->description;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function saveLine()
    {
        $this->validate();
        if ($this->isEdit && $this->lineId) {
            $line = ProductionLine::findOrFail($this->lineId);
            $line->update([
                'name' => $this->name,
                'min_size' => $this->min_size,
                'max_size' => $this->max_size,
                'capacity_kg_hr' => $this->capacity_kg_hr,
                'description' => $this->description,
            ]);
        } else {
            ProductionLine::create([
                'name' => $this->name,
                'min_size' => $this->min_size,
                'max_size' => $this->max_size,
                'capacity_kg_hr' => $this->capacity_kg_hr,
                'description' => $this->description,
            ]);
        }
        $this->showModal = false;
        $this->resetForm();
        session()->flash('message', $this->isEdit ? 'Line updated.' : 'Line created.');
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteLine()
    {
        $line = ProductionLine::findOrFail($this->deleteId);
        $line->delete();
        $this->showDeleteModal = false;
        $this->deleteId = null;
        session()->flash('message', 'Line deleted.');
    }

    public function resetForm()
    {
        $this->lineId = null;
        $this->name = '';
        $this->min_size = '';
        $this->max_size = '';
        $this->capacity_kg_hr = '';
        $this->description = '';
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