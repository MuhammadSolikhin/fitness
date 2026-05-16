@extends('layouts.app')

@section('title', 'Detail Kelas')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.classes.index') }}">Manajemen Kelas</a></li>
    <li class="breadcrumb-item active">Detail Kelas</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card mb-4">
            <div class="card-header">
                <h3>{{ $class->name }}</h3>
            </div>
            <div class="card-body d-flex">
                @if ($class->image_path)
                    <img src="{{ asset('storage/' . $class->image_path) }}" width="150" height="150" class="mr-3 rounded"
                        style="object-fit: cover;">
                @else
                    <div style="width: 150px; height: 150px; background-color: #e9ecef;" class="mr-3 rounded d-flex align-items-center justify-content-center text-muted">
                        No Image
                    </div>
                @endif
                <div>
                    <p><strong>Deskripsi:</strong> {{ $class->description }}</p>
                    <p><strong>Dibuat:</strong> {{ $class->created_at->format('d M Y') }}</p>
                    <!-- Tombol buka modal -->
                    <button type="button" class="btn btn-success mt-2" data-toggle="modal" data-target="#addUserModal">
                        + Tambah User
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal tambah user -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('admin.classes.addUser', $class->id) }}" method="POST" id="addUserForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addUserModalLabel">Tambah User ke Kelas</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Jenis Pendaftaran</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="register_type" id="existing_user"
                                        value="existing" checked>
                                    <label class="form-check-label" for="existing_user">Pilih User yang Sudah Ada</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="register_type" id="new_user"
                                        value="new">
                                    <label class="form-check-label" for="new_user">Daftarkan User Baru</label>
                                </div>
                            </div>

                            <div class="form-group" id="existingUserForm">
                                <label for="user_id">Pilih User</label>
                                <select name="user_id" id="user_id" class="form-control">
                                    <option value="">-- Pilih User --</option>
                                    @foreach($allUsers as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="newUserForm" style="display: none;">
                                <div class="form-group">
                                    <label for="new_name">Nama</label>
                                    <input type="text" name="new_name" id="new_name" class="form-control">
                                </div>
                                <div class="form-group mt-2">
                                    <label for="new_email">Email</label>
                                    <input type="email" name="new_email" id="new_email" class="form-control">
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label for="schedule_id">Pilih Jadwal</label>
                                <select name="schedule_id" id="schedule_id" class="form-control" required>
                                    <option value="">-- Pilih Jadwal --</option>
                                    @foreach($class->schedules as $schedule)
                                        <option value="{{ $schedule->id }}">
                                            {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }} |
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-check mt-3">
                                <input type="checkbox" class="form-check-input" id="is_paid_per_session"
                                    name="is_paid_per_session" value="1">
                                <label class="form-check-label" for="is_paid_per_session">Bayar per sesi</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                aria-label="Close">Batal</button>
                            <button type="submit" class="btn btn-primary">Tambah User</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>User Terdaftar</h4>
            </div>
            <div class="card-body">
                @if($users->isEmpty())
                    <p class="text-muted">Belum ada user terdaftar di kelas ini.</p>
                @else
                    <div class="accordion" id="accordionSchedules">
                        @foreach($schedules as $schedule)
                            @php
                                $scheduleUsers = $users->filter(function($user) use ($schedule) {
                                    return $user->pivot->schedule_id == $schedule->id;
                                });
                            @endphp
                            <div class="card mb-2 shadow-sm">
                                <div class="card-header bg-light" id="heading{{ $schedule->id }}">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left font-weight-bold text-dark text-decoration-none" type="button" data-toggle="collapse" data-target="#collapse{{ $schedule->id }}" aria-expanded="true" aria-controls="collapse{{ $schedule->id }}">
                                            Sesi: {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }} ({{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }})
                                            <span class="badge badge-primary float-right">{{ $scheduleUsers->count() }} User</span>
                                        </button>
                                    </h2>
                                </div>

                                <div id="collapse{{ $schedule->id }}" class="collapse show" aria-labelledby="heading{{ $schedule->id }}">
                                    <div class="card-body p-0">
                                        @if($scheduleUsers->isEmpty())
                                            <p class="text-muted p-3 mb-0">Belum ada user terdaftar di sesi ini.</p>
                                        @else
                                            <ul class="list-group list-group-flush">
                                                @foreach($scheduleUsers as $user)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <i class="fas fa-user-circle text-secondary mr-2"></i>
                                                            <strong>{{ $user->name }}</strong> 
                                                            <small class="text-muted">({{ $user->email }})</small>
                                                        </div>
                                                        <form action="{{ route('admin.classes.updateMembership', [$class->id, $user->id]) }}"
                                                            method="POST" class="d-flex align-items-center">
                                                            @csrf
                                                            @method('PATCH')
                                                            <select name="is_paid_per_session" class="form-control form-control-sm mr-2"
                                                                onchange="this.form.submit()">
                                                                <option value="0" {{ !$user->pivot->is_paid_per_session ? 'selected' : '' }}>Membership
                                                                </option>
                                                                <option value="1" {{ $user->pivot->is_paid_per_session ? 'selected' : '' }}>Bayar Per Sesi
                                                                </option>
                                                            </select>
                                                            <button class="btn btn-sm btn-primary" type="submit">Simpan</button>
                                                        </form>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Menangani user yang jadwalnya sudah dihapus atau tidak valid --}}
                        @php
                            $unassignedUsers = $users->filter(function($user) use ($schedules) {
                                return !isset($schedules[$user->pivot->schedule_id]);
                            });
                        @endphp
                        
                        @if($unassignedUsers->isNotEmpty())
                            <div class="card mb-2 shadow-sm border-danger">
                                <div class="card-header bg-danger text-white" id="headingUnassigned">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left font-weight-bold text-white text-decoration-none" type="button" data-toggle="collapse" data-target="#collapseUnassigned" aria-expanded="true" aria-controls="collapseUnassigned">
                                            Sesi Tidak Valid / Dihapus
                                            <span class="badge badge-light float-right">{{ $unassignedUsers->count() }} User</span>
                                        </button>
                                    </h2>
                                </div>

                                <div id="collapseUnassigned" class="collapse" aria-labelledby="headingUnassigned">
                                    <div class="card-body p-0">
                                        <ul class="list-group list-group-flush">
                                            @foreach($unassignedUsers as $user)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="fas fa-exclamation-circle text-danger mr-2"></i>
                                                        <strong>{{ $user->name }}</strong> 
                                                        <small class="text-muted">({{ $user->email }})</small>
                                                    </div>
                                                    <form action="{{ route('admin.classes.updateMembership', [$class->id, $user->id]) }}"
                                                        method="POST" class="d-flex align-items-center">
                                                        @csrf
                                                        @method('PATCH')
                                                        <select name="is_paid_per_session" class="form-control form-control-sm mr-2"
                                                            onchange="this.form.submit()">
                                                            <option value="0" {{ !$user->pivot->is_paid_per_session ? 'selected' : '' }}>Membership
                                                            </option>
                                                            <option value="1" {{ $user->pivot->is_paid_per_session ? 'selected' : '' }}>Bayar Per Sesi
                                                            </option>
                                                        </select>
                                                        <button class="btn btn-sm btn-primary" type="submit">Simpan</button>
                                                    </form>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('input[name="register_type"]').change(function () {
                if ($(this).val() === 'new') {
                    $('#existingUserForm').hide();
                    $('#newUserForm').show();
                    $('#user_id').prop('required', false);
                    $('#new_name, #new_email').prop('required', true);
                } else {
                    $('#existingUserForm').show();
                    $('#newUserForm').hide();
                    $('#user_id').prop('required', true);
                    $('#new_name, #new_email').prop('required', false);
                }
            });
        });
    </script>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            });
        </script>
    @endif
@endpush
