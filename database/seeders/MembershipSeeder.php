<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Membership;
use Carbon\Carbon;

class MembershipSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil 5 user biasa
        $users = User::where('role', 'user')->limit(5)->get();

        foreach ($users as $user) {
            // Tambahkan membership aktif
            $startDate = Carbon::now();
            $endDate = (clone $startDate)->addMonths(1);

            Membership::create([
                'user_id' => $user->id,
                'package' => '1_bulan',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'remaining_sessions' => 12,
            ]);

            // Update status di table users
            $user->update([
                'is_membership' => true,
                'membership_expired_at' => $endDate,
            ]);
        }
    }
}
