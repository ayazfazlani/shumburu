<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FyaWarehouse extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'finished_good_id',
        'movement_type',
        'quantity',
        'batch_number',
        'purpose',
        'customer_id',
        'movement_date',
        'reference_type',
        'reference_id',
        'notes',
        'created_by',
    ];

    public function finishedGood()
    {
        return $this->belongsTo(FinishedGood::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
