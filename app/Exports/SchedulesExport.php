<?php

namespace App\Exports;

use App\Models\Schedule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SchedulesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Schedule::with('class')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Nama Kelas', 'Hari', 'Tanggal', 'Jam Mulai', 'Jam Selesai', 'Dibuat Pada'];
    }

    public function map($schedule): array
    {
        return [
            $schedule->id,
            $schedule->class ? $schedule->class->name : '-',
            $schedule->day_of_week,
            $schedule->schedule_date,
            $schedule->start_time,
            $schedule->end_time,
            $schedule->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
