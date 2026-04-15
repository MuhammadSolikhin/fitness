@extends('layouts.app')

@section('title', 'Detail Kelas')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('user.classes.my') }}">Kelas yang Saya Ikuti</a></li>
    <li class="breadcrumb-item active">{{ $class->name }}</li>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    @if($class->image_path)
                        <img class="card-img-top" src="{{ asset('storage/' . $class->image_path) }}" alt="{{ $class->name }}">
                    @else
                        <img class="card-img-top" src="https://via.placeholder.com/350x350?text=No+Image" alt="No Image">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title font-weight-bold">{{ $class->name }}</h5>
                        <p class="card-text text-muted mb-1"><i class="fas fa-building mr-1"></i> Sanggar:
                            {{ $class->sanggar_name }}</p>
                        @if($class->coach)
                            <p class="card-text text-muted"><i class="fas fa-user-tie mr-1"></i> Pelatih:
                                {{ $class->coach->name }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Informasi Kelas</h5>
                    </div>
                    <div class="card-body">
                        <h5>Deskripsi</h5>
                        <p class="text-justify">{{ $class->description }}</p>

                        <hr>

                        <h5>Jadwal Anda</h5>
                        @if($enrollment && $enrollment->pivot->schedule_id)
                            @php
                                $schedule = \App\Models\Schedule::find($enrollment->pivot->schedule_id);
                            @endphp
                            @if($schedule)
                                <div class="alert alert-info">
                                    <i class="far fa-calendar-alt mr-2"></i>
                                    <strong>{{ $schedule->day_of_week }}</strong>,
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                    <br>
                                    <small class="text-muted">Tanggal:
                                        {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}</small>
                                </div>
                            @else
                                <p class="text-muted">Jadwal tidak ditemukan.</p>
                            @endif
                        @else
                            <p class="text-muted">Informasi jadwal tidak tersedia.</p>
                        @endif

                        <div class="mt-4">
                            <a href="{{ route('user.classes.my') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection