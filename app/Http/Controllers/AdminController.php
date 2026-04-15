<?php

namespace App\Http\Controllers;
use App\Models\Classes;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();

        return view('admin.dashboard', [
            'totalClasses' => Classes::count(),

            // Jumlah jadwal untuk hari ini berdasarkan schedule_date
            'todaySchedules' => Schedule::whereDate('schedule_date', $today)->count(),

            // Jumlah user dengan role 'user'
            'totalUsers' => User::where('role', 'user')->count(),

            // Jumlah user dengan role 'coach'
            'totalCoaches' => User::where('role', 'pelatih')->count(),

            // Membership aktif = tanggal akhir >= hari ini
            'activeMemberships' => Membership::whereDate('end_date', '>=', $today)->count(),

            // Membership tidak aktif
            'inactiveMemberships' => Membership::whereDate('end_date', '<', $today)->count()
            +User::where('role', 'user')->where('is_membership', 0)->count(),
        ]);
    }
}
