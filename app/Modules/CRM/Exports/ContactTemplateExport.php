<?php

namespace App\Modules\CRM\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ContactTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    /**
     * Return the template data with sample rows.
     */
    public function array(): array
    {
        return [
            [
                'Ahmed Al-Rashid',
                'ahmed.alrashid@example.com',
                '+966501234567',
                'ABC Company',
                'CEO',
                'lead',
                'high',
                'Riyadh',
                'Saudi Arabia',
                'Interested in our premium services',
                'LinkedIn',
                'John Smith'
            ],
            [
                'Sarah Al-Mahmoud',
                'sarah.mahmoud@example.com',
                '+966509876543',
                'XYZ Corporation',
                'Marketing Manager',
                'prospect',
                'medium',
                'Jeddah',
                'Saudi Arabia',
                'Requested product demo',
                'Website',
                'Jane Doe'
            ],
            [
                'Omar Al-Zahrani',
                'omar.zahrani@example.com',
                '+966507654321',
                'Tech Solutions',
                'IT Director',
                'customer',
                'high',
                'Dammam',
                'Saudi Arabia',
                'Existing customer - renewal due',
                'Referral',
                'Mike Johnson'
            ]
        ];
    }

    /**
     * Return the headings for the template.
     */
    public function headings(): array
    {
        return [
            'Name *',
            'Email *',
            'Phone',
            'Company',
            'Position',
            'Status *',
            'Priority',
            'City',
            'Country',
            'Notes',
            'Source',
            'Assigned To'
        ];
    }

    /**
     * Apply styles to the worksheet.
     */
    public function styles(Worksheet $sheet)
    {
        // Header row styling
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3B82F6'], // Blue background
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Data rows styling
        $sheet->getStyle('A2:L4')->applyFromArray([
            'font' => [
                'size' => 11,
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Alternate row colors
        $sheet->getStyle('A2:L2')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8FAFC'], // Light gray
            ],
        ]);

        $sheet->getStyle('A4:L4')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8FAFC'], // Light gray
            ],
        ]);

        // Add borders
        $sheet->getStyle('A1:L4')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);

        // Add instructions below the data
        $sheet->setCellValue('A6', 'INSTRUCTIONS:');
        $sheet->setCellValue('A7', '• Fields marked with * are required');
        $sheet->setCellValue('A8', '• Status options: lead, prospect, customer, inactive');
        $sheet->setCellValue('A9', '• Priority options: low, medium, high');
        $sheet->setCellValue('A10', '• Source options: website, social_media, referral, advertising, cold_call, email, event, other');
        $sheet->setCellValue('A11', '• Delete the sample rows before uploading your data');
        $sheet->setCellValue('A12', '• Maximum 1000 contacts per upload');

        // Style instructions
        $sheet->getStyle('A6:A12')->applyFromArray([
            'font' => [
                'size' => 10,
                'color' => ['rgb' => '6B7280'],
            ],
        ]);

        $sheet->getStyle('A6')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => '374151'],
            ],
        ]);

        return $sheet;
    }

    /**
     * Set column widths.
     */
    public function columnWidths(): array
    {
        return [
            'A' => 20, // Name
            'B' => 25, // Email
            'C' => 15, // Phone
            'D' => 20, // Company
            'E' => 18, // Position
            'F' => 12, // Status
            'G' => 12, // Priority
            'H' => 15, // City
            'I' => 15, // Country
            'J' => 30, // Notes
            'K' => 15, // Source
            'L' => 18, // Assigned To
        ];
    }

    /**
     * Set worksheet title.
     */
    public function title(): string
    {
        return 'Contact Import Template';
    }
}
