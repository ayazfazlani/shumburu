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

    public $syncResults = [];

    public $showSyncResults = false;

    public function mount()
    {
        abort_unless(auth()->user()->can('reports.raw-material-stock-balance-report'), 403);
        $this->date = Carbon::today()->toDateString();

        // Check if we need to show initialization button
        $this->showInitialization = ! StockTransaction::exists();
    }

    /**
     * Sync stock levels: recalculate RawMaterial.quantity from StockTransaction ledger.
     * Does NOT modify any transaction history.
     */
    public function syncStock()
    {
        $this->syncResults = RawMaterial::syncAllStocks();
        $this->showSyncResults = true;

        $syncedCount = collect($this->syncResults)->where('synced', true)->count();

        if ($syncedCount > 0) {
            session()->flash('success', "Stock synced! {$syncedCount} material(s) had their quantity corrected to match transaction history.");
        } else {
            session()->flash('info', 'All stock levels are already in sync with transaction history.');
        }
    }

    public function dismissSyncResults()
    {
        $this->showSyncResults = false;
        $this->syncResults = [];
    }

    public function initializeStockData()
    {
        // Get all raw materials with current stock
        $materials = RawMaterial::all();

        foreach ($materials as $material) {
            // Check if this material has any transactions
            $hasTransactions = $material->transactions()->exists();

            if (! $hasTransactions && $material->quantity > 0) {
                // Create initial transaction (today) - simple opening balance
                StockTransaction::create([
                    'raw_material_id' => $material->id,
                    'type' => 'in',
                    'quantity' => $material->quantity,
                    'balance_before' => 0,
                    'balance_after' => $material->quantity,
                    'reference_type' => 'initial_setup',
                    'reference_id' => null,
                    'transaction_date' => now(),
                    'notes' => 'Initial stock setup - opening balance',
                ]);
            }
        }

        $this->showInitialization = false;
        session()->flash('success', 'Stock data initialized! Beginning balances are now available.');
    }

    public function render()
    {
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

            // Current stock from the RawMaterial table (may be out of sync)
            $currentStock = (float) $material->quantity;

            // If no transactions found, use current stock as fallback
            $hasTransactions = StockTransaction::where('raw_material_id', $material->id)->exists();

            if (! $hasTransactions && $currentStock > 0) {
                $beginningBalance = $currentStock;
                $endingBalance = $currentStock;
            }

            // Check if there's a mismatch between transaction ledger and stored quantity
            $stockMismatch = $hasTransactions ? abs($endingBalance - $currentStock) > 0.01 : false;

            $rows[] = [
                'name' => $material->name,
                'beginning' => $beginningBalance,
                'addition' => $dailyMovements['in'],
                'out' => $dailyMovements['out'],
                'return' => $dailyMovements['return'],
                'waste' => $dailyMovements['waste'],
                'ending' => $endingBalance,
                'current_stock' => $currentStock,
                'has_transactions' => $hasTransactions,
                'stock_mismatch' => $stockMismatch,
                'min_stock' => $material->min_stock ?? 100,
                'remark' => $this->getMaterialRemark($beginningBalance, $dailyMovements, $endingBalance, $currentStock, $hasTransactions, $stockMismatch),
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

    private function getMaterialRemark($beginning, $movements, $ending, $currentStock, $hasTransactions, $stockMismatch)
    {
        // If no transactions exist
        if (! $hasTransactions) {
            return 'No transactions — click Initialize or Sync';
        }

        // If stock mismatch detected
        if ($stockMismatch) {
            $diff = abs($ending - $currentStock);
            return "⚠ MISMATCH: Ledger={$ending}, Actual={$currentStock} (diff: " . number_format($diff, 2) . 'kg) — click Sync';
        }

        // Check calculated vs recorded ending balance
        $calculatedEnding = $beginning + $movements['in'] + $movements['return'] - $movements['out'] - $movements['waste'];
        $discrepancy = abs($calculatedEnding - $ending);

        if ($discrepancy > 0.01) {
            return '⚠ Discrepancy: ' . number_format($discrepancy, 2) . 'kg difference';
        }

        if ($ending < 0) {
            return '🚫 NEGATIVE STOCK';
        }

        return '✓ OK';
    }
}
