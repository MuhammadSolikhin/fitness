@extends('layouts.app')

@section('title', 'Manajemen Pembayaran')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-6">
                <form action="{{ route('admin.payments.history') }}" method="GET" class="form-inline">
                    <input type="text" name="search" class="form-control mr-2" placeholder="Cari nama pengguna"
                        value="{{ request('search') }}">

                    <select name="status" class="form-control mr-2">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Berhasil</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                    </select>

                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Nama Pengguna</th>
                                <th>Order ID</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th>Info / Bukti</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    <td>{{ $payment->user->name }}</td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $payment->midtrans_order_id ?? '#' . $payment->id }}
                                        </small>
                                    </td>
                                    <td>
                                        {{ ucwords(str_replace('_', ' ', $payment->type)) }}
                                    </td>
                                    <td>
                                        <strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        @php
                                            $status = strtolower($payment->status);
                                        @endphp

                                        @if (in_array($status, ['success', 'settlement', 'capture', 'paid']))
                                            <span class="badge badge-success">Berhasil</span>
                                        @elseif(in_array($status, ['pending']))
                                            <span class="badge badge-warning text-white">Menunggu</span>
                                        @elseif(in_array($status, ['deny', 'expire', 'cancel', 'failed']))
                                            <span class="badge badge-danger">Gagal / Expired</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($payment->snap_token || $payment->midtrans_order_id)
                                            <span class="badge badge-info">Otomatis (Midtrans)</span>
                                        @elseif($payment->payment_proof)
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal"
                                                data-target="#proofModal{{ $payment->id }}">
                                                <i class="fas fa-image"></i> Lihat Bukti
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                
                                @if($payment->payment_proof && !$payment->snap_token)
                                    <div class="modal fade" id="proofModal{{ $payment->id }}" tabindex="-1" role="dialog"
                                        aria-labelledby="proofModalLabel{{ $payment->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="proofModalLabel{{ $payment->id }}">Bukti Pembayaran</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <img src="{{ asset('storage/' . $payment->payment_proof) }}" alt="Bukti Pembayaran"
                                                        class="img-fluid rounded">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $payments->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection