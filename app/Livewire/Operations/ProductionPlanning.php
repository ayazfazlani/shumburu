<?php

namespace App\Livewire\Operations;

use App\Models\ProductionRequest;
use App\Models\PurchaseRequest;
use App\Models\RawMaterial;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class ProductionPlanning extends Component
{
    use WithPagination;

    public $showMaterialRequestModal = false;
    public $selectedOrderId;
    public $selectedRequestId;
    public $req_material_id;
    public $req_quantity;
    public $shortageInfo = [];
    public $recentDowntime = [];

    #[Layout('components.layouts.app')]
    public function render()
    {
        // 1. Get orders that have pending production demands
        $ordersWithDemands = \App\Models\ProductionOrder::whereHas('orderItems.productionRequests', function($q) {
                $q->whereIn('status', ['pending', 'approved', 'scheduled']);
            })
            ->with(['customer'])
            ->withCount(['orderItems as pending_requests_count' => function($q) {
                $q->whereHas('productionRequests', function($sq) {
                    $sq->whereIn('status', ['pending', 'approved', 'scheduled']);
                });
            }])
            ->latest()
            ->get();

        // 2. Get direct stock replenishment demands (no order attached)
        $replenishmentRequests = ProductionRequest::with(['product', 'requestedBy'])
            ->whereNull('order_item_id')
            ->whereIn('status', ['pending', 'approved', 'scheduled'])
            ->latest()
            ->get();

        // 3. Global Aggregated Material Demands (Across ALL orders)
        $globalMaterialSummary = \App\Models\MaterialRequest::with('rawMaterial')
            ->whereIn('status', ['pending', 'approved', 'purchase_raised'])
            ->get()
            ->groupBy('raw_material_id')
            ->map(function($group) {
                return [
                    'name' => $group->first()->rawMaterial->name,
                    'unit' => $group->first()->rawMaterial->unit,
                    'total_quantity' => $group->sum('quantity'),
                    'in_stock' => $group->first()->rawMaterial->quantity,
                    'status' => $group->first()->status,
                ];
            });

        // 4. If an order is selected, get its demands
        $selectedOrderDemands = [];
        $viewingOrder = null;
        $aggregatedMaterialSummary = [];

        if ($this->selectedOrderId) {
            $viewingOrder = \App\Models\ProductionOrder::with('customer')->find($this->selectedOrderId);
            $selectedOrderDemands = ProductionRequest::with(['product', 'orderItem', 'requestedBy', 'materialRequests.rawMaterial'])
                ->whereHas('orderItem', function($q) {
                    $q->where('production_order_id', $this->selectedOrderId);
                })
                ->whereIn('status', ['pending', 'approved', 'scheduled'])
                ->get();

            // Aggregated Material Summary for the entire order
            $aggregatedMaterialSummary = \App\Models\MaterialRequest::whereIn('production_request_id', $selectedOrderDemands->pluck('id'))
                ->with('rawMaterial')
                ->get()
                ->groupBy('raw_material_id')
                ->map(function($group) {
                    return [
                        'name' => $group->first()->rawMaterial->name,
                        'unit' => $group->first()->rawMaterial->unit,
                        'total_quantity' => $group->sum('quantity'),
                        'in_stock' => $group->first()->rawMaterial->quantity,
                    ];
                });
        }

        $rawMaterials = \App\Models\RawMaterial::orderBy('name')->get();

        $this->recentDowntime = \App\Models\DowntimeRecord::latest()->take(5)->get();

        return view('livewire.operations.production-planning', [
            'ordersWithDemands' => $ordersWithDemands,
            'replenishmentRequests' => $replenishmentRequests,
            'selectedOrderDemands' => $selectedOrderDemands,
            'viewingOrder' => $viewingOrder,
            'rawMaterials' => $rawMaterials,
            'aggregatedMaterialSummary' => $aggregatedMaterialSummary,
            'globalMaterialSummary' => $globalMaterialSummary,
        ]);
    }

    public function selectOrder($orderId)
    {
        $this->selectedOrderId = $orderId;
    }

    public function backToList()
    {
        $this->selectedOrderId = null;
        $this->shortageInfo = [];
    }

    public function checkShortage($requestId)
    {
        $request = ProductionRequest::with(['product.primaryMaterial'])->findOrFail($requestId);
        $product = $request->product;

        if (!$product->primaryMaterial) {
            $this->shortageInfo[$requestId] = [
                'status' => 'missing_info',
                'message' => 'No primary material assigned to this product.'
            ];
            return;
        }

        $neededKg = $request->quantity * ($product->weight_per_meter ?? 1);
        $availableKg = $product->primaryMaterial->quantity;
        $difference = $availableKg - $neededKg;

        $this->shortageInfo[$requestId] = [
            'status' => $difference >= 0 ? 'ok' : 'shortage',
            'needed' => $neededKg,
            'available' => $availableKg,
            'shortfall' => abs(min(0, $difference)),
            'material_name' => $product->primaryMaterial->name,
            'material_id' => $product->primary_material_id
        ];
    }

    public function openMaterialRequestModal($requestId)
    {
        $this->selectedRequestId = $requestId;
        $request = ProductionRequest::with('product')->find($requestId);
        
        $this->req_material_id = $request->product->primary_material_id ?? '';
        // Calculate a sensible default quantity: (Request Quantity * Product Weight)
        $this->req_quantity = $request->quantity * ($request->product?->weight_per_meter ?? 1);
        $this->showMaterialRequestModal = true;
    }

    public function openPurchaseModal($requestId = null)
    {
        if ($requestId) {
            $this->openMaterialRequestModal($requestId);
        } else {
            $this->selectedRequestId = null;
            $this->req_material_id = '';
            $this->req_quantity = 0;
            $this->showMaterialRequestModal = true;
        }
    }

    public function submitMaterialRequest()
    {
        $this->validate([
            'req_material_id' => 'required|exists:raw_materials,id',
            'req_quantity' => 'required|numeric|min:0.01',
        ]);

        if ($this->selectedRequestId) {
            \App\Models\MaterialRequest::updateOrCreate(
                [
                    'production_request_id' => $this->selectedRequestId,
                    'raw_material_id' => $this->req_material_id,
                    'status' => 'pending',
                ],
                [
                    'quantity' => $this->req_quantity,
                    'requested_by' => Auth::id(),
                    'notes' => "Manual request from Planning Dashboard",
                ]
            );
        } else {
            \App\Models\MaterialRequest::create([
                'raw_material_id' => $this->req_material_id,
                'quantity' => $this->req_quantity,
                'requested_by' => Auth::id(),
                'status' => 'pending',
                'notes' => "Manual replenishment request",
            ]);
        }

        $this->showMaterialRequestModal = false;
        session()->flash('success', 'Material demand created successfully!');
    }

    public function requestMaterials($requestId)
    {
        $this->openMaterialRequestModal($requestId);
    }

    public function updateStatus($requestId, $status)
    {
        $request = ProductionRequest::findOrFail($requestId);
        $request->update(['status' => $status]);
        session()->flash('success', "Request #$requestId marked as $status.");
    }
}
