<?php
namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PelatihController extends Controller
{
    public function dashboard()
    {
        $pelatihId = Auth::id();
        
        $totalSchedules = Schedule::whereHas('class', function ($query) use ($pelatihId) {
            $query->where('coach_id', $pelatihId);
        })->count();

        $totalClasses = Classes::where('coach_id', $pelatihId)->count();

        $classes = Classes::where('coach_id', $pelatihId)->with('users')->get();
        $totalMembers = $classes->flatMap->users->unique('id')->count();

        return view('pelatih.dashboard', compact('totalSchedules', 'totalClasses', 'totalMembers'));
    }

    public function myClasses()
    {
        $pelatihId = Auth::id();

        // Ambil kelas yang dimiliki pelatih ini
        $classes = Classes::where('coach_id', $pelatihId)->get();

        return view('pelatih.kelas_saya', compact('classes'));
    }

    public function show(Classes $class)
    {
        if ($class->coach_id !== auth()->id()) {
            abort(403);
        }

        $users = $class->users;
        $schedules = Schedule::where('class_id', $class->id)->get()->keyBy('id');
        $allUsers = User::where('role', 'user')->get();

        return view('pelatih.show', compact('class', 'users', 'allUsers', 'schedules'));
    }

    public function addUser(Request $request, Classes $class)
    {
        // Validasi umum
        $request->validate([
            'register_type' => 'required|in:existing,new',
            'schedule_id' => 'required|exists:schedules,id',
            'is_paid_per_session' => 'sometimes|boolean',
        ]);

        // Tentukan user_id berdasarkan jenis pendaftaran
        if ($request->register_type === 'existing') {
            $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $userId = $request->user_id;
        } else {
            // Validasi input untuk user baru
            $request->validate([
                'new_name' => 'required|string|max:255',
                'new_email' => 'required|email|unique:users,email',
            ]);

            // Buat user baru
            $user = User::create([
                'name' => $request->new_name,
                'email' => $request->new_email,
                'role' => 'user', // Atur role sebagai user
                'password' => bcrypt('password123'), // Default password, bisa diminta ubah nanti
            ]);

            $userId = $user->id;
        }

        // Cek apakah user sudah terdaftar di kelas dengan jadwal yang sama
        $alreadyExists = $class->users()
            ->where('user_id', $userId)
            ->wherePivot('schedule_id', $request->schedule_id)
            ->exists();

        if ($alreadyExists) {
            return redirect()->back()->withErrors('User sudah terdaftar di kelas dan jadwal ini.');
        }

        // Tambahkan user ke kelas dengan data pivot
        $class->users()->attach($userId, [
            'schedule_id' => $request->schedule_id,
            'is_paid_per_session' => $request->has('is_paid_per_session'),
        ]);

        return redirect()->back()->with('success', 'User berhasil ditambahkan ke kelas.');
    }

    public function updateMembership(Request $request, Classes $class, User $user)
    {
        // Hanya pelatih pemilik kelas yang boleh
        if ($class->coach_id !== auth()->id()) {
            abort(403);
        }

        // Validasi input
        $data = $request->validate([
            'is_paid_per_session' => 'required|boolean',
        ]);

        // Update pivot table
        $class->users()->updateExistingPivot($user->id, [
            'is_paid_per_session' => $data['is_paid_per_session'],
        ]);

        return redirect()->back()->with('success', 'Status membership berhasil diperbarui.');
    }

    public function jadwal()
    {
        return view('pelatih.jadwal');
    }

    public function getEvents(Request $request)
    {
        $schedules = Schedule::with(['class'])
            ->whereHas('class', function ($query) {
                $query->where('coach_id', auth()->id());
            })
            ->get();

        $events = [];

        foreach ($schedules as $schedule) {
            $events[] = [
                'title' => $schedule->class->sanggar_name . ' - ' . $schedule->class->name,
                'start' => Carbon::parse($schedule->schedule_date . ' ' . $schedule->start_time)->toIso8601String(),
                'end' => Carbon::parse($schedule->schedule_date . ' ' . $schedule->end_time)->toIso8601String(),
                'color' => '#28a745',
            ];
        }

        return response()->json($events);
    }

    public function payments()
    {
        $pelatihId = Auth::id();
        $classIds = Classes::where('coach_id', $pelatihId)->pluck('id');
        
        $payments = Payment::with(['user', 'class'])
            ->whereIn('class_id', $classIds)
            ->latest()
            ->paginate(10);
            
        return view('pelatih.payments.index', compact('payments'));
    }
}
