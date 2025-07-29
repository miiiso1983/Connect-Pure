<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ContactsTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            [
                'Ahmed Al-Rashid',
                'Al-Shifa Pharmacy',
                'ahmed@alshifa.com',
                '+971-50-123-4567',
                'client',
                'closed_won',
                'Large pharmacy chain interested in our ERP solution.',
                '15000.00',
                'Website',
                'Sales Rep 1',
                '2024-08-15 10:00:00'
            ],
            [
                'Sarah Mohammed',
                'Noor Healthcare',
                'sarah@noorhc.com',
                '+971-55-987-6543',
                'lead',
                'qualified',
                'Small clinic looking for basic CRM features.',
                '8500.00',
                'Referral',
                'Sales Rep 2',
                '2024-08-10 14:30:00'
            ],
            [
                'Omar Hassan',
                'Seha Medical Center',
                'omar@seha.ae',
                '+971-52-456-7890',
                'lead',
                'proposal',
                'Large medical center requiring full ERP implementation.',
                '25000.00',
                'Trade Show',
                'Sales Rep 1',
                '2024-08-05 09:15:00'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'name',
            'company',
            'email',
            'phone',
            'type',
            'status',
            'notes',
            'potential_value',
            'source',
            'assigned_to',
            'next_follow_up'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as header
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => Color::COLOR_WHITE],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '2563eb'], // Blue background
                ],
            ],
        ];
    }
}
