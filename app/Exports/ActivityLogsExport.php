<?php

namespace App\Exports;

use App\Models\ActivityLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ActivityLogsExport implements FromCollection, WithHeadings, WithColumnWidths, WithStyles
{
    /**
     * Return the data to be exported.
     */
    public function collection()
    {
        return ActivityLog::with('user')
            ->get()
            ->map(function ($log) {
                return [
                    $log->id,
                    $log->user ? $log->user->name : 'Guest',
                    $log->user ? $log->user->email : 'N/A',
                    ucfirst($log->action),
                    $log->model_type ? class_basename($log->model_type) : 'N/A',
                    $log->model_id ?? 'N/A',
                    $log->description,
                    $log->ip_address,
                    $log->created_at->format('Y-m-d H:i:s'),
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
            'User',
            'Email',
            'Action',
            'Model Type',
            'Model ID',
            'Description',
            'IP Address',
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
            'B' => 20,
            'C' => 25,
            'D' => 15,
            'E' => 15,
            'F' => 12,
            'G' => 40,
            'H' => 18,
            'I' => 20,
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
