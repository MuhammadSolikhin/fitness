@extends('layouts.app')

@section('title', 'Manajemen Kelas')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Manajemen Kelas</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <div class="card-header">
                        <div class="row w-100">
                            <div class="col-md-6">
                                <a href="{{ route('admin.classes.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Kelas
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <form action="{{ route('admin.classes.index') }}" method="GET"
                                    class="form-inline float-right">
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Cari kelas / pelatih..." value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-secondary" type="submit">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Kelas</th>
                                    <th>Nama Sanggar</th>
                                    <th>Pelatih</th>
                                    <th>Gambar</th>
                                    <th>Deskripsi</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($classes as $index => $class)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $class->name }}</td>
                                        <td>{{ $class->sanggar_name ?? '-' }}</td>
                                        <td>{{ $class->coach->name ?? '-' }}</td>
                                        <td>
                                            @if ($class->image_path)
                                                <img src="{{ asset('storage/' . $class->image_path) }}" alt="gambar kelas"
                                                    width="60" height="60"
                                                    style="object-fit: cover; border-radius: 8px; cursor: pointer;"
                                                    data-toggle="modal" data-target="#imageModal{{ $class->id }}">
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        <td>{{ Str::limit($class->description, 50) }}</td>
                                        <td>{{ $class->created_at->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                data-id="{{ $class->id }}" data-name="{{ $class->name }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>

                                            <form id="delete-form-{{ $class->id }}"
                                                action="{{ route('admin.classes.destroy', $class) }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                    @if ($class->image_path)
                                        <!-- Modal -->
                                        <div class="modal fade" id="imageModal{{ $class->id }}" tabindex="-1" role="dialog"
                                            aria-labelledby="imageModalLabel{{ $class->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="imageModalLabel{{ $class->id }}">Gambar Kelas
                                                        </h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="{{ asset('storage/' . $class->image_path) }}"
                                                            alt="gambar kelas detail" class="img-fluid rounded">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data kelas</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer clearfix">
                        {{ $classes->appends(request()->query())->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function () {
                    const classId = this.getAttribute('data-id');
                    const className = this.getAttribute('data-name');

                    Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        text: "Kelas \"" + className + "\" akan dihapus secara permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('delete-form-' + classId).submit();
                        }
                    });
                });
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