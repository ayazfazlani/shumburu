<?php

namespace App\Livewire\Production;

use App\Models\ProductionRequest;
use App\Models\MaterialRequest;
use App\Models\ProductionDailySchedule;
use App\Models\ProductionDailyMaterialRequest;
use App\Models\RawMaterial;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Manager extends Component
{
    public $activePlanId;
    public $activePlan;
    public $dailySchedules = [];
    public $selectedDate;
    public $selectedScheduleId;
    public $showMaterialRequestForm = false;
    public $materialQuantities = [];
    public $showProductionForm = false;
    public $productionData = [];
    public $materialRequestsHistory = [];
    public $receivedMaterials = [];

    #[Layout('components.layouts.app')]
    public function render()
    {
        // Get active production plans (approved and scheduled)
        $activePlans = ProductionRequest::with(['product', 'orderItem.productionOrder.customer'])
            ->whereIn('status', ['approved', 'scheduled', 'in_progress'])
            ->where('quantity', '>', 0)
            ->latest()
            ->get();

        // Get today's schedules
        $todaySchedules = ProductionDailySchedule::with(['materialRequests.rawMaterial'])
            ->where('date', today())
            ->whereIn('status', ['materials_requested', 'materials_issued', 'in_production'])
            ->get();

        // Get pending material requests (requested but not yet issued by warehouse)
        $pendingMaterialRequests = ProductionDailyMaterialRequest::with(['rawMaterial', 'dailySchedule'])
            ->where('status', 'pending')
            ->whereDate('request_date', today())
            ->get();

        // Get materials already received today
        $receivedMaterials = ProductionDailyMaterialRequest::with(['rawMaterial', 'dailySchedule'])
            ->where('status', 'issued')
            ->whereDate('request_date', today())
            ->get();

        // Get production history for the week
        $productionHistory = ProductionDailySchedule::with(['productionPlan.product'])
            ->whereBetween('date', [now()->subDays(7), today()])
            ->orderBy('date', 'desc')
            ->get();

        return view('livewire.production.manager', [
            'activePlans' => $activePlans,
            'todaySchedules' => $todaySchedules,
            'pendingMaterialRequests' => $pendingMaterialRequests,
            'receivedMaterials' => $receivedMaterials,
            'productionHistory' => $productionHistory,
        ]);
    }

    public function selectPlan($planId)
    {
        $this->activePlanId = $planId;
        $this->activePlan = ProductionRequest::with(['product', 'product.billOfMaterials.rawMaterial'])
            ->findOrFail($planId);

        $this->loadDailySchedules();
    }

    public function loadDailySchedules()
    {
        // Get or create daily schedules for this plan
        $this->dailySchedules = ProductionDailySchedule::where('production_request_id', $this->activePlanId)
            ->orderBy('date')
            ->get();

        if ($this->dailySchedules->isEmpty()) {
            $this->generateDailySchedules();
        }
    }

    public function generateDailySchedules()
    {
        $totalQuantity = $this->activePlan->quantity;
        $dailyCapacity = 50; // meters per day (can be dynamic from settings)
        $productionDays = ceil($totalQuantity / $dailyCapacity);

        DB::transaction(function () use ($productionDays, $totalQuantity, $dailyCapacity) {
            for ($i = 0; $i < $productionDays; $i++) {
                $remaining = $totalQuantity - ($i * $dailyCapacity);
                $plannedQty = min($dailyCapacity, $remaining);

                ProductionDailySchedule::create([
                    'production_request_id' => $this->activePlanId,
                    'date' => now()->addDays($i),
                    'planned_quantity' => $plannedQty,
                    'remaining_quantity' => $remaining - $plannedQty,
                    'status' => 'pending',
                ]);
            }
        });

        $this->loadDailySchedules();
        session()->flash('success', 'Daily production schedule generated!');
    }

    public function requestMaterialsForDay($scheduleId)
    {
        $schedule = ProductionDailySchedule::findOrFail($scheduleId);
        $this->selectedScheduleId = $scheduleId;
        $this->selectedDate = $schedule->date;

        // Calculate materials needed for this day's production
        $this->calculateDailyMaterialRequirements($schedule);
        $this->showMaterialRequestForm = true;
    }

    public function calculateDailyMaterialRequirements($schedule)
    {
        $product = $this->activePlan->product;
        $dailyQty = $schedule->planned_quantity;

        $this->materialQuantities = [];

        foreach ($product->billOfMaterials as $bom) {
            // Convert from per unit to kg
            $dailyKg = ($bom->quantity_per_unit * $dailyQty) / 1000;

            $this->materialQuantities[$bom->raw_material_id] = [
                'name' => $bom->rawMaterial->name,
                'required' => $dailyKg,
                'available_stock' => $bom->rawMaterial->quantity,
                'requested' => 0,
            ];
        }
    }

    public function submitDailyMaterialRequest()
    {
        DB::transaction(function () {
            $schedule = ProductionDailySchedule::findOrFail($this->selectedScheduleId);

            foreach ($this->materialQuantities as $materialId => $data) {
                if ($data['requested'] > 0) {
                    ProductionDailyMaterialRequest::create([
                        'production_request_id' => $this->activePlanId,
                        'production_daily_schedule_id' => $schedule->id,
                        'raw_material_id' => $materialId,
                        'request_date' => $this->selectedDate,
                        'requested_quantity' => $data['requested'],
                        'status' => 'pending',
                        'requested_by' => Auth::id(),
                        'notes' => "Daily request for production day " . $schedule->date->format('d M Y'),
                    ]);
                }
            }

            $schedule->update(['status' => 'materials_requested']);
            $this->showMaterialRequestForm = false;

            session()->flash('success', 'Material request sent to warehouse for ' . $schedule->date->format('d M Y'));
        });

        $this->loadDailySchedules();
    }

    public function startProduction($scheduleId)
    {
        $schedule = ProductionDailySchedule::findOrFail($scheduleId);

        // Check if all requested materials have been issued
        $pendingMaterials = $schedule->materialRequests()
            ->where('status', '!=', 'issued')
            ->exists();

        if ($pendingMaterials) {
            session()->flash('error', 'Cannot start production. Some materials have not been issued by warehouse yet.');
            return;
        }

        $this->selectedScheduleId = $scheduleId;
        $this->productionData = [
            'start_time' => now()->format('H:i'),
            'actual_quantity' => $schedule->planned_quantity,
            'notes' => '',
        ];
        $this->showProductionForm = true;
    }

    public function recordProduction()
    {
        $this->validate([
            'productionData.start_time' => 'required',
            'productionData.actual_quantity' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () {
            $schedule = ProductionDailySchedule::findOrFail($this->selectedScheduleId);

            $schedule->update([
                'status' => 'completed',
                'actual_quantity' => $this->productionData['actual_quantity'],
                'start_time' => $this->productionData['start_time'],
                'end_time' => now()->format('H:i'),
                'notes' => $this->productionData['notes'] ?? null,
            ]);

            // Update production request remaining quantity
            $productionRequest = ProductionRequest::findOrFail($this->activePlanId);
            $newRemaining = $productionRequest->remaining_quantity - $schedule->planned_quantity;
            $productionRequest->update([
                'remaining_quantity' => max(0, $newRemaining),
                'status' => $newRemaining <= 0 ? 'completed' : 'in_progress',
            ]);

            // Record material consumption
            foreach ($schedule->materialRequests as $materialRequest) {
                $materialRequest->update([
                    'actual_used_quantity' => $materialRequest->requested_quantity,
                    'status' => 'consumed',
                ]);

                // Update raw material stock (already deducted by warehouse when issued)
                // Just track consumption
            }
        });

        $this->showProductionForm = false;
        $this->loadDailySchedules();
        session()->flash('success', 'Production recorded successfully!');
    }

    public function refreshMaterialStatus()
    {
        // Refresh the status of requested materials
        if ($this->selectedScheduleId) {
            $schedule = ProductionDailySchedule::find($this->selectedScheduleId);
            if ($schedule) {
                $this->materialRequestsHistory = $schedule->materialRequests()
                    ->with('rawMaterial')
                    ->get();
            }
        }

        $this->dispatch('$refresh');
    }

    // Auto-refresh every 30 seconds for real-time updates
    public function getListeners()
    {
        return [
            'refreshComponent' => '$refresh',
            'echo:material-requests,MaterialRequestUpdated' => 'refreshMaterialStatus',
        ];
    }
}