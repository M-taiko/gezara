<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class UsersExport implements FromCollection, WithHeadings, WithColumnWidths, WithStyles
{
    /**
     * Return the data to be exported.
     */
    public function collection()
    {
        return User::with('roles')
            ->get()
            ->map(function ($user) {
                return [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->roles()->pluck('display_name')->implode(', ') ?: 'No Role',
                    ucfirst($user->status),
                    $user->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }

    /**
     * Define the headings for the export.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Role',
            'Status',
            'Created At',
        ];
    }

    /**
     * Set column widths.
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 25,
            'C' => 30,
            'D' => 20,
            'E' => 15,
            'F' => 20,
        ];
    }

    /**
     * Apply styling to the sheet.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '007bff'],
                ],
            ],
        ];
    }
}
