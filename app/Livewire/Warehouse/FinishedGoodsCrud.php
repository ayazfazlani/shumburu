<?php

namespace App\Livewire\Warehouse;

use App\Models\Product;
use Livewire\Component;
use App\Models\Customer;
use App\Models\FinishedGood;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinishedGoodsCrud extends Component
{
    use WithPagination;

    // Search and filter properties
    public $search = '';
    public $filter_product = '';
    public $filter_customer = '';
    public $filter_purpose = '';
    public $filter_type = '';
    public $filter_status = '';
    public $date_from = '';
    public $date_to = '';

    // Form properties
    public $showForm = false;
    public $editingId = null;
    public $viewingId = null;

    // Form fields
    public $product_id = '';
    public $quantity = '';
    public $batch_number = '';
    public $production_date = '';
    public $purpose = 'for_stock';
    public $customer_id = '';
    public $produced_by = '';
    public $notes = '';
    public $type = 'roll';
    public $length_m = '';
    public $outer_diameter = '';
    public $size = '';
    public $surface = '';
    public $thickness = '';
    public $start_ovality = '';
    public $end_ovality = '';
    public $stripe_color = '';
    public $total_weight = '';

    // Bulk operations
    public $selectedItems = [];
    public $selectAll = false;
    public $bulkAction = '';

    // Pagination
    public $perPage = 10;

    protected $rules = [
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|numeric|min:0.001',
        'batch_number' => 'nullable|string|max:255',
        'production_date' => 'required|date',
        'purpose' => 'required|in:for_stock,for_sale,for_customer',
        'customer_id' => 'nullable|exists:customers,id',
        'notes' => 'nullable|string|max:1000',
        'type' => 'required|in:roll,cut',
        'length_m' => 'required|numeric|min:0.001',
        'outer_diameter' => 'nullable|numeric|min:0',
        'size' => 'nullable|string|max:100',
        'surface' => 'nullable|string|max:100',
        'thickness' => 'nullable|numeric|min:0',
        'start_ovality' => 'nullable|numeric|min:0',
        'end_ovality' => 'nullable|numeric|min:0',
        'stripe_color' => 'nullable|string|max:50',
        'total_weight' => 'nullable|numeric|min:0',
    ];

    public function mount()
    {
        $this->production_date = now()->format('Y-m-d');
        $this->produced_by = Auth::id();
    }

    public function render()
    {
        $query = FinishedGood::with(['product', 'customer', 'producedBy'])
            ->when($this->search, function($q) {
                $q->where(function($query) {
                    $query->where('batch_number', 'like', '%' . $this->search . '%')
                          ->orWhere('size', 'like', '%' . $this->search . '%')
                          ->orWhere('notes', 'like', '%' . $this->search . '%')
                          ->orWhereHas('product', function($q) {
                              $q->where('name', 'like', '%' . $this->search . '%');
                          });
                });
            })
            ->when($this->filter_product, function($q) {
                $q->where('product_id', $this->filter_product);
            })
            ->when($this->filter_customer, function($q) {
                $q->where('customer_id', $this->filter_customer);
            })
            ->when($this->filter_purpose, function($q) {
                $q->where('purpose', $this->filter_purpose);
            })
            ->when($this->filter_type, function($q) {
                $q->where('type', $this->filter_type);
            })
            ->when($this->date_from, function($q) {
                $q->whereDate('production_date', '>=', $this->date_from);
            })
            ->when($this->date_to, function($q) {
                $q->whereDate('production_date', '<=', $this->date_to);
            })
            ->orderBy('created_at', 'desc');

        $finishedGoods = $query->paginate($this->perPage);

        $products = Product::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        return view('livewire.warehouse.finished-goods-crud', [
            'finishedGoods' => $finishedGoods,
            'products' => $products,
            'customers' => $customers,
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editingId = null;
    }

    public function edit($id)
    {
        $finishedGood = FinishedGood::findOrFail($id);
        
        $this->editingId = $id;
        $this->product_id = $finishedGood->product_id;
        $this->quantity = $finishedGood->quantity;
        $this->batch_number = $finishedGood->batch_number;
        $this->production_date = $finishedGood->production_date->format('Y-m-d');
        $this->purpose = $finishedGood->purpose;
        $this->customer_id = $finishedGood->customer_id;
        $this->produced_by = $finishedGood->produced_by;
        $this->notes = $finishedGood->notes;
        $this->type = $finishedGood->type;
        $this->length_m = $finishedGood->length_m;
        $this->outer_diameter = $finishedGood->outer_diameter;
        $this->size = $finishedGood->size;
        $this->surface = $finishedGood->surface;
        $this->thickness = $finishedGood->thickness;
        $this->start_ovality = $finishedGood->start_ovality;
        $this->end_ovality = $finishedGood->end_ovality;
        $this->stripe_color = $finishedGood->stripe_color;
        $this->total_weight = $finishedGood->total_weight;

        $this->showForm = true;
    }

    public function view($id)
    {
        $this->viewingId = $id;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'batch_number' => $this->batch_number,
            'production_date' => $this->production_date,
            'purpose' => $this->purpose,
            'customer_id' => $this->customer_id ?: null,
            'produced_by' => $this->produced_by,
            'notes' => $this->notes,
            'type' => $this->type,
            'length_m' => $this->length_m,
            'outer_diameter' => $this->outer_diameter,
            'size' => $this->size,
            'surface' => $this->surface,
            'thickness' => $this->thickness,
            'start_ovality' => $this->start_ovality,
            'end_ovality' => $this->end_ovality,
            'stripe_color' => $this->stripe_color,
            'total_weight' => $this->total_weight ?: $this->calculateWeight(),
        ];

        if ($this->editingId) {
            $finishedGood = FinishedGood::findOrFail($this->editingId);
            $finishedGood->update($data);
            session()->flash('success', 'Finished good updated successfully!');
        } else {
            FinishedGood::create($data);
            session()->flash('success', 'Finished good created successfully!');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        $finishedGood = FinishedGood::findOrFail($id);
        $finishedGood->delete();
        session()->flash('success', 'Finished good deleted successfully!');
    }

    public function deleteSelected()
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', 'Please select items to delete.');
            return;
        }

        FinishedGood::whereIn('id', $this->selectedItems)->delete();
        $this->selectedItems = [];
        $this->selectAll = false;
        session()->flash('success', 'Selected finished goods deleted successfully!');
    }

    public function updatePurpose()
    {
        if (empty($this->selectedItems) || !$this->bulkAction) {
            session()->flash('error', 'Please select items and choose an action.');
            return;
        }

        FinishedGood::whereIn('id', $this->selectedItems)->update(['purpose' => $this->bulkAction]);
        $this->selectedItems = [];
        $this->selectAll = false;
        $this->bulkAction = '';
        session()->flash('success', 'Selected finished goods updated successfully!');
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedItems = FinishedGood::pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSelectedItems()
    {
        $this->selectAll = count($this->selectedItems) === FinishedGood::count();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filter_product', 'filter_customer', 'filter_purpose', 'filter_type', 'date_from', 'date_to']);
        $this->resetPage();
    }

    public function exportToCsv()
    {
        $query = FinishedGood::with(['product', 'customer', 'producedBy'])
            ->when($this->search, function($q) {
                $q->where(function($query) {
                    $query->where('batch_number', 'like', '%' . $this->search . '%')
                          ->orWhere('size', 'like', '%' . $this->search . '%')
                          ->orWhereHas('product', function($q) {
                              $q->where('name', 'like', '%' . $this->search . '%');
                          });
                });
            })
            ->when($this->filter_product, function($q) {
                $q->where('product_id', $this->filter_product);
            })
            ->when($this->filter_customer, function($q) {
                $q->where('customer_id', $this->filter_customer);
            })
            ->when($this->filter_purpose, function($q) {
                $q->where('purpose', $this->filter_purpose);
            })
            ->when($this->filter_type, function($q) {
                $q->where('type', $this->filter_type);
            })
            ->when($this->date_from, function($q) {
                $q->whereDate('production_date', '>=', $this->date_from);
            })
            ->when($this->date_to, function($q) {
                $q->whereDate('production_date', '<=', $this->date_to);
            })
            ->orderBy('created_at', 'desc');

        $finishedGoods = $query->get();

        $filename = 'finished_goods_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($finishedGoods) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'ID', 'Product', 'Quantity', 'Batch Number', 'Production Date', 
                'Purpose', 'Customer', 'Type', 'Length (m)', 'Size', 'Thickness',
                'Outer Diameter', 'Surface', 'Start Ovality', 'End Ovality',
                'Stripe Color', 'Total Weight', 'Produced By', 'Notes', 'Created At'
            ]);

            // CSV Data
            foreach ($finishedGoods as $good) {
                fputcsv($file, [
                    $good->id,
                    $good->product->name ?? '',
                    $good->quantity,
                    $good->batch_number ?? '',
                    $good->production_date->format('Y-m-d'),
                    ucfirst(str_replace('_', ' ', $good->purpose)),
                    $good->customer->name ?? '',
                    ucfirst($good->type),
                    $good->length_m,
                    $good->size ?? '',
                    $good->thickness ?? '',
                    $good->outer_diameter ?? '',
                    $good->surface ?? '',
                    $good->start_ovality ?? '',
                    $good->end_ovality ?? '',
                    $good->stripe_color ?? '',
                    $good->total_weight ?? '',
                    $good->producedBy->name ?? '',
                    $good->notes ?? '',
                    $good->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function calculateWeight()
    {
        if ($this->product_id && $this->length_m && $this->quantity) {
            $product = Product::find($this->product_id);
            $weightPerMeter = $product->weight_per_meter ?? 1.0;
            return $this->length_m * $this->quantity * $weightPerMeter;
        }
        return null;
    }

    private function resetForm()
    {
        $this->reset([
            'product_id', 'quantity', 'batch_number', 'production_date', 'purpose',
            'customer_id', 'produced_by', 'notes', 'type', 'length_m', 'outer_diameter',
            'size', 'surface', 'thickness', 'start_ovality', 'end_ovality',
            'stripe_color', 'total_weight'
        ]);
        $this->production_date = now()->format('Y-m-d');
        $this->produced_by = Auth::id();
        $this->resetValidation();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterProduct()
    {
        $this->resetPage();
    }

    public function updatedFilterCustomer()
    {
        $this->resetPage();
    }

    public function updatedFilterPurpose()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }
}
