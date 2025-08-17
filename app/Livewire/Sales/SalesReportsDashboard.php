<?php

namespace App\Livewire\Sales;

use Livewire\Component;

class SalesReportsDashboard extends Component
{
    public $activeTab = 'daily';

    public function render()
    {
        return view('livewire.sales.sales-reports-dashboard');
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }
}
