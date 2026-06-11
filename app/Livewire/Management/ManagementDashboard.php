<?php

namespace App\Livewire\Management;

use App\Models\ProductionOrder;
use App\Models\Delivery;
use App\Models\ScrapWaste;
use App\Models\FinishedGood;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

class ManagementDashboard extends Component
{
    public $timeFrame = 'month'; // week, month, year

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.management.management-dashboard', [
            'metrics' => $this->calculateMetrics(),
            'recentHighScrap' => $this->getHighScrapBatches(),
            'orderStatus' => $this->getOrderStatusSummary(),
        ]);
    }

    private function calculateMetrics()
    {
        $startDate = match($this->timeFrame) {
            'week' => now()->startOfWeek(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        // 1. OTD (On Time Delivery)
        $deliveredOrders = ProductionOrder::where('status', 'delivered')
            ->where('updated_at', '>=', $startDate)
            ->get();
        
        $onTimeCount = $deliveredOrders->filter(function($order) {
            return $order->updated_at <= ($order->requested_date ?? $order->updated_at);
        })->count();

        $otd = $deliveredOrders->count() > 0 ? ($onTimeCount / $deliveredOrders->count()) * 100 : 100;

        // 2. Scrap Rate
        $totalProducedWeight = FinishedGood::where('production_date', '>=', $startDate)->sum('total_weight') ?: 1;
        $totalScrapWeight = ScrapWaste::where('created_at', '>=', $startDate)->sum('quantity');
        $scrapRate = ($totalScrapWeight / ($totalProducedWeight + $totalScrapWeight)) * 100;

        // 3. Output Volume
        $outputVolume = FinishedGood::where('production_date', '>=', $startDate)->sum('quantity');

        return [
            'otd' => round($otd, 1),
            'scrapRate' => round($scrapRate, 2),
            'outputVolume' => round($outputVolume, 0),
            'onTimeCount' => $onTimeCount,
            'totalDelivered' => $deliveredOrders->count(),
        ];
    }

    private function getHighScrapBatches()
    {
        return ScrapWaste::with('materialStockOutLine.productionLine')
            ->orderBy('quantity', 'desc')
            ->take(5)
            ->get();
    }

    private function getOrderStatusSummary()
    {
        return ProductionOrder::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
    }
}
