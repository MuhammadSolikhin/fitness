@extends('layouts.app')

@section('title', 'Manajemen User')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Manajemen User</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8">
                                <form action="{{ route('admin.users.index') }}" method="GET" class="form-inline">
                                    <input type="text" name="search" class="form-control mr-2 mb-2"
                                        placeholder="Cari nama atau email" value="{{ request('search') }}">
                                    <select name="role" class="form-control mr-2 mb-2">
                                        <option value="">-- Semua Role --</option>
                                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin
                                        </option>
                                        <option value="pelatih" {{ request('role') == 'pelatih' ? 'selected' : '' }}>Pelatih
                                        </option>
                                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                                    </select>
                                    <select name="is_membership" class="form-control mr-2 mb-2">
                                        <option value="">-- Membership --</option>
                                        <option value="yes" {{ request('is_membership') == 'yes' ? 'selected' : '' }}>Aktif
                                        </option>
                                        <option value="no" {{ request('is_membership') == 'no' ? 'selected' : '' }}>Tidak
                                            Aktif</option>
                                    </select>
                                    <button class="btn btn-primary mb-2" type="submit">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="{{ route('admin.users.export') }}" class="btn btn-success mb-2 mr-2">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </a>
                                <a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-2">
                                    <i class="fas fa-plus"></i> Tambah User
                                </a>
                            </div>
                        </div>
                    </div>


                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Membership</th>
                                    <th>Kadaluarsa Membership</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ ucfirst($user->role) }}</td>
                                        <td>{{ $user->is_membership ? 'Aktif' : 'Tidak Aktif' }}</td>
                                        <td>{{ $user->membership_expired_at ? $user->membership_expired_at->format('d-m-Y') : '-' }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.users.edit', $user) }}"
                                                class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button onclick="return confirm('Yakin hapus user ini?')"
                                                    class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer clearfix">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection