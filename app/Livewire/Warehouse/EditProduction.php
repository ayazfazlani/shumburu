<?php

namespace App\Livewire\Warehouse;

use App\Models\Product;
use Livewire\Component;
use App\Models\ScrapWaste;
use App\Models\ProductionLine;
use Livewire\Attributes\Layout;
use App\Models\MaterialStockOut;
use Illuminate\Support\Facades\DB;
use App\Models\MaterialStockOutLine;
use Illuminate\Support\Facades\Auth;

class EditProduction extends Component
{
  public $productionId;
  public $production_line_id;
  public $product_id;
  public $material_stock_out_id;
  public $shift;
  public $size;
  public $surface;
  public $thickness;
  public $outer_diameter;
  public $ovality;
  public $notes;

  public $finishedGoods = [];
  public $scraps = [];

  public function mount($id)
  {
    $this->productionId = $id;
    $production = MaterialStockOutLine::with([
      'materialStockOut',
      'productionLengths',
      'scrapWastes'
    ])->findOrFail($id);

    $this->production_line_id = $production->production_line_id;
    $this->product_id = $production->materialStockOut->product_id;
    $this->material_stock_out_id = $production->material_stock_out_id;
    $this->shift = $production->shift;
    $this->size = $production->size;
    $this->surface = $production->surface;
    $this->thickness = $production->thickness;
    $this->outer_diameter = $production->outer_diameter;
    $this->ovality = $production->ovality;

    $this->finishedGoods = $production->productionLengths->map(function ($length) {
      return [
        'id' => $length->id,
        'type' => $length->type,
        'length_m' => $length->length_m,
        'quantity' => $length->quantity
      ];
    })->toArray();

    if (empty($this->finishedGoods)) {
      $this->finishedGoods = [['type' => 'roll', 'length_m' => '', 'quantity' => '']];
    }

    $this->scraps = $production->scrapWastes->map(function ($scrap) {
      return [
        'id' => $scrap->id,
        'quantity' => $scrap->quantity,
        'reason' => $scrap->reason,
        'notes' => $scrap->notes
      ];
    })->toArray();

    if (empty($this->scraps)) {
      $this->scraps = [['quantity' => '', 'reason' => '', 'notes' => '']];
    }
  }

  public function addFinishedGood()
  {
    $this->finishedGoods[] = ['type' => 'roll', 'length_m' => '', 'quantity' => ''];
  }

  public function removeFinishedGood($index)
  {
    unset($this->finishedGoods[$index]);
    $this->finishedGoods = array_values($this->finishedGoods);
  }

  public function addScrap()
  {
    $this->scraps[] = ['quantity' => '', 'reason' => '', 'notes' => ''];
  }

  public function removeScrap($index)
  {
    unset($this->scraps[$index]);
    $this->scraps = array_values($this->scraps);
  }

  public function update()
  {
    $this->validate([
      'production_line_id' => 'required|exists:production_lines,id',
      'product_id' => 'required|exists:products,id',
      'material_stock_out_id' => 'required|exists:material_stock_outs,id',
      'shift' => 'required|string|max:10',
      'size' => 'required|string|max:50',
      'finishedGoods.*.type' => 'required|in:roll,cut',
      'finishedGoods.*.length_m' => 'required|numeric|min:1',
      'finishedGoods.*.quantity' => 'required|integer|min:1',
      'scraps.*.quantity' => 'nullable|numeric|min:0',
      'scraps.*.reason' => 'nullable|string|max:255',
    ]);

    DB::transaction(function () {
      $production = MaterialStockOutLine::findOrFail($this->productionId);

      $production->update([
        'production_line_id' => $this->production_line_id,
        'shift' => $this->shift,
        'size' => $this->size,
        'surface' => $this->surface,
        'thickness' => $this->thickness,
        'outer_diameter' => $this->outer_diameter,
        'ovality' => $this->ovality,
      ]);

      $production->productionLengths()->delete();
      $production->scrapWastes()->delete();

      foreach ($this->finishedGoods as $fg) {
        $product = Product::find($this->product_id);
        $weight_per_meter = $product->weight_per_meter ?? 0;
        $total_weight = $fg['quantity'] * $fg['length_m'] * $weight_per_meter;

        $production->productionLengths()->create([
          'type' => $fg['type'],
          'length_m' => $fg['length_m'],
          'quantity' => $fg['quantity'],
          'total_weight' => $total_weight,
        ]);
      }

      foreach ($this->scraps as $scrap) {
        if ($scrap['quantity']) {
          ScrapWaste::create([
            'material_stock_out_line_id' => $production->id,
            'quantity' => $scrap['quantity'],
            'reason' => $scrap['reason'],
            'waste_date' => now(),
            'recorded_by' => Auth::id(),
            'notes' => $scrap['notes'],
          ]);
        }
      }
    });

    session()->flash('success', 'Production record updated successfully!');
    return redirect()->route('warehouse.production.index');
  }

  public function cancel()
  {
    return redirect()->route('warehouse.production.index');
  }

  #[Layout('components.layouts.app')]
  public function render()
  {
    return view('livewire.warehouse.edit-production', [
      'lines' => ProductionLine::all(),
      'products' => Product::all(),
      'stockOuts' => MaterialStockOut::all(),
    ]);
  }
}
