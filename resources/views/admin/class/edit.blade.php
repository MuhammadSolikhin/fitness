@extends('layouts.app')

@section('title', 'Edit Kelas')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.classes.index') }}">Manajemen Kelas</a></li>
    <li class="breadcrumb-item active">Edit Kelas</li>
@endsection

@section('content')
<div class="container-fluid">
    <form action="{{ route('admin.classes.update', $class->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Form Edit Kelas</h3>
            </div>

            <div class="card-body">
                @include('admin.class.partials.form')
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
    </form>
</div>
@endsection
