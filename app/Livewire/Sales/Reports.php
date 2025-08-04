<?php

namespace App\Livewire\Sales;

use App\Models\ProductionOrder;
use App\Models\Delivery;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class Reports extends Component
{
    use WithPagination;

    public $reportType = 'daily';
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->startDate = now()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render()
    {
        $user = Auth::user();

        // Get delivered orders only
        $deliveredOrders = ProductionOrder::with(['customer', 'items.product', 'payments'])
            ->where('status', 'delivered')
            ->when($this->reportType === 'daily', function ($query) {
                $query->whereDate('delivery_date', $this->startDate);
            })
            ->when($this->reportType === 'weekly', function ($query) {
                $query->whereBetween('delivery_date', [
                    Carbon::parse($this->startDate)->startOfWeek(),
                    Carbon::parse($this->endDate)->endOfWeek()
                ]);
            })
            ->when($this->reportType === 'monthly', function ($query) {
                $query->whereYear('delivery_date', Carbon::parse($this->startDate)->year)
                      ->whereMonth('delivery_date', Carbon::parse($this->startDate)->month);
            })
            ->latest()
            ->paginate(15);

        // Get deliveries for the period
        $deliveries = Delivery::with(['customer', 'productionOrder'])
            ->when($this->reportType === 'daily', function ($query) {
                $query->whereDate('delivery_date', $this->startDate);
            })
            ->when($this->reportType === 'weekly', function ($query) {
                $query->whereBetween('delivery_date', [
                    Carbon::parse($this->startDate)->startOfWeek(),
                    Carbon::parse($this->endDate)->endOfWeek()
                ]);
            })
            ->when($this->reportType === 'monthly', function ($query) {
                $query->whereYear('delivery_date', Carbon::parse($this->startDate)->year)
                      ->whereMonth('delivery_date', Carbon::parse($this->startDate)->month);
            })
            ->latest()
            ->paginate(15);

        // Get payments for the period
        $payments = Payment::with(['customer', 'productionOrder'])
            ->when($this->reportType === 'daily', function ($query) {
                $query->whereDate('payment_date', $this->startDate);
            })
            ->when($this->reportType === 'weekly', function ($query) {
                $query->whereBetween('payment_date', [
                    Carbon::parse($this->startDate)->startOfWeek(),
                    Carbon::parse($this->endDate)->endOfWeek()
                ]);
            })
            ->when($this->reportType === 'monthly', function ($query) {
                $query->whereYear('payment_date', Carbon::parse($this->startDate)->year)
                      ->whereMonth('payment_date', Carbon::parse($this->startDate)->month);
            })
            ->latest()
            ->paginate(15);

        // Calculate summary statistics
        $summary = $this->calculateSummary($deliveredOrders, $deliveries, $payments);

        return view('livewire.sales.reports', [
            'deliveredOrders' => $deliveredOrders,
            'deliveries' => $deliveries,
            'payments' => $payments,
            'summary' => $summary,
        ]);
    }

    private function calculateSummary($orders, $deliveries, $payments)
    {
        $totalOrders = $orders->total();
        $totalDeliveries = $deliveries->total();
        $totalPayments = $payments->total();

        $totalOrderValue = $orders->getCollection()->sum(function ($order) {
            return $order->items->sum('total_price');
        });

        $totalDeliveryValue = $deliveries->getCollection()->sum('total_amount');
        $totalPaymentValue = $payments->getCollection()->sum('amount');

        return [
            'total_orders' => $totalOrders,
            'total_deliveries' => $totalDeliveries,
            'total_payments' => $totalPayments,
            'total_order_value' => $totalOrderValue,
            'total_delivery_value' => $totalDeliveryValue,
            'total_payment_value' => $totalPaymentValue,
        ];
    }

    public function updatedReportType()
    {
        $this->resetPage();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }
}
