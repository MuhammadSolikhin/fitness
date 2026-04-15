<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;


class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('user', 'membership')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.payments.index', compact('payments'));
    }


    public function verify(Payment $payment)
    {
        $payment->update(['status' => 'verified']);

        if ($payment->type === 'membership') {
            $user = $payment->user;

            $package = '1_bulan';
            $remainingSessions = 12;
            $amount = $payment->amount;

            if ($amount == 200000) {
                $package = '2_bulan';
                $remainingSessions = 24;
            }

            $startDate = Carbon::now();
            $endDate = $package === '1_bulan'
                ? $startDate->copy()->addMonth()
                : $startDate->copy()->addMonths(2);

            $existingMembership = $user->membership;
            if ($existingMembership) {
                $existingMembership->delete();
            }

            $membership = Membership::create([
                'user_id' => $user->id,
                'package' => $package,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'remaining_sessions' => $remainingSessions,
            ]);

            $user->update([
                'is_membership' => 1,
                'membership_expired_at' => $endDate,
            ]);

            $payment->update([
                'membership_id' => $membership->id,
            ]);
        }

        return back()->with('success', 'Pembayaran berhasil diverifikasi dan membership diaktifkan.');
    }

    public function reject(Payment $payment)
    {
        $payment->update(['status' => 'rejected']);
        return back()->with('error', 'Pembayaran ditolak.');
    }

    public function history(Request $request)
    {
        $query = Payment::with('user', 'membership')->where('status', '!=', 'pending');

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $payments = $query->latest()->paginate(10)->withQueryString();

        return view('admin.payments.history', compact('payments'));
    }

}
