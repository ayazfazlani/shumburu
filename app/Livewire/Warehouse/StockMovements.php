<?php

namespace App\Livewire\Warehouse;

use App\Models\MaterialStockIn;
use App\Models\MaterialStockOut;
use App\Models\RawMaterial;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class StockMovements extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    /** 'in' or 'out' */
    public $movement_type = 'in';

    // Form
    public $raw_material_id;
    public $quantity;
    public $batch_number;
    public $movement_date;
    public $notes;

    // Edit
    public $edit_id;
    public $edit_type;
    public $is_editing = false;

    // Delete
    public $delete_id;
    public $delete_type;
    public $showDeleteModal = false;
    public $deleteQuantity = 0;
    public $deleteMaterialName = '';

    // List filter
    public $filter_type = 'all'; // all | in | out

    protected function rules()
    {
        return [
            'movement_type'   => 'required|in:in,out',
            'raw_material_id' => 'required|exists:raw_materials,id',
            'quantity'        => 'required|numeric|min:0.001',
            'batch_number'    => 'required|string|max:255',
            'movement_date'   => 'required|date',
            'notes'           => 'nullable|string',
        ];
    }

    public function mount()
    {
        $this->movement_date = now()->format('Y-m-d');
        $this->showDeleteModal = false;
    }

    public function updatedMovementType()
    {
        // Clear validation when switching type
        $this->resetValidation();
    }

    /* ───────────────────────── SAVE ───────────────────────── */

    public function save()
    {
        $this->validate();

        return $this->movement_type === 'in'
            ? $this->saveStockIn()
            : $this->saveStockOut();
    }

    private function saveStockIn()
    {
        $user = Auth::user();

        if ($this->is_editing) {
            $stockIn = MaterialStockIn::findOrFail($this->edit_id);
            $oldQty  = $stockIn->quantity;
            $oldMat  = $stockIn->raw_material_id;

            $stockIn->update([
                'raw_material_id' => $this->raw_material_id,
                'quantity'        => $this->quantity,
                'batch_number'    => $this->batch_number,
                'received_date'   => $this->movement_date,
                'notes'           => $this->notes,
            ]);

            RawMaterial::$skipAutoTransaction = true;
            if ($oldMat == $this->raw_material_id) {
                $mat = RawMaterial::find($oldMat);
                $mat->quantity += ($this->quantity - $oldQty);
                $mat->save();
            } else {
                $old = RawMaterial::find($oldMat);
                $old->quantity += $oldQty;
                $old->save();

                $new = RawMaterial::find($this->raw_material_id);
                $new->quantity -= $this->quantity;
                $new->save();
            }
            RawMaterial::$skipAutoTransaction = false;

            session()->flash('message', 'Stock IN record updated successfully.');
        } else {
            MaterialStockIn::create([
                'raw_material_id' => $this->raw_material_id,
                'quantity'        => $this->quantity,
                'batch_number'    => $this->batch_number,
                'received_date'   => $this->movement_date,
                'received_by'     => $user->id,
                'notes'           => $this->notes,
            ]);

            RawMaterial::$skipAutoTransaction = true;
            $mat = RawMaterial::find($this->raw_material_id);
            $mat->quantity += $this->quantity;
            $mat->save();
            RawMaterial::$skipAutoTransaction = false;

            session()->flash('message', 'Material stock-in recorded successfully.');
        }

        $this->resetForm();
        $this->resetPage();
    }

    private function saveStockOut()
    {
        $user = Auth::user();
        $rawMaterial = RawMaterial::find($this->raw_material_id);

        if ($this->is_editing) {
            $stockOut = MaterialStockOut::findOrFail($this->edit_id);
            $oldQty   = $stockOut->quantity;
            $oldMat   = $stockOut->raw_material_id;

            // Calculate difference (same material only supported for stock-out edit)
            if ($oldMat != $this->raw_material_id) {
                session()->flash('error', 'Changing material on an existing stock-out is not allowed. Delete and re-create instead.');
                return;
            }

            $diff = $this->quantity - $oldQty;
            if ($diff > 0 && $rawMaterial->quantity < $diff) {
                session()->flash('error', 'Insufficient stock for update. Available: ' . $rawMaterial->quantity . ' ' . $rawMaterial->unit);
                return;
            }

            $stockOut->update([
                'quantity'     => $this->quantity,
                'batch_number' => $this->batch_number,
                'issued_date'  => $this->movement_date,
                'notes'        => $this->notes,
            ]);

            RawMaterial::$skipAutoTransaction = true;
            $rawMaterial->quantity -= $diff;
            $rawMaterial->save();
            RawMaterial::$skipAutoTransaction = false;

            session()->flash('message', 'Stock OUT record updated successfully.');
        } else {
            if ($rawMaterial->quantity < $this->quantity) {
                session()->flash('error', 'Insufficient stock. Available: ' . $rawMaterial->quantity . ' ' . $rawMaterial->unit);
                return;
            }

            MaterialStockOut::create([
                'raw_material_id' => $this->raw_material_id,
                'quantity'        => $this->quantity,
                'batch_number'    => $this->batch_number,
                'issued_date'     => $this->movement_date,
                'issued_by'       => $user->id,
                'status'          => 'material_on_process',
                'notes'           => $this->notes,
            ]);

            RawMaterial::$skipAutoTransaction = true;
            $rawMaterial->quantity -= $this->quantity;
            $rawMaterial->save();
            RawMaterial::$skipAutoTransaction = false;

            session()->flash('message', 'Material stock-out recorded successfully.');
        }

        $this->resetForm();
        $this->resetPage();
    }

    /* ───────────────────────── EDIT ───────────────────────── */

    public function edit($id, $type)
    {
        $this->edit_type     = $type;
        $this->movement_type = $type;

        if ($type === 'in') {
            $rec = MaterialStockIn::findOrFail($id);
            $this->movement_date = $rec->received_date->format('Y-m-d');
        } else {
            $rec = MaterialStockOut::findOrFail($id);
            $this->movement_date = $rec->issued_date->format('Y-m-d');
        }

        $this->edit_id         = $rec->id;
        $this->raw_material_id = $rec->raw_material_id;
        $this->quantity        = $rec->quantity;
        $this->batch_number    = $rec->batch_number;
        $this->notes           = $rec->notes;
        $this->is_editing      = true;

        $this->dispatch('scroll-to-form');
    }

    public function cancelEdit()
    {
        $this->resetForm();
    }

    /* ───────────────────────── DELETE ───────────────────────── */

    public function setDeleteId($id, $type)
    {
        $this->delete_id   = $id;
        $this->delete_type = $type;

        if ($type === 'in') {
            $rec = MaterialStockIn::findOrFail($id);
        } else {
            $rec = MaterialStockOut::findOrFail($id);
        }

        $this->deleteQuantity     = $rec->quantity;
        $this->deleteMaterialName = $rec->rawMaterial->name ?? 'N/A';
        $this->showDeleteModal    = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal    = false;
        $this->delete_id          = null;
        $this->delete_type        = null;
        $this->deleteQuantity     = 0;
        $this->deleteMaterialName = '';
    }

    public function delete()
    {
        if (!$this->delete_id) return;

        RawMaterial::$skipAutoTransaction = true;

        if ($this->delete_type === 'in') {
            $rec = MaterialStockIn::findOrFail($this->delete_id);
            $mat = $rec->rawMaterial;
            $mat->quantity -= $rec->quantity;
            $mat->save();
            $rec->delete();
        } else {
            $rec = MaterialStockOut::findOrFail($this->delete_id);
            $mat = $rec->rawMaterial;
            $mat->quantity += $rec->quantity;
            $mat->save();
            $rec->delete();
        }

        RawMaterial::$skipAutoTransaction = false;

        session()->flash('message', 'Stock ' . strtoupper($this->delete_type) . ' record deleted successfully.');
        $this->closeDeleteModal();
    }

    public function updateStatus($id, $status)
    {
        MaterialStockOut::findOrFail($id)->update(['status' => $status]);
        session()->flash('message', 'Status updated successfully.');
    }

    /* ───────────────────────── HELPERS ───────────────────────── */

    private function resetForm()
    {
        $this->reset([
            'raw_material_id', 'quantity', 'batch_number', 'notes',
            'edit_id', 'edit_type', 'is_editing', 'delete_id', 'delete_type',
        ]);
        $this->movement_date  = now()->format('Y-m-d');
        $this->showDeleteModal = false;
        $this->resetValidation();
    }

    public function render()
    {
        $rawMaterials = RawMaterial::where('is_active', true)->get();

        /* ── Merge both collections for unified table ── */
        $ins = MaterialStockIn::with(['rawMaterial', 'receivedBy'])
            ->when($this->filter_type === 'out', fn($q) => $q->whereRaw('1=0'))
            ->latest('received_date')
            ->take(100)
            ->get()
            ->map(fn($r) => (object)[
                'id'           => $r->id,
                'type'         => 'in',
                'raw_material' => $r->rawMaterial,
                'quantity'     => $r->quantity,
                'batch_number' => $r->batch_number,
                'date'         => $r->received_date,
                'user'         => $r->receivedBy,
                'status'       => 'received',
                'notes'        => $r->notes,
            ]);

        $outs = MaterialStockOut::with(['rawMaterial', 'issuedBy'])
            ->when($this->filter_type === 'in', fn($q) => $q->whereRaw('1=0'))
            ->latest('issued_date')
            ->take(100)
            ->get()
            ->map(fn($r) => (object)[
                'id'           => $r->id,
                'type'         => 'out',
                'raw_material' => $r->rawMaterial,
                'quantity'     => $r->quantity,
                'batch_number' => $r->batch_number,
                'date'         => $r->issued_date,
                'user'         => $r->issuedBy,
                'status'       => $r->status,
                'notes'        => $r->notes,
            ]);

        $merged = $ins->concat($outs)->sortByDesc('date')->values();

        $page     = $this->getPage();
        $perPage  = 10;
        $total    = $merged->count();
        $items    = $merged->slice(($page - 1) * $perPage, $perPage)->values();
        $movements = new \Illuminate\Pagination\LengthAwarePaginator(
            $items, $total, $perPage, $page, ['path' => request()->url()]
        );

        /* ── Stats ── */
        $stats = [
            'total_in'    => MaterialStockIn::count(),
            'total_out'   => MaterialStockOut::count(),
            'qty_in'      => MaterialStockIn::sum('quantity'),
            'qty_out'     => MaterialStockOut::sum('quantity'),
            'today_in'    => MaterialStockIn::whereDate('received_date', today())->count(),
            'today_out'   => MaterialStockOut::whereDate('issued_date', today())->count(),
        ];

        return view('livewire.warehouse.stock-movements', [
            'rawMaterials' => $rawMaterials,
            'movements'    => $movements,
            'stats'        => $stats,
        ]);
    }
}
