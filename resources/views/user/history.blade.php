@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')

@section('content')
    <div class="container mt-4">

        @if ($payments->isEmpty())
            <div class="alert alert-info">Belum ada riwayat pembayaran yang tersedia.</div>
        @else
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Jenis</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                    <th>Info / Bukti</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $payment)
                                    <tr>
                                        <td>
                                            <small class="text-muted">
                                                {{ $payment->midtrans_order_id ?? '#' . $payment->id }}
                                            </small>
                                        </td>
                                        <td>
                                            {{-- Ubah 'per_class' jadi 'Per Kelas' --}}
                                            {{ ucwords(str_replace('_', ' ', $payment->type)) }}
                                        </td>
                                        <td>
                                            <strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            @php
                                                // Normalisasi status ke huruf kecil
                                                $status = strtolower($payment->status);
                                            @endphp

                                            @if (in_array($status, ['success', 'settlement', 'capture', 'paid', 'verified']))
                                                <span class="badge badge-success">Berhasil</span>
                                            @elseif(in_array($status, ['pending']))
                                                <span class="badge badge-warning text-white">Menunggu</span>
                                            @elseif(in_array($status, ['deny', 'expire', 'cancel', 'rejected', 'failed']))
                                                <span class="badge badge-danger">Gagal / Expired</span>
                                            @else
                                                <span class="badge badge-secondary">{{ ucfirst($status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{-- Logika Tampilan Bukti --}}
                                            @if ($payment->snap_token || $payment->midtrans_order_id)
                                                {{-- Jika Transaksi Midtrans --}}
                                                <span class="badge badge-info">Otomatis (Midtrans)</span>
                                            @elseif($payment->payment_proof)
                                                {{-- Jika Transaksi Manual (Upload Gambar) --}}
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    data-toggle="modal" data-target="#proofModal{{ $payment->id }}">
                                                    <i class="fas fa-image"></i> Lihat Bukti
                                                </button>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                                    </tr>

                                    {{-- Modal Bukti (Hanya dirender jika ada gambar) --}}
                                    @if ($payment->payment_proof && !$payment->snap_token)
                                        <div class="modal fade" id="proofModal{{ $payment->id }}" tabindex="-1"
                                            role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Bukti Transfer Manual</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="{{ asset('storage/' . $payment->payment_proof) }}"
                                                            class="img-fluid rounded" alt="Bukti Pembayaran">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Pagination Links --}}
                <div class="card-footer">
                    {{ $payments->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection