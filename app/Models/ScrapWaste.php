<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScrapWaste extends Model
{
    use HasFactory;
    protected $table = 'scrap_waste';

    protected $fillable = [
        'material_stock_out_line_id',
        'finished_good_id',
        'scrap_type', // 'raw_material' or 'finished_goods'
        'quantity',
        'reason',
        'waste_date',
        'recorded_by',
        'notes',
        'is_repressible', // Can this scrap be reused?
        'disposal_method', // 'dispose', 'reprocess', 'return_to_supplier'
        'cost',
        'status', // 'pending', 'approved', 'rejected'
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'waste_date' => 'date',
        'is_repressible' => 'boolean',
        'cost' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function materialStockOutLine(): BelongsTo
    {
        return $this->belongsTo(MaterialStockOutLine::class);
    }

    public function finishedGood(): BelongsTo
    {
        return $this->belongsTo(FinishedGood::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scope for raw material scrap
    public function scopeRawMaterialScrap($query)
    {
        return $query->where('scrap_type', 'raw_material');
    }

    // Scope for finished goods scrap
    public function scopeFinishedGoodsScrap($query)
    {
        return $query->where('scrap_type', 'finished_goods');
    }

    // Scope for repressible scrap
    public function scopeRepressible($query)
    {
        return $query->where('is_repressible', true);
    }

    // Scope for non-repressible scrap
    public function scopeNonRepressible($query)
    {
        return $query->where('is_repressible', false);
    }
}