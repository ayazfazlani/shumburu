<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class MaterialStockIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_material_id',
        'quantity',
        'batch_number',
        'received_date',
        'received_by',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'received_date' => 'date',
    ];

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    protected static function booted()
    {
        static::created(function ($stockIn) {
            $material = $stockIn->rawMaterial;
            $previousBalance = $material->getBalanceAtDate($stockIn->received_date->subDay());

            StockTransaction::create([
                'raw_material_id' => $stockIn->raw_material_id,
                'type' => 'in',
                'quantity' => $stockIn->quantity,
                'balance_before' => $previousBalance,
                'balance_after' => $previousBalance + $stockIn->quantity,
                'reference_type' => self::class,
                'reference_id' => $stockIn->id,
                'transaction_date' => $stockIn->received_date,
                'notes' => "Stock in: {$stockIn->quantity} units",
            ]);

            // Update current stock
            // $material->increment('quantity', $stockIn->quantity);

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
                    'balance_before' => $stockIn->rawMaterial->getCurrentBalance(),
                    'balance_after' => $stockIn->rawMaterial->getCurrentBalance() + $difference,
                    'reference_type' => self::class,
                    'reference_id' => $stockIn->id,
                    'transaction_date' => now(),
                    'notes' => "Quantity adjusted from {$originalQuantity} to {$newQuantity}",
                    // 'is_adjustment' => true,
                ]);

                // Update edit tracking
                $stockIn->last_edited_by = Auth::id();
                $stockIn->last_edited_at = now();
                $stockIn->edit_count = ($stockIn->edit_count ?? 0) + 1;
            }
        });
    }
}
