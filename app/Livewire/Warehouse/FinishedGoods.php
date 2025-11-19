<?php

namespace App\Livewire\Warehouse;

use App\Models\Customer;
use App\Models\FinishedGood;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class FinishedGoods extends Component
{
    use WithPagination;

    // Form fields
    public $product_id;

    public $type = 'roll';

    public $length_m;

    public $quantity;

    public $waste_quantity = 0;

    public $batch_number;

    public $production_date;

    public $purpose = 'for_stock';

    public $customer_id;

    public $notes;

    // Additional fields
    public $size;

    public $outerDiameter;

    public $Surface;

    public $thickness;

    public $startOvality;

    public $endOvality;

    public $stripeColor;

    // New field
    public $weightPerMeter;

    // Edit mode variables
    public $editingId = null;

    public $isEditing = false;

    protected $rules = [
        'product_id' => 'required|exists:products,id',
        'type' => 'required|in:roll,cut',
        'length_m' => 'required|numeric|min:0.01',
        'quantity' => 'required|numeric|min:0.01',
        'waste_quantity' => 'nullable|numeric|min:0',
        'batch_number' => 'required|string|max:255',
        'production_date' => 'required|date',
        'purpose' => 'required|in:for_stock,for_customer_order',
        'customer_id' => 'nullable|required_if:purpose,for_customer_order|exists:customers,id',
        'notes' => 'nullable|string',
        'size' => 'nullable|numeric',
        'outerDiameter' => 'nullable|numeric',
        'Surface' => 'nullable|string',
        'thickness' => 'nullable|string',
        'startOvality' => 'nullable|string',
        'endOvality' => 'nullable|string',
        'stripeColor' => 'nullable|string',
        'weightPerMeter' => 'nullable|numeric|min:0',
    ];

    public function mount()
    {
        $this->type = 'roll';
        $this->production_date = now()->format('Y-m-d');
    }

    public function updatedPurpose()
    {
        if ($this->purpose === 'for_stock') {
            $this->customer_id = null;
        }
    }

    public function save()
    {
        $this->validate();
        $user = Auth::user();

        // if ($this->product_id) {
        //     $weightPerMeter = Product::where('id', $this->product_id)->value('weight_per_meter');
        //     $totalWeight = $weightPerMeter * $this->quantity * $this->length_m;
        // }

        $totalWeight = $this->weightPerMeter ? $this->weightPerMeter * $this->quantity * $this->length_m : null;

        $data = [
            'product_id' => $this->product_id,
            'type' => $this->type,
            'length_m' => $this->length_m,
            'quantity' => $this->quantity,
            'waste_quantity' => $this->waste_quantity ?? 0,
            'batch_number' => $this->batch_number,
            'production_date' => $this->production_date,
            'purpose' => $this->purpose,
            'customer_id' => $this->customer_id,
            'produced_by' => $user->id,
            'notes' => $this->notes,
            'size' => $this->size,
            'total_weight' => $totalWeight ?? 0,
            'outer_diameter' => $this->outerDiameter,
            'surface' => $this->Surface,
            'thickness' => $this->thickness,
            'start_ovality' => $this->startOvality,
            'end_ovality' => $this->endOvality,
            'stripe_color' => $this->stripeColor,
            'weight_per_meter' => $this->weightPerMeter,
        ];

        if ($this->isEditing) {
            FinishedGood::find($this->editingId)->update($data);
            session()->flash('message', 'Finished goods updated successfully.');
        } else {
            FinishedGood::create($data);
            session()->flash('message', 'Finished goods recorded successfully.');
        }

        $this->resetForm();
    }

    public function edit($id)
    {
        $finishedGood = FinishedGood::findOrFail($id);

        $this->editingId = $id;
        $this->isEditing = true;

        // Fill form fields
        $this->product_id = $finishedGood->product_id;
        $this->type = $finishedGood->type;
        $this->length_m = $finishedGood->length_m;
        $this->quantity = $finishedGood->quantity;
        $this->waste_quantity = $finishedGood->waste_quantity;
        $this->batch_number = $finishedGood->batch_number;
        $this->production_date = $finishedGood->production_date;
        $this->purpose = $finishedGood->purpose;
        $this->customer_id = $finishedGood->customer_id;
        $this->notes = $finishedGood->notes;
        $this->size = $finishedGood->size;
        $this->outerDiameter = $finishedGood->outer_diameter;
        $this->Surface = $finishedGood->surface;
        $this->thickness = $finishedGood->thickness;
        $this->startOvality = $finishedGood->start_ovality;
        $this->endOvality = $finishedGood->end_ovality;
        $this->stripeColor = $finishedGood->stripe_color;
        $this->weightPerMeter = $finishedGood->weight_per_meter;

        // Scroll to form
        $this->dispatch('scroll-to-form');
    }

    public function delete($id)
    {
        $finishedGood = FinishedGood::findOrFail($id);
        $finishedGood->delete();

        session()->flash('message', 'Finished goods record deleted successfully.');
    }

    public function cancelEdit()
    {
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'product_id', 'type', 'length_m', 'quantity', 'waste_quantity',
            'batch_number', 'customer_id', 'notes', 'size', 'outerDiameter',
            'Surface', 'thickness', 'startOvality', 'endOvality', 'stripeColor',
            'editingId', 'isEditing',
            'weightPerMeter',

        ]);
        $this->production_date = now()->format('Y-m-d');
        $this->purpose = 'for_stock';
        $this->type = 'roll';
    }

    public function render()
    {
        $finishedGoods = FinishedGood::with(['product', 'customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $products = Product::where('is_active', true)->get();
        $customers = Customer::where('is_active', true)->get();

        return view('livewire.warehouse.finished-goods', [
            'finishedGoods' => $finishedGoods,
            'products' => $products,
            'customers' => $customers,
        ]);
    }
}
