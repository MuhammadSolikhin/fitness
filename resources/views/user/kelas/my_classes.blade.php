@extends('layouts.app')
@section('title', 'Kelas yang Saya Ikuti')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Kelas yang Saya Ikuti</li>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            @forelse($myClasses as $class)
                <div class="col-md-4">
                    <div class="card shadow-sm mb-4">
                        @if($class->image_path)
                            <img class="card-img-top" src="{{ asset('storage/' . $class->image_path) }}" alt="Class Image">
                        @else
                            <img class="card-img-top" src="https://via.placeholder.com/350x150?text=No+Image" alt="No Image">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $class->name }}</h5>
                            <h6 class="text-muted">Sanggar: {{ $class->sanggar_name }}</h6>
                            <p class="card-text">{{ \Illuminate\Support\Str::limit($class->description, 100) }}</p>
                            <a href="{{ route('user.classes.show', $class->id) }}" class="btn btn-primary btn-sm">Lihat
                                Detail</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">Belum ada kelas yang kamu ikuti.</div>
                </div>
            @endforelse
        </div>
    </div>
@endsection