<?php

// app/Livewire/Reports/RawMaterialStockBalanceReport.php

namespace App\Livewire\Reports;

use App\Models\RawMaterial;
use App\Models\StockTransaction;
use Carbon\Carbon;
use Livewire\Component;

class RawMaterialStockBalanceReport extends Component
{
    public $date;

    public $raw_material_id = '';

    public $showInitialization = false;

    public function mount()
    {
        $this->date = Carbon::today()->toDateString();

        // Check if we need to show initialization button
        $this->showInitialization = ! StockTransaction::exists();
    }

    public function initializeStockData()
    {
        // Get all raw materials with current stock
        $materials = RawMaterial::all();

        foreach ($materials as $material) {
            // Check if this material has any transactions
            $hasTransactions = $material->transactions()->exists();

            if (! $hasTransactions && $material->quantity > 0) {
                // Create initial transaction (30 days ago)
                StockTransaction::create([
                    'raw_material_id' => $material->id,
                    'type' => 'in',
                    'quantity' => $material->quantity,
                    'balance_before' => 0,
                    'balance_after' => $material->quantity,
                    'reference_type' => 'initial_setup',
                    'reference_id' => null,
                    'transaction_date' => now()->subDays(30),
                    'notes' => 'Initial stock setup',
                ]);

                // Create transactions for the last 30 days (to build history)
                for ($i = 29; $i >= 0; $i--) {
                    $transactionDate = now()->subDays($i);
                    StockTransaction::create([
                        'raw_material_id' => $material->id,
                        'type' => 'in',
                        'quantity' => 0,
                        'balance_before' => $material->quantity,
                        'balance_after' => $material->quantity,
                        'reference_type' => 'daily_balance',
                        'reference_id' => null,
                        'transaction_date' => $transactionDate,
                        'notes' => 'Daily balance record',
                    ]);
                }
            }
        }

        $this->showInitialization = false;
        session()->flash('success', 'Stock data initialized! Beginning balances are now available.');
    }

    public function render()
    {
        // $data = $this->initializeStockData();
        // dd($data);
        // Show initialization message if no data
        if ($this->showInitialization) {
            return view('livewire.reports.raw-material-stock-balance-report', [
                'rows' => [],
                'date' => $this->date,
                'allMaterials' => RawMaterial::orderBy('name')->get(),
                'showInitialization' => true,
            ]);
        }

        $date = Carbon::parse($this->date);
        $previousDate = $date->copy()->subDay();

        // Get materials
        $materialsQuery = RawMaterial::query();

        if ($this->raw_material_id) {
            $materialsQuery->where('id', $this->raw_material_id);
        }

        $materials = $materialsQuery->orderBy('name')->get();
        $allMaterials = RawMaterial::orderBy('name')->get();

        $rows = [];
        foreach ($materials as $material) {
            // Get beginning balance (previous day's ending balance)
            $beginningBalance = $this->getBalanceAtDate($material->id, $previousDate);

            // Get daily movements
            $dailyMovements = $this->getDailyMovements($material->id, $date);

            // Get ending balance (today's balance)
            $endingBalance = $this->getBalanceAtDate($material->id, $date);

            // If no transactions found, use current stock as fallback
            if ($beginningBalance == 0 && $endingBalance == 0 && $material->quantity > 0) {
                $beginningBalance = $material->quantity;
                $endingBalance = $material->quantity;
            }

            $rows[] = [
                'name' => $material->name,
                'beginning' => $beginningBalance,
                'addition' => $dailyMovements['in'],
                'out' => $dailyMovements['out'],
                'return' => $dailyMovements['return'],
                'waste' => $dailyMovements['waste'],
                'ending' => $endingBalance,
                'min_stock' => $material->min_stock ?? 100,
                'remark' => $this->getMaterialRemark($beginningBalance, $dailyMovements, $endingBalance, $material->quantity),
            ];
        }

        return view('livewire.reports.raw-material-stock-balance-report', [
            'rows' => $rows,
            'date' => $this->date,
            'allMaterials' => $allMaterials,
            'showInitialization' => false,
        ]);
    }

    private function getBalanceAtDate($materialId, $date)
    {
        $transaction = StockTransaction::where('raw_material_id', $materialId)
            ->whereDate('transaction_date', '<=', $date)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        return $transaction ? $transaction->balance_after : 0;
    }

    private function getDailyMovements($materialId, $date)
    {
        $movements = StockTransaction::where('raw_material_id', $materialId)
            ->whereDate('transaction_date', $date)
            ->where('quantity', '>', 0) // Only actual movements, not balance records
            ->get();

        return [
            'in' => $movements->where('type', 'in')->sum('quantity'),
            'out' => $movements->where('type', 'out')->sum('quantity'),
            'return' => $movements->where('type', 'return')->sum('quantity'),
            'waste' => $movements->where('type', 'waste')->sum('quantity'),
        ];
    }

    private function getMaterialRemark($beginning, $movements, $ending, $currentStock)
    {
        // If we're using fallback data (no transactions)
        if ($beginning == $currentStock && $ending == $currentStock) {
            return 'Using current stock - initialize transactions';
        }

        $calculatedEnding = $beginning + $movements['in'] + $movements['return'] - $movements['out'] - $movements['waste'];
        $discrepancy = abs($calculatedEnding - $ending);

        if ($discrepancy > 0.01) {
            return 'Check: '.number_format($discrepancy, 2).'kg difference';
        }

        if ($ending < 0) {
            return 'NEGATIVE STOCK';
        }

        return 'OK';
    }
}
