<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Payment::with(['user', 'class', 'schedule'])->get();
    }

    public function headings(): array
    {
        return ['ID', 'Nama User', 'Order ID (Midtrans)', 'Nominal', 'Tipe', 'Paket Membership', 'Kelas', 'Jadwal', 'Status', 'Dibayar Pada', 'Dibuat Pada'];
    }

    public function map($payment): array
    {
        return [
            $payment->id,
            $payment->user ? $payment->user->name : '-',
            $payment->midtrans_order_id ?? '-',
            $payment->amount,
            $payment->type,
            $payment->membership_package ?? '-',
            $payment->class ? $payment->class->name : '-',
            $payment->schedule ? $payment->schedule->schedule_date . ' ' . $payment->schedule->start_time : '-',
            $payment->status,
            $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('Y-m-d H:i:s') : '-',
            $payment->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
