<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\ClassUser;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $membership = Membership::where('user_id', $user->id)
            ->where('end_date', '>=', now())
            ->latest()
            ->first();

        $classUsers = ClassUser::with('schedule')
            ->where('user_id', $user->id)
            ->whereHas('schedule', function ($q) {
                $q->where('schedule_date', '>=', now()->subMonths(6));
            })
            ->get();

        $monthlyProgress = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('M Y');
            $count = $classUsers->filter(function ($cu) use ($i) {
                return Carbon::parse($cu->schedule->schedule_date)->format('Y-m') === now()->subMonths($i)->format('Y-m');
            })->count();
            $monthlyProgress->put($month, $count);
        }

        $pendingClasses = ClassUser::where('user_id', $user->id)
            ->whereHas('schedule', function ($query) {
                $query->where('schedule_date', '<', Carbon::today());
            })
            ->count();

        return view('user.dashboard', [
            'membership' => $membership,
            'monthlyProgress' => $monthlyProgress,
            'totalClasses' => $classUsers->count(),
            'upcomingSchedules' => $classUsers->filter(function ($cu) {
                return $cu->schedule->schedule_date >= now();
            }),
            'pendingClasses' => $pendingClasses,
        ]);
    }


    public function availableClasses()
    {
        $classes = Classes::with('schedules')->get();
        return view('user.kelas.available_classes', compact('classes'));
    }

    public function myClasses()
    {
        $user = Auth::user();
        $myClasses = $user->classes;
        return view('user.kelas.my_classes', compact('myClasses'));
    }

    public function showClass(Classes $class)
    {
        $user = Auth::user();

        // Check if user is enrolled in this class
        $isEnrolled = $user->classes()->where('classes.id', $class->id)->exists();

        if (!$isEnrolled) {
            return redirect()->route('user.classes.my')->with('error', 'Anda belum terdaftar di kelas ini.');
        }

        // Load necessary relationships
        $class->load(['coach', 'schedules']);

        // Get pivot data (schedule info) for this user and class
        // Since a user might enroll in the same class multiple times (different schedules), 
        // we should probably get all enrollments for this class.
        // However, the current pivot structure in `users()` relationship suggests N-to-N.
        // Let's get the specific enrollment details.

        $enrollment = $user->classes()->where('classes.id', $class->id)->first();

        return view('user.kelas.show', compact('class', 'enrollment'));
    }

    public function registerClass(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'schedule_id' => 'required|exists:schedules,id',
        ]);

        $user = Auth::user();
        $classId = $request->class_id;
        $scheduleId = $request->schedule_id;

        // Cek apakah user sudah daftar di class + schedule yg sama
        $alreadyJoined = DB::table('class_user')
            ->where('user_id', $user->id)
            ->where('class_id', $classId)
            ->where('schedule_id', $scheduleId)
            ->exists();

        if ($alreadyJoined) {
            return back()->with('error', 'Anda sudah mendaftar kelas di jadwal ini.');
        }

        $hasMembership = $user->membership && now()->lte($user->membership->end_date);
        $isPaidPerSession = false;

        if ($hasMembership && $user->membership->remaining_sessions > 0) {
            $user->membership->decrement('remaining_sessions');
        } else {
            $isPaidPerSession = true;
        }

        $user->classes()->attach($classId, [
            'schedule_id' => $scheduleId,
            'is_paid_per_session' => $isPaidPerSession,
        ]);

        return back()->with('success', 'Berhasil mendaftar kelas.');
    }

    public function history()
    {
        $payments = Payment::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('user.history', compact('payments'));
    }

    public function payPerSession(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'schedule_id' => 'required|exists:schedules,id',
            'amount' => 'required|integer',
        ]);

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        $orderId = 'PAY-' . time() . '-' . auth()->id();

        $payment = Payment::create([
            'user_id' => auth()->id(),
            'amount' => $request->amount,
            'type' => 'per_class',
            'midtrans_order_id' => $orderId,
            'class_user_id' => null,
            'status' => 'pending',
        ]);

        $payment->schedule_id = $request->schedule_id;
        $payment->class_id = $request->class_id;
        $payment->save();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $request->amount,
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'phone' => '081234567890', // Tambahkan nomor HP dummy untuk mencegah Midtrans error 2603 (QR/GoPay)
            ],
            'item_details' => [
                [
                    'id' => $request->class_id,
                    'price' => (int) $request->amount,
                    'quantity' => 1,
                    'name' => 'Sesi Kelas ' . $request->class_id
                ]
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            $payment->snap_token = $snapToken;
            $payment->save();

            return response()->json(['snap_token' => $snapToken]);

        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage()); // Cek storage/logs/laravel.log
            return response()->json(['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }

    public function cancelPayment($id)
    {
        $payment = Payment::where('user_id', auth()->id())
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        // Ubah status jadi cancelled di database lokal
        $payment->status = 'cancelled';
        $payment->save();

        // (Opsional) Panggil API Cancel Midtrans jika perlu, 
        // tapi untuk Snap cukup ubah di lokal agar user bisa buat order baru.

        return back()->with('success', 'Transaksi berhasil dibatalkan. Silakan buat pesanan baru.');
    }

}
