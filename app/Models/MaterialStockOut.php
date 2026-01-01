<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

use function Symfony\Component\Clock\now;

class MaterialStockOut extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_material_id',
        'quantity',
        'batch_number',
        'issued_date',
        'issued_by',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'issued_date' => 'date',
    ];

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function productionLines()
    {
        return $this->belongsToMany(ProductionLine::class, 'material_stock_out_line')
            ->withPivot('quantity_consumed')
            ->withTimestamps();
    }

    public function MaterialStockOutLine()
    {
        return $this->hasMany(MaterialStockOutLine::class);
    }

    protected static function booted()
    {
        static::created(function ($stockOut) {
            $material = $stockOut->rawMaterial;
            $previousBalance = $material->getBalanceAtDate($stockOut->issued_date->subDay());

            StockTransaction::create([
                'raw_material_id' => $stockOut->raw_material_id,
                'type' => 'out',
                'quantity' => $stockOut->quantity,
                'balance_before' => $previousBalance,
                'balance_after' => $previousBalance - $stockOut->quantity,
                'reference_type' => self::class,
                'reference_id' => $stockOut->id,
                'transaction_date' => $stockOut->issued_date,
                'notes' => "Stock out: {$stockOut->quantity} units",
            ]);

            // Update current stock
            // $material->decrement('quantity', $stockOut->quantity);
        });

        // When UPDATED (edited)
        static::updating(function ($stockIn) {
            // Get the original values before update
            $originalQuantity = $stockIn->getOriginal('quantity');
            $newQuantity = $stockIn->quantity;

            // If quantity changed
            if ($originalQuantity != $newQuantity) {
                $difference = $newQuantity - $originalQuantity;

                // Create adjustment transaction
                StockTransaction::create([
                    'raw_material_id' => $stockIn->raw_material_id,
                    'type' => $difference > 0 ? 'in' : 'out',
                    'quantity' => abs($difference),
                    'balance_before' => $stockIn->rawMaterial->getBalanceAtDate(now()),
                    'balance_after' => $stockIn->rawMaterial->getBalanceAtDate(now()) + $difference,
                    'reference_type' => self::class,
                    'reference_id' => $stockIn->id,
                    'transaction_date' => now(),
                    'notes' => "Quantity adjusted from {$originalQuantity} to {$newQuantity}",
                    // 'is_adjustment' => true,
                ]);

                // // Update edit tracking
                // $stockIn->last_edited_by = Auth::id();
                // $stockIn->last_edited_at = now();
                // $stockIn->edit_count = ($stockIn->edit_count ?? 0) + 1;
            }
        });

        // // After update, refresh material balance
        // static::updated(function ($stockIn) {
        //     if ($stockIn->wasChanged('quantity')) {
        //         $stockIn->rawMaterial->recalculateBalance();
        //     }
        // });

    }

    /**
     * Create stock transaction
     */
    private function createStockTransaction($stockIn, $type)
    {
        $previousBalance = $stockIn->rawMaterial->getBalanceAtDate(
            $stockIn->received_date->subDay()
        );

        StockTransaction::create([
            'raw_material_id' => $stockIn->raw_material_id,
            'type' => $type,
            'quantity' => $stockIn->quantity,
            'balance_before' => $previousBalance,
            'balance_after' => $previousBalance + $stockIn->quantity,
            'reference_type' => self::class,
            'reference_id' => $stockIn->id,
            'transaction_date' => $stockIn->received_date,
            'notes' => $stockIn->notes ?? "Stock {$type}: {$stockIn->quantity} units",
        ]);
    }
}
