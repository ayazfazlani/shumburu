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
        'purchase_request_id',
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

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    protected static function booted()
    {
        static::created(function ($stockIn) {
            $material = $stockIn->rawMaterial;
            $previousBalance = round($material->getCurrentBalance(), 2);
            $qty = round((float) $stockIn->quantity, 2);

            StockTransaction::create([
                'raw_material_id' => $stockIn->raw_material_id,
                'type' => 'in',
                'quantity' => $qty,
                'balance_before' => $previousBalance,
                'balance_after' => round($previousBalance + $qty, 2),
                'reference_type' => self::class,
                'reference_id' => $stockIn->id,
                'transaction_date' => $stockIn->received_date,
                'notes' => "Stock in: {$qty} units" . ($stockIn->purchase_request_id ? " (GRN for PO)" : ""),
            ]);
        });

        static::updating(function ($stockIn) {
            $originalQuantity = $stockIn->getOriginal('quantity');
            $newQuantity = $stockIn->quantity;

            if ($originalQuantity != $newQuantity) {
                $difference = $newQuantity - $originalQuantity;
                $currentBalance = $stockIn->rawMaterial->getCurrentBalance();
                
                StockTransaction::create([
                    'raw_material_id' => $stockIn->raw_material_id,
                    'type' => $difference > 0 ? 'in' : 'out',
                    'quantity' => abs($difference),
                    'balance_before' => $currentBalance,
                    'balance_after' => $currentBalance + $difference,
                    'reference_type' => self::class,
                    'reference_id' => $stockIn->id,
                    'transaction_date' => now(),
                    'notes' => "Stock-in quantity adjusted from {$originalQuantity} to {$newQuantity}",
                ]);

                $stockIn->last_edited_by = Auth::id();
                $stockIn->last_edited_at = now();
                $stockIn->edit_count = ($stockIn->edit_count ?? 0) + 1;
            }
        });
    }
}
