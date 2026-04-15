<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Wajib import Log
use Midtrans\Config;
use Midtrans\Notification;

class PaymentCallbackController extends Controller
{
    public function callback(Request $request)
    {
        // 1. Setup Konfigurasi
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        try {
            // 2. Tangkap Notifikasi
            $notif = new Notification();
        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            return response()->json(['message' => 'Invalid notification'], 400);
        }

        // 3. Ambil Data Penting
        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $order_id = $notif->order_id;

        // Log untuk debugging (Cek storage/logs/laravel.log)
        Log::info("Midtrans Callback Masuk: Order ID $order_id Status: $transaction");

        // 4. Cari Data Payment di Database
        $payment = Payment::where('midtrans_order_id', $order_id)->first();

        if (!$payment) {
            Log::error("Payment not found for Order ID: $order_id");
            return response()->json(['message' => 'Payment not found'], 404);
        }

        // 5. Logika Update Status
        if ($transaction == 'capture' || $transaction == 'settlement') {
            // SUKSES
            $payment->status = 'success';
            $payment->paid_at = now();
            $payment->save();

            if ($payment->type == 'membership') {
                $user = User::find($payment->user_id);

                if ($user) {
                    // Tentukan Durasi & Sesi berdasarkan paket
                    $monthsToAdd = ($payment->membership_package == '2_bulan') ? 2 : 1;
                    $sessionsToAdd = ($payment->membership_package == '2_bulan') ? 24 : 12;

                    // Cek apakah user sudah punya membership aktif/expired
                    $membership = \App\Models\Membership::where('user_id', $user->id)->first();

                    if ($membership) {
                        // UPDATE MEMBERSHIP LAMA
                        // Jika masih aktif, tambah durasi dari tanggal berakhir sebelumnya
                        if ($membership->end_date > now()) {
                            $membership->end_date = $membership->end_date->addMonths($monthsToAdd);
                        } else {
                            // Jika sudah expired, mulai baru dari sekarang
                            $membership->start_date = now();
                            $membership->end_date = now()->addMonths($monthsToAdd);
                        }

                        // Update Paket & Tambah Sisa Sesi
                        $membership->package = $payment->membership_package;
                        $membership->remaining_sessions += $sessionsToAdd; // Akumulasi sesi
                        $membership->save();
                    } else {
                        // BUAT MEMBERSHIP BARU
                        \App\Models\Membership::create([
                            'user_id' => $user->id,
                            'package' => $payment->membership_package,
                            'start_date' => now(),
                            'end_date' => now()->addMonths($monthsToAdd),
                            'remaining_sessions' => $sessionsToAdd,
                        ]);
                    }
                }
            } else if ($payment->type == 'per_class') {
                $user = User::find($payment->user_id);
                if ($user && $payment->class_id && $payment->schedule_id) {
                    // Cek agar tidak duplikat
                    $exists = \DB::table('class_user')
                        ->where('user_id', $user->id)
                        ->where('schedule_id', $payment->schedule_id)
                        ->exists();

                    if (!$exists) {
                        $user->classes()->attach($payment->class_id, [
                            'schedule_id' => $payment->schedule_id,
                            'is_paid_per_session' => true,
                        ]);
                        Log::info("User {$user->name} berhasil didaftarkan ke kelas.");
                    }
                }
            }

        } else if ($transaction == 'pending') {
            $payment->status = 'pending';
            $payment->save();
        } else if ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
            $payment->status = 'failed';
            $payment->save();
        }

        return response()->json(['message' => 'Notification processed']);
    }
}