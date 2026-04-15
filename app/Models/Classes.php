<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Classes extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sanggar_name',
        'description',
        'image_path',
        'coach_id',
    ];

    // Relasi
    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'class_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'class_user', 'class_id', 'user_id')
            ->withPivot('schedule_id', 'is_paid_per_session')
            ->withTimestamps();

    }
}
