<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_membership',
        'membership_expired_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_membership' => 'boolean',
            'membership_expired_at' => 'datetime',
        ];
    }

    public function getRedirectRoute()
    {
        return match ($this->role) {
            'admin' => 'admin/dashboard',
            'pelatih' => 'pelatih/dashboard',
            'user' => 'user/dashboard',
            default => 'dashboard',
        };
    }

    // Relasi
    public function membership()
    {
        return $this->hasOne(Membership::class);
    }

    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'class_user', 'user_id', 'class_id')
            ->withPivot('schedule_id', 'is_paid_per_session')
            ->withTimestamps();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function classUsers()
    {
        return $this->hasMany(ClassUser::class);
    }

}
