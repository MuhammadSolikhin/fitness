@extends('layouts.app')
@section('title', 'Jadwal Mengajar')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pelatih.dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Jadwal Mengajar</li>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let calendarEl = document.getElementById('calendar');

            let calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                themeSystem: 'bootstrap',
                locale: 'id',
                timeZone: 'Asia/Jakarta',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                events: '{{ route('pelatih.jadwal.events') }}',
                eventDidMount: function (info) {
                    let titleEl = info.el.querySelector('.fc-event-title');
                    if (titleEl) {
                        titleEl.innerHTML = info.event.title;
                    }
                }
            });

            calendar.render();
        });
    </script>
@endpush