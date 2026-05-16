<?php

namespace App\Exports;

use App\Models\Classes;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClassesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Classes::with('coach')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Nama Kelas', 'Nama Sanggar', 'Deskripsi', 'Pelatih', 'Dibuat Pada'];
    }

    public function map($class): array
    {
        return [
            $class->id,
            $class->name,
            $class->sanggar_name,
            $class->description,
            $class->coach ? $class->coach->name : '-',
            $class->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
