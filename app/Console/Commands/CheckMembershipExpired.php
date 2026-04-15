<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Membership;
use Carbon\Carbon;

class CheckMembershipExpired extends Command
{
    protected $signature = 'membership:check-expired';
    protected $description = 'Set non-active for expired memberships';

    public function handle(): void
    {
        $today = Carbon::today();

        // Cek semua user membership
        $users = User::where('is_membership', true)
            ->whereNotNull('membership_expired_at')
            ->get();

        foreach ($users as $user) {
            if ($user->membership_expired_at < $today) {
                $user->is_membership = false;
                $user->membership_expired_at = null;
                $user->save();

                $this->info("Membership expired untuk user: {$user->email}");
            }
        }

        $this->info('Membership checking selesai.');
    }
}
