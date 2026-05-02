@extends('layouts.app')

@section('title', 'Dashboard Pelatih')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pelatih.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Dashboard</li>
@endsection
@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-12">
            <h5 class="mb-3">Selamat datang, {{ Auth::user()->name }}!</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalSchedules }}</h3>
                    <p>Total Jadwal Mengajar</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <a href="{{ route('pelatih.jadwal') }}" class="small-box-footer">Lihat Jadwal <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        <div class="col-lg-4 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalClasses }}</h3>
                    <p>Total Kelas Saya</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dumbbell"></i>
                </div>
                <a href="{{ route('pelatih.kelas-saya') }}" class="small-box-footer">Lihat Kelas <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        <div class="col-lg-4 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalMembers }}</h3>
                    <p>Total Anggota</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('pelatih.kelas-saya') }}" class="small-box-footer">Kelola Anggota <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
</div>
@endsection
