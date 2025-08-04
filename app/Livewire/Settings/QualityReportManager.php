<?php

namespace App\Livewire\Settings;

use App\Models\QualityReport;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class QualityReportManager extends Component
{
    use WithPagination;

    public $showForm = false;
    public $editingId = null;
    
    // Form fields
    public $report_type = 'weekly';
    public $start_date;
    public $end_date;
    public $quality_comment;
    public $problems;
    public $corrective_actions;
    public $remarks;
    public $prepared_by;
    public $checked_by;
    public $approved_by;
    public $is_active = true;

    protected $rules = [
        'report_type' => 'required|in:daily,weekly,monthly',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'quality_comment' => 'nullable|string',
        'problems' => 'nullable|string',
        'corrective_actions' => 'nullable|string',
        'remarks' => 'nullable|string',
        'prepared_by' => 'nullable|string',
        'checked_by' => 'nullable|string',
        'approved_by' => 'nullable|string',
        'is_active' => 'boolean',
    ];

    public function mount()
    {
        $this->start_date = Carbon::now()->startOfWeek()->toDateString();
        $this->end_date = Carbon::now()->endOfWeek()->toDateString();
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editingId = null;
    }

    public function edit($id)
    {
        $qualityReport = QualityReport::findOrFail($id);
        $this->editingId = $id;
        $this->report_type = $qualityReport->report_type;
        $this->start_date = $qualityReport->start_date->toDateString();
        $this->end_date = $qualityReport->end_date->toDateString();
        $this->quality_comment = $qualityReport->quality_comment;
        $this->problems = $qualityReport->problems;
        $this->corrective_actions = $qualityReport->corrective_actions;
        $this->remarks = $qualityReport->remarks;
        $this->prepared_by = $qualityReport->prepared_by;
        $this->checked_by = $qualityReport->checked_by;
        $this->approved_by = $qualityReport->approved_by;
        $this->is_active = $qualityReport->is_active;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'report_type' => $this->report_type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'quality_comment' => $this->quality_comment,
            'problems' => $this->problems,
            'corrective_actions' => $this->corrective_actions,
            'remarks' => $this->remarks,
            'prepared_by' => $this->prepared_by,
            'checked_by' => $this->checked_by,
            'approved_by' => $this->approved_by,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            QualityReport::findOrFail($this->editingId)->update($data);
            session()->flash('message', 'Quality report updated successfully.');
        } else {
            QualityReport::create($data);
            session()->flash('message', 'Quality report created successfully.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        QualityReport::findOrFail($id)->delete();
        session()->flash('message', 'Quality report deleted successfully.');
    }

    public function cancel()
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->report_type = 'weekly';
        $this->start_date = Carbon::now()->startOfWeek()->toDateString();
        $this->end_date = Carbon::now()->endOfWeek()->toDateString();
        $this->quality_comment = '';
        $this->problems = '';
        $this->corrective_actions = '';
        $this->remarks = '';
        $this->prepared_by = '';
        $this->checked_by = '';
        $this->approved_by = '';
        $this->is_active = true;
    }

    public function render()
    {
        $qualityReports = QualityReport::orderBy('created_at', 'desc')->paginate(10);
        
        return view('livewire.settings.quality-report-manager', [
            'qualityReports' => $qualityReports
        ]);
    }
} 