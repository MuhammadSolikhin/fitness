@extends('layouts.app')

@section('title', 'Manajemen Pembayaran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pelatih.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Pembayaran</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Pembayaran Kelas Anda</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Nama Anggota</th>
                            <th>Kelas</th>
                            <th>Tipe Pembayaran</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->user->name ?? '-' }}</td>
                                <td>{{ $payment->class->name ?? 'Membership Umum' }}</td>
                                <td>{{ ucfirst($payment->type) }}</td>
                                <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td>
                                    @if($payment->status == 'verified')
                                        <span class="badge badge-success">Terverifikasi</span>
                                    @elseif($payment->status == 'pending')
                                        <span class="badge badge-warning">Menunggu</span>
                                    @elseif($payment->status == 'rejected')
                                        <span class="badge badge-danger">Ditolak</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($payment->status) }}</span>
                                    @endif
                                </td>
                                <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Tidak ada pembayaran untuk kelas Anda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($payments->hasPages())
        <div class="card-footer clearfix">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
