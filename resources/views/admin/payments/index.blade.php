@extends('layouts.app')

@section('title', 'Manajemen Pembayaran')

@section('content')
    <div class="container-fluid">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>Jenis Pembayaran</th>
                        <th>Jumlah</th>
                        <th>Bukti</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
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
                            <td>
                                <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST"
                                    class="d-inline form-verify">
                                    @csrf
                                    <button type="button" class="btn btn-success btn-sm btn-verify">Verifikasi</button>
                                </form>

                                <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST"
                                    class="d-inline form-reject">
                                    @csrf
                                    <button type="button" class="btn btn-danger btn-sm btn-reject">Tolak</button>
                                </form>

                            </td>
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

                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Tidak ada pembayaran yang menunggu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>

        document.addEventListener("DOMContentLoaded", function () {
            // Verifikasi
            document.querySelectorAll('.btn-verify').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    Swal.fire({
                        title: 'Verifikasi pembayaran?',
                        text: "Pastikan pembayaran valid.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Verifikasi!',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#d33'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            btn.closest('form').submit();
                        }
                    });
                });
            });

            // Tolak
            document.querySelectorAll('.btn-reject').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    Swal.fire({
                        title: 'Tolak pembayaran?',
                        text: "User harus mengirim ulang bukti pembayaran.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Tolak!',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            btn.closest('form').submit();
                        }
                    });
                });
            });
        });


        @if (session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Berhasil',
                showConfirmButton: false,
                timer: 3000
            });
        @elseif (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33',
            });
        @elseif (session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: '{{ session('warning') }}',
                confirmButtonColor: '#f1c40f',
            });
        @endif
    </script>

@endpush