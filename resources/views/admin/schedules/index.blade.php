{{-- schedules/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manajemen Jadwal')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Manajemen Jadwal</li>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css" />
@endpush

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addScheduleModal">
                    <i class="fas fa-plus"></i> Tambah Jadwal
                </button>

            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>

    </div>

    {{-- Modal Detail Jadwal --}}
    <div class="modal fade" id="scheduleModal" tabindex="-1" role="dialog" aria-labelledby="scheduleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="scheduleForm">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="scheduleModalLabel">Detail Jadwal</h5>
                        <button type="button" class="close" data-dismiss="modal"
                            aria-label="Close"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="scheduleId">
                        <div class="form-group">
                            <label>Nama Kelas</label>
                            <input type="text" class="form-control" id="modalClassName" readonly>
                        </div>
                        <div class="form-group">
                            <label>Nama Sanggar</label>
                            <input type="text" class="form-control" id="modalSanggarName" readonly>
                        </div>
                        <div class="form-group">
                            <label>Pelatih</label>
                            <input type="text" class="form-control" id="modalCoachName" readonly>
                        </div>
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" class="form-control" id="modalDate" name="schedule_date" required>
                        </div>
                        <div class="form-group">
                            <label>Jam Mulai</label>
                            <input type="time" class="form-control" id="modalStart" name="start_time" required>
                        </div>
                        <div class="form-group">
                            <label>Jam Selesai</label>
                            <input type="time" class="form-control" id="modalEnd" name="end_time" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="deleteSchedule">Hapus</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Tambah Jadwal --}}
    <div class="modal fade" id="addScheduleModal" tabindex="-1" role="dialog" aria-labelledby="addScheduleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="addScheduleForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Jadwal</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Kelas</label>
                            <select id="class_id" name="class_id" class="form-control" required>
                                <option value="" disabled selected>-- Pilih Kelas --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" data-sanggar="{{ $class->sanggar_name }}">
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Nama Sanggar</label>
                            <input type="text" class="form-control" id="addSanggarName" readonly
                                placeholder="Otomatis terisi...">
                        </div>

                        <div class="form-group">
                            <label>Tanggal</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="addDate" name="schedule_date"
                                    placeholder="Pilih Tanggal" required
                                    style="background-color: #ffffff; cursor: pointer;">

                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Jam Mulai</label>
                            <input type="time" class="form-control" id="start_time" name="start_time" required>
                        </div>
                        <div class="form-group">
                            <label>Jam Selesai</label>
                            <input type="time" class="form-control" id="end_time" name="end_time" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            let currentEvent = null;

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                allDaySlot: false,
                slotMinTime: '06:00:00',
                slotMaxTime: '22:00:00',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                events: @json($events),
                dayCellDidMount: function (info) {
                    // info.date adalah objek Date
                    if (info.date.getDay() === 0) { // 0 = Sunday
                        // kasih kelas css agar background abu-abu
                        info.el.style.backgroundColor = '#f0f0f0';
                        info.el.style.pointerEvents = 'none'; // supaya gak bisa diklik
                        info.el.style.opacity = '0.5'; // efek abu-abu
                    }
                },

                eventClick: function (info) {
                    if (info.event.start.getDay() === 0) {
                        alert('Hari Minggu tidak bisa diedit');
                        return;
                    }
                    currentEvent = info.event;
                    const props = currentEvent.extendedProps;

                    document.getElementById('scheduleId').value = currentEvent.id;
                    document.getElementById('modalClassName').value = props.class_name;
                    document.getElementById('modalCoachName').value = props.coach_name;
                    document.getElementById('modalSanggarName').value = props.sanggar_name;

                    const start = currentEvent.start;
                    const end = currentEvent.end;

                    // Format: YYYY-MM-DD and HH:MM
                    document.getElementById('modalDate').value = start.toISOString().split('T')[0];
                    document.getElementById('modalStart').value = start.toTimeString().slice(0, 5);
                    document.getElementById('modalEnd').value = end.toTimeString().slice(0, 5);

                    $('#scheduleModal').modal('show');
                }

            });

            calendar.render();

            // Save edited schedule
            document.getElementById('scheduleForm').addEventListener('submit', function (e) {
                e.preventDefault();

                const id = document.getElementById('scheduleId').value;
                const schedule_date = document.getElementById('modalDate').value;
                const start_time = document.getElementById('modalStart').value;
                const end_time = document.getElementById('modalEnd').value;
                const selectedDateTime = new Date(schedule_date + 'T' + start_time);
                const now = new Date();

                if (selectedDateTime < now) {
                    Swal.fire('Error', 'Tidak bisa mengatur jadwal ke waktu yang sudah lewat.', 'error');
                    return;
                }

                if (start_time >= end_time) {
                    Swal.fire('Error', 'Waktu selesai harus lebih besar dari waktu mulai.', 'error');
                    return;
                }

                fetch(`/admin/schedules/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        schedule_date: schedule_date,
                        start_time: start_time + ':00',
                        end_time: end_time + ':00',
                    })
                })
                    .then(res => {
                        if (!res.ok) throw new Error('Gagal update');
                        return res.json();
                    })
                    .then(data => {
                        Swal.fire('Berhasil', 'Jadwal berhasil diperbarui.', 'success')
                            .then(() => location.reload());
                    })
                    .catch(err => {
                        Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data.', 'error');
                        console.error(err);
                    });
            });

            // Delete schedule
            document.getElementById('deleteSchedule').addEventListener('click', function () {
                Swal.fire({
                    title: 'Yakin ingin menghapus jadwal?',
                    text: "Data jadwal akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/admin/schedules/${currentEvent.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                            .then(res => {
                                if (!res.ok) throw new Error('Failed to delete schedule');
                                return res.json();
                            })
                            .then(data => {
                                calendar.getEventById(currentEvent.id).remove();
                                Swal.fire('Terhapus!', 'Jadwal berhasil dihapus.', 'success');
                                $('#scheduleModal').modal('hide');
                            })
                            .catch(err => {
                                Swal.fire('Error', 'Gagal menghapus jadwal.', 'error');
                                console.error(err);
                            });
                    }
                })
            });

        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            $('#class_id').on('change', function () {
                var selectedOption = $(this).find(':selected');
                var sanggarName = selectedOption.data('sanggar');

                $('#addSanggarName').val(sanggarName);
            });

            flatpickr("#addDate", {
                dateFormat: "Y-m-d",
                static: true,
                allowInput: true,
                disable: [
                    function (date) {
                        return date.getDay() === 0;
                    }
                ],
                locale: {
                    firstDayOfWeek: 1
                }
            });
        });
    </script>
    <script>
        $(function () {
            $('#addScheduleForm').on('submit', function (e) {
                e.preventDefault();

                let scheduleDate = $('#addDate').val();
                let start = $('#start_time').val() + ':00';
                let end = $('#end_time').val() + ':00';


                if (!start || !end || !scheduleDate) {
                    Swal.fire('Error', 'Tanggal dan waktu mulai/selesai harus diisi.', 'error');
                    return;
                }

                // Bandingkan jam selesai harus lebih besar
                if (end <= start) {
                    Swal.fire('Error', 'Jam selesai harus lebih besar dari jam mulai.', 'error');
                    return;
                }

                // Cek jadwal tidak di masa lalu
                let selectedDate = new Date(scheduleDate + 'T' + start);
                let now = new Date();
                if (selectedDate < now) {
                    Swal.fire('Error', 'Tidak bisa menambahkan jadwal di masa lalu.', 'error');
                    return;
                }

                let formData = $(this).serializeArray().map(field => {
                    if (field.name === 'start_time') return { name: 'start_time', value: start };
                    if (field.name === 'end_time') return { name: 'end_time', value: end };
                    return field;
                });


                // Kirim data via AJAX
                $.ajax({
                    url: '{{ route("admin.schedules.store") }}',
                    method: 'POST',
                    data: $.param(formData),
                    success: function (res) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: res.message || 'Jadwal berhasil disimpan.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        let errorMsg = 'Gagal menyimpan jadwal.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: errorMsg,
                            icon: 'error',
                            confirmButtonText: 'Tutup'
                        });
                    }
                });
            });
        });
    </script>


@endpush