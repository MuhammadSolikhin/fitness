@extends('layouts.app')

@section('title', 'Dashboard Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Kartu Membership -->
            <div class="col-md-4">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h4>Membership Aktif</h4>
                        @if($membership)
                            <p>{{ ucfirst(str_replace('_', ' ', $membership->package)) }}</p>
                        @else
                            <p class="text-white">Belum memiliki membership aktif.</p>
                        @endif
                    </div>
                    <div class="icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <a href="{{ route('user.membership') }}" class="small-box-footer">
                        Kelola Membership <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Kartu Kelas Terdaftar -->
            <div class="col-md-4">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h4>Kelas Terdaftar</h4>
                        <p>{{ $totalClasses }} kelas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                    <a href="{{ route('user.classes.my') }}" class="small-box-footer">
                        Lihat Kelas <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Kartu Kelas yang Belum Diikuti -->
            <div class="col-md-4">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h4>Kelas Belum Diikuti</h4>
                        <p>{{ $pendingClasses ?? 0 }} kelas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <a href="{{ route('user.classes.my') }}" class="small-box-footer">
                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Jadwal Kelas Mendatang -->
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">Jadwal Kelas Mendatang</h5>
            </div>
            <div class="card-body p-3">
                @if(count($upcomingSchedules))
                    <ul class="list-group">
                        @foreach($upcomingSchedules as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <strong>{{ $item->class->name }}</strong><br>
                                    {{ \Carbon\Carbon::parse($item->schedule->schedule_date)->translatedFormat('l, d M Y') }} -
                                    {{ \Carbon\Carbon::parse($item->schedule->start_time)->format('H:i') }} s/d
                                    {{ \Carbon\Carbon::parse($item->schedule->end_time)->format('H:i') }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">Belum ada kelas mendatang.</p>
                @endif
            </div>
        </div>

        <!-- Bar Chart Progress Kelas Bulanan -->
        <div class="card card-primary mt-4">
            <div class="card-header py-2">
                <h5 class="card-title mb-0">Progress Kelas Bulanan</h5>
            </div>
            <div class="card-body p-3" style="height: 400px;">
                <canvas id="monthlyProgressChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('monthlyProgressChart');
        const monthlyProgressChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthlyProgress->keys()->toArray()) !!},
                datasets: [{
                    label: 'Jumlah Kelas Diikuti',
                    data: {!! json_encode($monthlyProgress->values()->toArray()) !!},
                    backgroundColor: '#007bff',
                    borderRadius: 5
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
@endpush