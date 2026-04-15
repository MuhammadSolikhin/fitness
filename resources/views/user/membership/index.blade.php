@extends('layouts.app')

@section('content')
    {{-- Script Midtrans --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

    <div class="container">
        <h3 class="mb-4">Membership Saya</h3>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($membership)
            @php
                $isExpired = \Carbon\Carbon::make($membership->end_date)->endOfDay()->isPast();
            @endphp

            {{-- INFO MEMBERSHIP --}}
            <div class="card {{ $isExpired ? 'border-danger' : 'border-success' }} mb-5">
                <div class="card-header {{ $isExpired ? 'bg-danger' : 'bg-success' }} text-white">
                    {{ $isExpired ? 'Membership Kedaluwarsa' : 'Membership Aktif' }}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Paket:</strong> {{ ucfirst(str_replace('_', ' ', $membership->package)) }}</p>
                            <p><strong>Periode:</strong> {{ $membership->start_date->format('d M Y') }} s/d
                                {{ $membership->end_date->format('d M Y') }}
                            </p>
                            @if($isExpired)
                                <div class="alert alert-danger mt-2">
                                    <i class="fas fa-exclamation-circle"></i> Masa berlaku membership Anda sudah habis. Silakan
                                    perpanjang untuk terus menikmati layanan.
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6 text-md-right">
                            <h4 class="{{ $isExpired ? 'text-danger' : 'text-success' }}">{{ $membership->remaining_sessions }}
                                / {{ $totalSessions }}</h4>
                            <small class="text-muted">Sisa Pertemuan</small>
                        </div>
                    </div>

                    <hr>

                    {{-- Tombol Aksi --}}
                    <div class="d-flex flex-wrap gap-2">
                        {{-- Tombol Upgrade VIP dihapus atas permintaan user --}}
                        <button type="button" class="btn btn-success"
                            onclick="processMembership('{{ $membership->package }}', {{ $membership->package === '2_bulan' ? 200000 : 120000 }})">
                            Perpanjang Membership
                        </button>
                    </div>
                </div>
            </div>

            {{-- GRID KOTAK PERTEMUAN --}}
            <h4 class="mb-3">Kartu Pertemuan ({{ $joinedClasses->count() }} Terpakai)</h4>
            <div class="row">
                {{-- Loop sebanyak Total Sesi (misal 12 atau 24) --}}
                @for ($i = 0; $i < $totalSessions; $i++)
                    <div class="col-6 col-md-4 col-lg-3 mb-4">

                        {{-- CEK: Apakah index ini sudah ada isinya (sudah dipakai)? --}}
                        @if (isset($joinedClasses[$i]))
                            @php $classData = $joinedClasses[$i]; @endphp
                            {{-- KOTAK TERISI (KELAS DIAMBIL) --}}
                            <div class="card h-100 shadow-sm border-0 session-card filled-card">
                                {{-- Gambar Kelas --}}
                                <div class="card-img-wrapper">
                                    <img src="{{ $classData->image_path ? asset('storage/' . $classData->image_path) : 'https://via.placeholder.com/300x200?text=No+Image' }}"
                                        class="card-img-top" alt="{{ $classData->name }}">
                                    <div class="date-badge">
                                        {{ \Carbon\Carbon::parse($classData->pivot->created_at)->format('d M') }}
                                    </div>
                                </div>

                                <div class="card-body text-center p-3">
                                    <h6 class="font-weight-bold text-uppercase mb-1">{{ $classData->name }}</h6>
                                    <small class="text-muted d-block mb-3">
                                        {{ $classData->sanggar_name ?? 'Sanggar Utama' }}
                                    </small>

                                    {{-- Tombol Detail (Opsional) --}}
                                    <button class="btn btn-sm btn-success rounded-pill px-4">
                                        Selesai
                                    </button>
                                </div>
                            </div>

                        @else
                            {{-- KOTAK KOSONG (BELUM DIPAKAI) --}}
                            <div class="card h-100 border-0 session-card empty-card d-flex align-items-center justify-content-center">
                                <div class="card-body text-center w-100 d-flex flex-column justify-content-center align-items-center">
                                    <h2 class="text-muted mb-3" style="opacity: 0.3;">{{ $i + 1 }}</h2>
                                    <a href="{{ route('user.classes.available') }}" class="btn btn-outline-primary rounded-pill">
                                        Ikuti Kelas
                                    </a>
                                </div>
                            </div>
                        @endif

                    </div>
                @endfor
            </div>

        @else
            {{-- TAMPILAN BELUM PUNYA MEMBERSHIP (Sama seperti sebelumnya) --}}
            <div class="alert alert-info">Silakan beli membership untuk melihat kartu pertemuan.</div>
            {{-- ... Kode card pilihan paket membership di sini ... --}}
            <div class="d-flex justify-content-center gap-4 flex-wrap">
                <div class="card shadow-sm border-primary mb-4 mx-2" style="width: 320px;">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">Reguler</h4>
                    </div>
                    <div class="card-body text-center">
                        <p>1 Bulan / 12x Pertemuan</p>
                        <h3>Rp 120.000</h3>
                        <button type="button" class="btn btn-primary btn-block mt-3"
                            onclick="processMembership('1_bulan', 120000)">
                            Daftar Sekarang
                        </button>
                    </div>
                </div>

                <div class="card shadow-sm border-warning mb-4 mx-2" style="width: 320px;">
                    <div class="card-header bg-warning text-white text-center">
                        <h4 class="mb-0">VIP</h4>
                    </div>
                    <div class="card-body text-center">
                        <p>2 Bulan / 24x Pertemuan</p>
                        <h3>Rp 200.000</h3>
                        <button type="button" class="btn btn-warning btn-block mt-3"
                            onclick="processMembership('2_bulan', 200000)">
                            Daftar VIP
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        /* Style untuk Kartu Sesi */
        .session-card {
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.2s;
        }

        .session-card:hover {
            transform: translateY(-5px);
        }

        /* Style Kartu Kosong (Abu-abu) */
        .empty-card {
            background-color: #e9ecef;
            /* Abu-abu seperti contoh */
            border: 2px dashed #ced4da !important;
            min-height: 200px;
        }

        /* Style Kartu Terisi */
        .filled-card {
            background-color: #fff;
        }

        .card-img-wrapper {
            position: relative;
            height: 140px;
            overflow: hidden;
        }

        .card-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Badge Tanggal di pojok gambar */
        .date-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(108, 92, 231, 0.9);
            /* Warna Ungu/Biru */
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: bold;
        }
    </style>
@endpush

@push('scripts')
    {{-- Script Midtrans yang sudah ada sebelumnya --}}
    <script>
        function processMembership(package, amount) {
            // ... (Kode Javascript Midtrans Anda yang sebelumnya) ...
            Swal.fire({
                title: 'Konfirmasi Pendaftaran',
                text: `Anda akan mendaftar paket ${package.replace('_', ' ')} seharga Rp ${amount.toLocaleString('id-ID')}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Bayar Sekarang',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("user.membership.register") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            package: package,
                            amount: amount
                        },
                        success: function (response) {
                            if (response.snap_token) {
                                snap.pay(response.snap_token, {
                                    onSuccess: function (result) {
                                        Swal.fire('Berhasil', 'Pembayaran sukses!', 'success')
                                            .then(() => location.reload());
                                    },
                                    onPending: function (result) {
                                        Swal.fire('Pending', 'Selesaikan pembayaran.', 'info');
                                    },
                                    onError: function (result) {
                                        Swal.fire('Gagal', 'Pembayaran gagal.', 'error');
                                    },
                                    onClose: function () {
                                        Swal.fire('Batal', 'Anda menutup pembayaran.', 'warning');
                                    }
                                });
                            }
                        },
                        error: function (xhr) {
                            let msg = 'Terjadi kesalahan.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire('Gagal', msg, 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush