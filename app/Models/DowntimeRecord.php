<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DowntimeRecord extends Model
{
  use HasFactory;

  protected $fillable = [
    'downtime_date',
    'start_time',
    'end_time',
    'duration_minutes',
    'reason',
    'recorded_by',
    'notes',
  ];

  // Relationships (optional, add if needed)
  public function recordedBy()
  {
    return $this->belongsTo(User::class, 'recorded_by');
  }
}