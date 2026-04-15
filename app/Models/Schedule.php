<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'day_of_week',
        'schedule_date',
        'start_time',
        'end_time',
    ];

    // Relasi
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
}
