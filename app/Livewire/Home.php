<?php

namespace App\Livewire;

use App\Models\Payment;
use App\Models\ProductionOrder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Home extends Component
{
    public $timeRange = 'today';

    public $loading = false;

    public $salesStats = [];

    public $revenueTrend;

    protected $listeners = ['refreshDashboard' => 'refreshData'];

    public function mount()
    {
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->loading = true;

        $now = now();
        $today = $now->copy()->startOfDay();
        $yesterday = $now->copy()->subDay()->startOfDay();
        $yesterdayEnd = $now->copy()->subDay()->endOfDay();

        // Real data calculations
        $totalRevenue = Payment::sum('amount') ?? 0;
        $todayRevenue = Payment::whereDate('payment_date', $today)->sum('amount') ?? 0;
        $yesterdayRevenue = Payment::whereBetween('payment_date', [$yesterday, $yesterdayEnd])->sum('amount') ?? 0;

        $totalOrders = ProductionOrder::count();
        $todayOrders = ProductionOrder::whereDate('created_at', $today)->count();
        $yesterdayOrders = ProductionOrder::whereBetween('created_at', [$yesterday, $yesterdayEnd])->count();

        $pendingOrders = ProductionOrder::where('status', 'pending')->count();
        $yesterdayPending = ProductionOrder::where('status', 'pending')
            ->whereBetween('created_at', [$yesterday, $yesterdayEnd])
            ->count();

        $completedOrders = ProductionOrder::where('status', 'completed')->count();
        $deliveredOrders = ProductionOrder::where('status', 'delivered')->count();
        $inProductionOrders = ProductionOrder::where('status', 'in_production')->count();
        
        $completionRate = $totalOrders > 0 
            ? (($completedOrders + $deliveredOrders) / $totalOrders) * 100 
            : 0;
        $yesterdayCompletionRate = $yesterdayOrders > 0 
            ? (ProductionOrder::whereIn('status', ['completed', 'delivered'])
                ->whereBetween('created_at', [$yesterday, $yesterdayEnd])
                ->count() / $yesterdayOrders) * 100 
            : 0;

        // Calculate trends
        $revenueChange = $yesterdayRevenue > 0 
            ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100 
            : ($todayRevenue > 0 ? 100 : 0);
        
        $ordersChange = $yesterdayOrders > 0 
            ? (($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100 
            : ($todayOrders > 0 ? 100 : 0);

        $pendingChange = $yesterdayPending > 0 
            ? (($pendingOrders - $yesterdayPending) / $yesterdayPending) * 100 
            : ($pendingOrders > 0 ? 100 : 0);

        $completionChange = $yesterdayCompletionRate > 0 
            ? ($completionRate - $yesterdayCompletionRate) 
            : ($completionRate > 0 ? $completionRate : 0);

        $this->salesStats = [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'in_production' => $inProductionOrders,
            'completed' => $completedOrders,
            'delivered' => $deliveredOrders,
            'completion_rate' => $completionRate,
        ];

        // Revenue trend for last 30 days - use DB query to avoid Eloquent model issues
        $revenueTrendData = DB::table('payments')
            ->select(
                DB::raw('DATE(payment_date) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->where('payment_date', '>=', $now->copy()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $this->revenueTrend = $revenueTrendData;

        $this->loading = false;
    }

    public function updatedTimeRange()
    {
        $this->refreshData();
    }


    #[Layout('components.layouts.app')]
    public function render(): View
    {
        return view('livewire.home', [
            'salesStats' => $this->salesStats,
            'revenueTrend' => $this->revenueTrend,
        ]);
    }
}
