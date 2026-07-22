<?php

namespace App\Livewire\Operations;

use App\Models\DowntimeRecord as DowntimeRecordModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DowntimeRecord extends Component
{
    use WithPagination;

    // Form properties
    public $downtime_date;
    public $start_time;
    public $end_time;
    public $duration_minutes;
    public $reason;
    public $notes;

    // Edit state
    public $isEditing = false;
    public $editId = null;

    // Stats properties
    public $totalEvents = 0;
    public $todayEvents = 0;
    public $totalDuration = 0;
    public $avgDuration = 0;
    public $costPerMinute = 0;
    public $productionLoss = 0;

    protected $rules = [
        'downtime_date' => 'required|date',
        'start_time' => 'required',
        'end_time' => 'required|after:start_time',
        'reason' => 'required|string|max:255',
        'notes' => 'nullable|string',
    ];

    public function mount()
    {
        $this->downtime_date = now()->format('Y-m-d');
        $this->start_time = now()->format('H:i');
        $this->end_time = now()->addHour()->format('H:i');
        $this->calculateDuration();
        $this->loadStats();
    }

    public function updatedStartTime()
    {
        $this->calculateDuration();
    }

    public function updatedEndTime()
    {
        $this->calculateDuration();
    }

    public function calculateDuration()
    {
        if ($this->start_time && $this->end_time) {
            try {
                $start = \Carbon\Carbon::parse($this->start_time);
                $end = \Carbon\Carbon::parse($this->end_time);
                $this->duration_minutes = $start->diffInMinutes($end);
            } catch (\Exception $e) {
                $this->duration_minutes = 0;
            }
        }
    }

    public function loadStats()
    {
        $this->totalEvents = DowntimeRecordModel::count();
        $this->todayEvents = DowntimeRecordModel::whereDate('downtime_date', today())->count();
        $this->totalDuration = DowntimeRecordModel::sum('duration_minutes') ?? 0;
        $this->avgDuration = $this->totalEvents > 0 ? round($this->totalDuration / $this->totalEvents) : 0;
        $this->costPerMinute = 5.00; // Example: $5 per minute downtime cost
        $this->productionLoss = $this->totalDuration * 2; // Example: 2 units per minute
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();

        // Check if user has operations access
        if (!$user->hasRole(['Admin', 'Super Admin', 'Plant Manager'])) {
            session()->flash('error', 'Unauthorized to record downtime.');
            return;
        }

        $this->calculateDuration();

        $data = [
            'downtime_date' => $this->downtime_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'duration_minutes' => $this->duration_minutes,
            'reason' => $this->reason,
            'recorded_by' => $user->id,
            'notes' => $this->notes,
        ];

        if ($this->isEditing && $this->editId) {
            $record = DowntimeRecordModel::find($this->editId);
            if ($record) {
                $record->update($data);
                session()->flash('message', 'Downtime record updated successfully.');
            }
        } else {
            DowntimeRecordModel::create($data);
            session()->flash('message', 'Downtime recorded successfully.');
        }

        $this->resetForm();
        $this->loadStats();
    }

    public function edit($id)
    {
        $record = DowntimeRecordModel::find($id);
        if ($record) {
            $this->isEditing = true;
            $this->editId = $id;
            $this->downtime_date = $record->downtime_date;
            $this->start_time = $record->start_time;
            $this->end_time = $record->end_time;
            $this->duration_minutes = $record->duration_minutes;
            $this->reason = $record->reason;
            $this->notes = $record->notes;
        }
    }

    public function delete($id)
    {
        $record = DowntimeRecordModel::find($id);
        if ($record) {
            $record->delete();
            session()->flash('message', 'Downtime record deleted successfully.');
            $this->loadStats();
        }
    }

    public function cancel()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->editId = null;
    }

    public function resetForm()
    {
        $this->downtime_date = now()->format('Y-m-d');
        $this->start_time = now()->format('H:i');
        $this->end_time = now()->addHour()->format('H:i');
        $this->duration_minutes = 0;
        $this->reason = '';
        $this->notes = '';
        $this->isEditing = false;
        $this->editId = null;
        $this->resetValidation();
        $this->calculateDuration();
    }

    public function render()
    {
        $recentRecords = DowntimeRecordModel::with('recordedBy')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('livewire.operations.downtime-record', [
            'recentRecords' => $recentRecords,
        ]);
    }
}
