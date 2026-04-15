@extends('layouts.app')

@section('title', 'Tambah Kelas')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.classes.index') }}">Manajemen Kelas</a></li>
    <li class="breadcrumb-item active">Tambah Kelas</li>
@endsection

@section('content')
<div class="container-fluid">
    <form action="{{ route('admin.classes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Form Tambah Kelas</h3>
            </div>

            <div class="card-body">
                @include('admin.class.partials.form')
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </div>
    </form>
</div>
@endsection
