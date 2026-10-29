<?php

namespace App\Livewire\FYA;

use App\Models\Customer;
use App\Models\FinishedGood;
use App\Models\FyaWarehouse;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Warehouse2 extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // ─── Form fields ───
    public $movement_type = 'in';
    public $finished_good_id;
    public $quantity;
    public $batch_number;
    public $purpose = 'for_stock';
    public $customer_id;
    public $movement_date;
    public $notes;

    // ─── Editing ───
    public $editing_id = null;
    public $is_editing = false;

    // ─── Filters ───
    public $filter_type;
    public $filter_product_id;
    public $filter_batch;
    public $filter_purpose;
    public $filter_customer_id;
    public $filter_date_from;
    public $filter_date_to;

    // ─── UI state ───
    public bool $show_filters = false;
    public string $stock_search = '';

    // ─── Delete modal ───
    public $delete_id;
    public $deleteBatchNumber;
    public $showDeleteModal = false;

    protected $rules = [
        'movement_type'   => 'required|in:in,out',
        'finished_good_id'=> 'required|exists:finished_goods,id',
        'quantity'        => 'required|numeric|min:0.001',
        'batch_number'    => 'required|string',
        'purpose'         => 'required|in:for_stock,for_customer_order',
        'customer_id'     => 'nullable|required_if:purpose,for_customer_order|exists:customers,id',
        'movement_date'   => 'required|date',
        'notes'           => 'nullable|string',
    ];

    public function mount(): void
    {
        $this->movement_date = now()->format('Y-m-d');
        $this->showDeleteModal = false;
    }

    public function updatedPurpose(): void
    {
        if ($this->purpose === 'for_stock') {
            $this->customer_id = null;
        }
    }

    // ──────────────────────────────── SAVE ────────────────────────────────

    public function save(): void
    {
        $this->validate();

        $user = Auth::user();

        // Prevent negative stock when movement is OUT
        if ($this->movement_type === 'out') {
            $available = $this->calculateAvailableForBatch(
                $this->finished_good_id,
                $this->batch_number,
                $this->purpose,
                $this->customer_id
            );

            // If editing, exclude the current movement from calculation
            if ($this->is_editing) {
                $currentMovement = FyaWarehouse::find($this->editing_id);
                if ($currentMovement) {
                    if ($currentMovement->movement_type === 'out') {
                        $available += $currentMovement->quantity;
                    } elseif ($currentMovement->movement_type === 'in') {
                        $available -= $currentMovement->quantity;
                    }
                }
            }

            if ($this->quantity > $available) {
                session()->flash('error', 'Insufficient stock available for this batch and purpose.');
                return;
            }
        }

        if ($this->is_editing && $this->editing_id) {
            $movement = FyaWarehouse::findOrFail($this->editing_id);
            $movement->update([
                'finished_good_id' => $this->finished_good_id,
                'movement_type'    => $this->movement_type,
                'quantity'         => $this->quantity,
                'batch_number'     => $this->batch_number,
                'purpose'          => $this->purpose,
                'customer_id'      => $this->customer_id,
                'movement_date'    => $this->movement_date,
                'notes'            => $this->notes,
                'updated_by'       => $user->id,
            ]);

            session()->flash('message', 'Movement updated successfully.');
        } else {
            FyaWarehouse::create([
                'finished_good_id' => $this->finished_good_id,
                'movement_type'    => $this->movement_type,
                'quantity'         => $this->quantity,
                'batch_number'     => $this->batch_number,
                'purpose'          => $this->purpose,
                'customer_id'      => $this->customer_id,
                'movement_date'    => $this->movement_date,
                'notes'            => $this->notes,
                'created_by'       => $user->id,
            ]);

            session()->flash('message', 'Movement recorded successfully.');
        }

        $this->resetForm();
        $this->resetPage();
    }

    // ──────────────────────────────── EDIT ────────────────────────────────

    public function edit($id): void
    {
        $movement = FyaWarehouse::findOrFail($id);

        $this->editing_id    = $id;
        $this->is_editing    = true;
        $this->movement_type = $movement->movement_type;
        $this->finished_good_id = $movement->finished_good_id;
        $this->quantity      = $movement->quantity;
        $this->batch_number  = $movement->batch_number;
        $this->purpose       = $movement->purpose;
        $this->customer_id   = $movement->customer_id;
        $this->movement_date = $movement->movement_date;
        $this->notes         = $movement->notes;

        $this->dispatch('scroll-to-form');
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    // ──────────────────────────── DELETE (modal) ────────────────────────────

    public function setDeleteId($id): void
    {
        $movement = FyaWarehouse::findOrFail($id);

        $this->delete_id         = $id;
        $this->deleteBatchNumber = $movement->batch_number;
        $this->showDeleteModal   = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal   = false;
        $this->delete_id         = null;
        $this->deleteBatchNumber = null;
    }

    public function delete(): void
    {
        if (!$this->delete_id) {
            return;
        }

        $movement = FyaWarehouse::findOrFail($this->delete_id);

        // Guard against negative stock when deleting an IN movement
        if ($movement->movement_type === 'in') {
            $available = $this->calculateAvailableForBatch(
                $movement->finished_good_id,
                $movement->batch_number,
                $movement->purpose,
                $movement->customer_id
            );

            if (($available - $movement->quantity) < 0) {
                session()->flash('error', 'Cannot delete this movement: it would cause negative stock.');
                $this->closeDeleteModal();
                return;
            }
        }

        $movement->delete();
        session()->flash('message', 'Movement deleted successfully.');
        $this->closeDeleteModal();
        $this->resetPage();
    }

    // ──────────────────────────────── HELPERS ────────────────────────────────

    private function resetForm(): void
    {
        $this->editing_id = null;
        $this->is_editing = false;

        $this->reset([
            'movement_type',
            'finished_good_id',
            'quantity',
            'batch_number',
            'purpose',
            'customer_id',
            'notes',
        ]);

        $this->movement_type = 'in';
        $this->purpose       = 'for_stock';
        $this->movement_date = now()->format('Y-m-d');
    }

    private function calculateAvailableForBatch(int $finishedGoodId, string $batch, string $purpose, ?int $customerId): float
    {
        $base = FyaWarehouse::where('finished_good_id', $finishedGoodId)
            ->where('batch_number', $batch)
            ->where('purpose', $purpose)
            ->when($purpose === 'for_customer_order', fn ($q) => $q->where('customer_id', $customerId));

        $in  = (clone $base)->where('movement_type', 'in')->sum('quantity');
        $out = (clone $base)->where('movement_type', 'out')->sum('quantity');

        return (float) $in - (float) $out;
    }

    // ──────────────────────────────── RENDER ────────────────────────────────

    public function render()
    {
        $finishedGoods = FinishedGood::with(['product', 'customer'])->latest()->get();
        $customers     = Customer::where('is_active', true)->get();

        $movements = FyaWarehouse::with(['finishedGood.product', 'customer', 'createdBy'])
            ->when($this->filter_type,        fn ($q) => $q->where('movement_type', $this->filter_type))
            ->when($this->filter_product_id,  fn ($q) => $q->where('finished_good_id', $this->filter_product_id))
            ->when($this->filter_batch,       fn ($q) => $q->where('batch_number', 'like', "%{$this->filter_batch}%"))
            ->when($this->filter_purpose,     fn ($q) => $q->where('purpose', $this->filter_purpose))
            ->when($this->filter_customer_id, fn ($q) => $q->where('customer_id', $this->filter_customer_id))
            ->when($this->filter_date_from,   fn ($q) => $q->whereDate('movement_date', '>=', $this->filter_date_from))
            ->when($this->filter_date_to,     fn ($q) => $q->whereDate('movement_date', '<=', $this->filter_date_to))
            ->orderByDesc('movement_date')
            ->paginate(10);

        /**
         * Stock balance per (finished_good, batch, purpose, customer).
         * FIXED: removed `quantity` from select/groupBy — was splitting rows
         *        per movement instead of aggregating per batch.
         */
        $stockBalance = FyaWarehouse::query()
            ->selectRaw('finished_good_id, batch_number, purpose, customer_id, SUM(CASE WHEN movement_type = "in" THEN quantity ELSE -quantity END) as balance')
            ->groupBy('finished_good_id', 'batch_number', 'purpose', 'customer_id')
            ->with(['finishedGood.product', 'customer'])
            ->having('balance', '>', 0)
            ->orderBy('finished_good_id')
            ->get();

        // Filtered stock for the right-side card grid
        $filteredStock = $stockBalance
            ->when($this->stock_search, function ($c, $q) {
                $q = strtolower($q);
                return $c->filter(fn ($row) =>
                    str_contains(strtolower($row->finishedGood->product->name ?? ''), $q) ||
                    str_contains(strtolower($row->batch_number ?? ''), $q)
                );
            })
            ->sortByDesc('balance')
            ->values();

        return view('livewire.f-y-a.warehouse2', [
            'finishedGoods' => $finishedGoods,
            'customers'     => $customers,
            'movements'     => $movements,
            'stockBalance'  => $stockBalance,
            'filteredStock' => $filteredStock,
        ]);
    }
}
