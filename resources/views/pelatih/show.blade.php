@extends('layouts.app')

@section('title', 'Detail Kelas')

@section('content')
    <div class="container">
        <div class="card mb-4">
            <div class="card-header">
                <h3>{{ $class->name }}</h3>
            </div>
            <div class="card-body d-flex">
                <img src="{{ asset('storage/' . $class->image_path) }}" width="150" height="150" class="mr-3 rounded"
                    style="object-fit: cover;">
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
                <form action="{{ route('pelatih.classes.addUser', $class->id) }}" method="POST" id="addUserForm">
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
                                            {{ $schedule->start_time }} - {{ $schedule->end_time }}
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
                    <ul class="list-group">
                        @foreach($users as $user)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    {{ $user->name }} ({{ $user->email }})
                                    @php
                                        $schedule = $schedules[$user->pivot->schedule_id] ?? null;
                                    @endphp
                                    <small class="d-block text-muted">
                                        Jadwal: 
                                        {{ $schedule 
                                            ? \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') . ' (' . $schedule->start_time . ' - ' . $schedule->end_time . ')' 
                                            : '-' 
                                        }}
                                    </small>
                                </div>
                                <form action="{{ route('pelatih.classes.updateMembership', [$class->id, $user->id]) }}"
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