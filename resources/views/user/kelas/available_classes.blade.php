@extends('layouts.app')
@push('styles')
    <style>
        .fixed-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
    </style>
@endpush
@section('title', 'Kelas Tersedia')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Kelas Tersedia</li>
@endsection
@section('content')
    @php
        $userMembership = auth()->user()->membership;
        // Check if membership is expired
        $isExpired = $userMembership && \Carbon\Carbon::make($userMembership->end_date)->endOfDay()->isPast();

        // If expired, treat as no active membership for class registration (force pay per session)
        $remaining = ($userMembership && !$isExpired) ? $userMembership->remaining_sessions : '';
        \Carbon\Carbon::setLocale('id');    
    @endphp

    <div class="container">
        <div class="row">
            @forelse($classes as $class)
                <div class="col-md-4 mb-4">

                    <div class="card shadow-sm h-100">
                        @if($class->image_path)
                            <img class="card-img-top fixed-image" src="{{ asset('storage/' . $class->image_path) }}"
                                alt="Class Image">
                        @else
                            <img class="card-img-top fixed-image" src="https://via.placeholder.com/350x150?text=No+Image"
                                alt="No Image">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $class->name }} - </h5>
                            <h6 class="text-muted">Sanggar: {{ $class->sanggar_name }}</h6>
                            <p class="card-text">{{ \Illuminate\Support\Str::limit($class->description, 100) }}</p>

                            <h6>Jadwal Tersedia:</h6>
                            @php
                                $availableSchedules = $class->schedules->filter(function ($schedule) {
                                    return \Carbon\Carbon::parse($schedule->schedule_date)->isToday() || \Carbon\Carbon::parse($schedule->schedule_date)->isFuture();
                                });
                            @endphp
                            <ul>
                                @forelse ($availableSchedules as $schedule)
                                    <li>
                                        Hari: {{ \Carbon\Carbon::parse($schedule->schedule_date)->translatedFormat('l') }},
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}

                                        <form action="{{ route('user.class.register') }}" method="POST" class="register-form"
                                            id="registerForm-{{ $schedule->id }}">
                                            @csrf
                                            <input type="hidden" name="class_id" value="{{ $class->id }}">
                                            <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                                            <button type="button" class="btn btn-sm btn-primary register-button"
                                                data-id="{{ $schedule->id }}" data-class-id="{{ $class->id }}" data-remaining="{{$remaining}}">
                                                Daftar
                                            </button>
                                        </form>
                                    </li>
                                    <!-- Modal pembayaran per sesi -->
                                    <div class="modal fade" id="paymentModal-{{ $schedule->id }}" tabindex="-1"
                                        aria-labelledby="paymentModalLabel-{{ $schedule->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form method="POST" action="{{ route('user.class.payPerSession') }}"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="class_id" value="{{ $class->id }}">
                                                <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                                                <input type="hidden" name="amount" value="50000">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="paymentModalLabel-{{ $schedule->id }}">
                                                            Pembayaran Per Sesi</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Silakan unggah bukti pembayaran untuk kelas ini (Rp 50.000):</p>
                                                        <div class="mb-3">
                                                            <label for="payment_proof-{{ $schedule->id }}" class="form-label">Bukti
                                                                Pembayaran</label>
                                                            <input class="form-control" type="file" name="payment_proof"
                                                                id="payment_proof-{{ $schedule->id }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Kirim Pembayaran</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                @empty
                                    <p class="text-muted">Jadwal belum tersedia.</p>
                                @endforelse
                            </ul>

                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">Belum ada kelas yang tersedia.</div>
                </div>
            @endforelse
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('.register-button');

            buttons.forEach(button => {
                button.addEventListener('click', function () {
                    const scheduleId = this.getAttribute('data-id');
                    const classId = this.getAttribute('data-class-id');
                    const remainingRaw = this.getAttribute('data-remaining');
                    const remaining = remainingRaw === '' ? null : parseInt(remainingRaw);
                    const formId = `registerForm-${scheduleId}`;
                    const form = document.getElementById(formId);

                    if (remaining === null) {
                        Swal.fire({
                            title: 'Belum Memiliki Membership',
                            text: 'Anda belum memiliki membership. Apakah Anda ingin mendaftar kelas ini dengan membayar per sesi (Rp 50.000)?',
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Lanjutkan',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: '{{ route("user.class.payPerSession", [], false) }}',
                                    method: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        class_id: classId,
                                        schedule_id: scheduleId,
                                        amount: 50000
                                    },
                                    success: function (response) {
                                        if (response.snap_token) {
                                            snap.pay(response.snap_token, {
                                                onSuccess: function (result) {
                                                    Swal.fire('Berhasil', 'Pembayaran sukses!', 'success').then(() => location.reload());
                                                },
                                                onPending: function (result) {
                                                    Swal.fire('Menunggu', 'Silakan selesaikan pembayaran.', 'info');
                                                },
                                                onError: function (result) {
                                                    Swal.fire('Gagal', 'Pembayaran gagal.', 'error');
                                                },
                                                onClose: function () {
                                                    Swal.fire('Tutup', 'Anda menutup popup pembayaran.', 'warning');
                                                }
                                            });
                                        }
                                    },
                                    error: function (xhr) {
                                        Swal.fire('Error', 'Gagal memproses pembayaran', 'error');
                                    }
                                });
                            }
                        });
                        return;
                    }

                    if (remaining <= 0) {
                        Swal.fire({
                            title: 'Kuota Habis',
                            text: 'Anda tidak memiliki sisa kuota pertemuan. Silakan perpanjang membership Anda.',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Konfirmasi Pendaftaran',
                        html: `
                                                            <p>Apakah Anda yakin ingin mendaftar ke kelas ini?</p>
                                                            <p class="text-muted">Sisa kuota Pertemuan setelah mendaftar: <strong>${remaining - 1}x</strong></p>
                                                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, daftar',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Sukses',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33'
            });
        </script>
    @endif

@endpush