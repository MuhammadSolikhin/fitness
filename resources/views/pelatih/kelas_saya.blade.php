@extends('layouts.app')
@section('title', 'Kelas Saya')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pelatih.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Manajemen Kelas</li>
@endsection
@push('styles')
    <style>
        .hover-shadow:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            transition: 0.3s;
        }
    </style>
@endpush
@section('content')
    <div class="container">
        @if($classes->isEmpty())
            <div class="alert alert-info text-center mt-4">
                <i class="fas fa-info-circle"></i> Belum ada kelas yang Anda kelola.
            </div>
        @else
            <div class="row">
                @foreach($classes as $class)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm hover-shadow transition">
                            @if($class->image_path)
                                <img src="{{ asset('storage/' . $class->image_path) }}" class="card-img-top" alt="gambar kelas"
                                    style="height: 180px; object-fit: cover;">
                            @else
                                <img src="{{ asset('images/default-class.jpg') }}" class="card-img-top" alt="default image"
                                    style="height: 180px; object-fit: cover;">
                            @endif

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $class->name }}</h5>
                                <p class="card-text flex-grow-1">{{ Str::limit($class->description, 100) }}</p>
                                <a href="{{ route('pelatih.classes.show', $class) }}" class="btn btn-primary mt-auto">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

@endsection