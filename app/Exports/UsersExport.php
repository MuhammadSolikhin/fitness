<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return User::all();
    }

    public function headings(): array
    {
        return ['ID', 'Nama', 'Email', 'Role', 'Status Membership', 'Berlaku Sampai', 'Dibuat Pada'];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->role,
            $user->is_membership ? 'Aktif' : 'Tidak Aktif',
            $user->membership_expired_at ? $user->membership_expired_at->format('Y-m-d') : '-',
            $user->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
