<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;


class PaymentController extends Controller
{
    // Verifikasi pembayaran manual telah dihapus karena menggunakan Midtrans
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
