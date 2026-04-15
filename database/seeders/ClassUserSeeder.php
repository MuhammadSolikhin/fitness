<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classes;
use App\Models\User;
use App\Models\ClassUser;
use App\Models\Schedule;

class ClassUserSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil kelas pertama
        $class = Classes::first();
        $schedule = Schedule::where('class_id', $class->id)->first();

        if (!$class || !$schedule) {
            $this->command->error('Kelas atau Jadwal tidak ditemukan, seeder gagal.');
            return;
        }

        // Ambil semua user biasa
        $users = User::where('role', 'user')->get();

        foreach ($users as $user) {
            ClassUser::create([
                'user_id' => $user->id,
                'class_id' => $class->id,
                'schedule_id' => $schedule->id,
                'is_paid_per_session' => !$user->is_membership, // Kalau bukan member, harus bayar per pertemuan
            ]);
        }
    }
}
