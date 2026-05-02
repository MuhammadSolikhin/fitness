<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'status',
        'snap_token',        // Pastikan ini ada
        'midtrans_order_id', // Pastikan ini ada
        'payment_proof',
        'paid_at',
        'class_id',          // <--- TAMBAHKAN INI
        'schedule_id',       // <--- TAMBAHKAN INI
        'class_user_id',
        'membership_id',
        'membership_package' // <--- TAMBAHKAN INI
    ];

    // Relasi
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }
}
