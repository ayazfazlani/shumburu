<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RawMaterial extends Model
{
    use HasFactory;

    /**
     * When true, the updating observer will NOT create a StockTransaction.
     * Set this to true in StockIn/StockOut components where transactions
     * are already handled by the MaterialStockIn/MaterialStockOut model events.
     */
    public static bool $skipAutoTransaction = false;

    protected $fillable = [
        'name',
        'code',
        'description',
        'unit',
        'is_active',
        'quantity',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'quantity' => 'decimal:2', // Use 2 decimal places to match stock_transactions table
    ];

    protected static function booted()
    {
        // Auto-create StockTransaction when quantity changes (e.g., from Raw Material CRUD edit)
        static::updating(function (RawMaterial $material) {
            if (self::$skipAutoTransaction) {
                return; // Skip — called from StockIn/StockOut which handles transactions itself
            }

            $originalQuantity = round((float) $material->getOriginal('quantity'), 2);
            $newQuantity = round((float) $material->quantity, 2);
            $difference = round($newQuantity - $originalQuantity, 2);

            // Only create a transaction if quantity actually changed (using 2dp comparison)
            if (abs($difference) >= 0.01) {
                $ledgerBalance = round($material->getCurrentBalance(), 2);

                StockTransaction::create([
                    'raw_material_id' => $material->id,
                    'type' => $difference > 0 ? 'in' : 'out',
                    'quantity' => round(abs($difference), 2),
                    'balance_before' => $ledgerBalance,
                    'balance_after' => round($ledgerBalance + $difference, 2),
                    'reference_type' => 'manual_edit',
                    'reference_id' => null,
                    'transaction_date' => now(),
                    'notes' => "Manual edit: quantity changed from {$originalQuantity} to {$newQuantity}",
                ]);
            }
        });
    }

    public function stockIns(): HasMany
    {
        return $this->hasMany(MaterialStockIn::class);
    }

    public function stockOuts(): HasMany
    {
        return $this->hasMany(MaterialStockOut::class);
    }

    public function scrapWaste(): HasMany
    {
        return $this->hasMany(ScrapWaste::class);
    }

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function getBalanceAtDate($date)
    {
        $lastTransaction = $this->transactions()
            ->whereDate('transaction_date', '<=', $date)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        return $lastTransaction ? round((float) $lastTransaction->balance_after, 2) : 0;
    }

    public function getDailyMovements($date)
    {
        return $this->transactions()
            ->whereDate('transaction_date', $date)
            ->get()
            ->groupBy('type')
            ->map(function ($transactions) {
                return $transactions->sum('quantity');
            });
    }

    /**
     * Get the current balance from the latest StockTransaction.
     * Falls back to the quantity field if no transactions exist.
     */
    public function getCurrentBalance(): float
    {
        $latestTransaction = $this->transactions()
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        return $latestTransaction
            ? round((float) $latestTransaction->balance_after, 2)
            : round((float) $this->quantity, 2);
    }

    /**
     * Sync the StockTransaction ledger to match RawMaterial.quantity (actual stock).
     * Creates an adjustment StockTransaction ONLY for the difference.
     * All values rounded to 2dp to match the stock_transactions table precision.
     */
    public function syncStockFromTransactions(): array
    {
        $actualStock = round((float) $this->quantity, 2);
        $ledgerBalance = round($this->getCurrentBalance(), 2);
        $hasTransactions = $this->transactions()->exists();

        // If no transactions exist at all, create an opening balance transaction
        if (! $hasTransactions && $actualStock > 0) {
            StockTransaction::create([
                'raw_material_id' => $this->id,
                'type' => 'in',
                'quantity' => $actualStock,
                'balance_before' => 0,
                'balance_after' => $actualStock,
                'reference_type' => 'sync_opening_balance',
                'reference_id' => null,
                'transaction_date' => now(),
                'notes' => "Sync: Opening balance set to {$actualStock}",
            ]);

            return [
                'material' => $this->name,
                'ledger_balance' => 0,
                'actual_stock' => $actualStock,
                'adjustment' => $actualStock,
                'synced' => true,
                'action' => 'Opening balance created',
            ];
        }

        // Compare with 2dp precision — difference must be >= 0.01 to matter
        $difference = round($actualStock - $ledgerBalance, 2);

        if (abs($difference) >= 0.01) {
            StockTransaction::create([
                'raw_material_id' => $this->id,
                'type' => $difference > 0 ? 'in' : 'out',
                'quantity' => round(abs($difference), 2),
                'balance_before' => $ledgerBalance,
                'balance_after' => $actualStock,
                'reference_type' => 'sync_adjustment',
                'reference_id' => null,
                'transaction_date' => now(),
                'notes' => "Sync adjustment: Ledger was {$ledgerBalance}, actual is {$actualStock} (diff: " . number_format($difference, 2) . ")",
            ]);

            return [
                'material' => $this->name,
                'ledger_balance' => $ledgerBalance,
                'actual_stock' => $actualStock,
                'adjustment' => $difference,
                'synced' => true,
                'action' => ($difference > 0 ? '+' : '') . number_format($difference, 2) . ' adjustment',
            ];
        }

        return [
            'material' => $this->name,
            'ledger_balance' => $ledgerBalance,
            'actual_stock' => $actualStock,
            'adjustment' => 0,
            'synced' => false,
            'action' => 'Already in sync',
        ];
    }

    /**
     * Sync ALL raw materials' StockTransaction ledger to match their actual quantity.
     */
    public static function syncAllStocks(): array
    {
        $results = [];
        foreach (self::all() as $material) {
            $results[] = $material->syncStockFromTransactions();
        }
        return $results;
    }
}
