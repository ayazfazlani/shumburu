<?php

namespace App\Livewire\Operations;

use App\Models\DowntimeRecord as DowntimeRecordModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DowntimeRecord extends Component
{
  public $downtime_date;
  public $start_time;
  public $end_time;
  public $duration_minutes;
  public $reason;
  public $notes;

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
      $start = \Carbon\Carbon::parse($this->start_time);
      $end = \Carbon\Carbon::parse($this->end_time);
      $this->duration_minutes = $start->diffInMinutes($end);
    }
  }

  public function save()
  {
    $this->validate();

    $user = Auth::user();

    // Check if user has operations access
    if (!$user->hasRole(['admin', 'operations', 'plant_manager'])) {
      session()->flash('error', 'Unauthorized to record downtime.');
      return;
    }

    $this->calculateDuration();

    DowntimeRecordModel::create([
      'downtime_date' => $this->downtime_date,
      'start_time' => $this->start_time,
      'end_time' => $this->end_time,
      'duration_minutes' => $this->duration_minutes,
      'reason' => $this->reason,
      'recorded_by' => $user->id,
      'notes' => $this->notes,
    ]);

    session()->flash('message', 'Downtime recorded successfully.');

    $this->reset(['reason', 'notes']);
  }

  public function render()
  {
    return view('livewire.operations.downtime-record');
  }
}