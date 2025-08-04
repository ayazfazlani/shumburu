<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class QualityReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_type', // daily, weekly, monthly
        'start_date',
        'end_date',
        'quality_comment',
        'problems',
        'corrective_actions',
        'remarks',
        'prepared_by',
        'checked_by',
        'approved_by',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function scopeForPeriod($query, $startDate, $endDate, $reportType = 'weekly')
    {
        return $query->where('report_type', $reportType)
                    ->where('start_date', '<=', $endDate)
                    ->where('end_date', '>=', $startDate)
                    ->where('is_active', true)
                    ->orderBy('created_at', 'desc');
    }

    public function scopeForMonth($query, $month, $reportType = 'monthly')
    {
        $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($month . '-01')->endOfMonth();
        
        return $query->where('report_type', $reportType)
                    ->where('start_date', '<=', $endOfMonth)
                    ->where('end_date', '>=', $startOfMonth)
                    ->where('is_active', true)
                    ->orderBy('created_at', 'desc');
    }

    public function scopeForDate($query, $date, $reportType = 'daily')
    {
        return $query->where('report_type', $reportType)
                    ->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date)
                    ->where('is_active', true)
                    ->orderBy('created_at', 'desc');
    }
} 