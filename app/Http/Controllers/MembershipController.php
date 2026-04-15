<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Membership;
use Carbon\Carbon;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
// Import Midtrans
use Midtrans\Config;
use Midtrans\Snap;

class MembershipController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $membership = $user->membership;
        $joinedClasses = collect(); // Koleksi kosong default
        $totalSessions = 0;

        if ($membership) {
            $totalSessions = ($membership->package === '2_bulan') ? 24 : 12;

            $joinedClasses = $user->classes()
                ->wherePivot('created_at', '>=', $membership->start_date)
                ->wherePivot('created_at', '<=', $membership->end_date)
                ->orderBy('pivot_created_at', 'asc') 
                ->get();
        }

        return view('user.membership.index', compact('membership', 'joinedClasses', 'totalSessions'));
    }

    public function register(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'package' => 'required|in:1_bulan,2_bulan',
            'amount' => 'required|integer|min:1000',
        ]);

        $user = Auth::user();

        // 2. CEK APAKAH ADA TRANSAKSI PENDING
        $pendingPayment = Payment::where('user_id', $user->id)
            ->where('type', 'membership')
            ->where('status', 'pending')
            ->latest() // Ambil yang paling baru
            ->first();

        // --- PERUBAHAN LOGIKA DISINI ---
        if ($pendingPayment) {
            if ($pendingPayment->snap_token) {
                return response()->json([
                    'snap_token' => $pendingPayment->snap_token,
                    'message' => 'Melanjutkan transaksi pembayaran yang tertunda.'
                ]);
            }
        }
        // -------------------------------

        // 3. Konfigurasi Midtrans (Jika tidak ada pending, buat baru)
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        $orderId = 'MEM-' . time() . '-' . $user->id;

        try {
            $payment = Payment::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'type' => 'membership',
                'membership_package' => $request->package,
                'status' => 'pending',
                'midtrans_order_id' => $orderId,
            ]);

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $request->amount,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                ],
                'item_details' => [
                    [
                        'id' => $request->package,
                        'price' => (int) $request->amount,
                        'quantity' => 1,
                        'name' => 'Membership ' . ucwords(str_replace('_', ' ', $request->package))
                    ]
                ]
            ];

            $snapToken = Snap::getSnapToken($params);

            $payment->snap_token = $snapToken;
            $payment->save();

            return response()->json(['snap_token' => $snapToken]);

        } catch (\Exception $e) {
            Log::error('Membership Payment Error: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal memproses pembayaran: ' . $e->getMessage()], 500);
        }
    }
}