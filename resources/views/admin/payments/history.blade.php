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
                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Terverifikasi
                        </option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>

                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>Jenis Pembayaran</th>
                        <th>Jumlah</th>
                        <th>Bukti</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->user->name }}</td>
                            <td>{{ ucfirst($payment->type) }}</td>
                            <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                            <td>
                                @if($payment->payment_proof)
                                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                        data-target="#proofModal{{ $payment->id }}">
                                        Lihat
                                    </button>
                                @else
                                    <span class="text-danger">Belum Upload</span>
                                @endif

                            </td>
                            <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                            <td>{{ $payment->status }}</td>
                        </tr>
                        @if($payment->payment_proof)
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
                        <div class="d-flex justify-content-end mt-3">
                            {{ $payments->links() }}
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Tidak ada pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>
@endsection