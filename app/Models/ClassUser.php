<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ClassUser extends Pivot
{
    protected $table = 'class_user';

    protected $fillable = [
        'user_id',
        'class_id',
        'schedule_id',
        'is_paid_per_session',
    ];

    protected $casts = [
        'is_paid_per_session' => 'boolean',
    ];

    // ClassUser.php
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

}
