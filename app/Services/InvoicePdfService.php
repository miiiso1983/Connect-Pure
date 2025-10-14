<?php

namespace App\Services;

use App\Modules\Accounting\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoicePdfService
{
    /**
     * Generate PDF for an invoice
     */
    public function generateInvoicePdf(Invoice $invoice): string
    {
        // Load invoice with relationships
        $invoice->load(['customer', 'items', 'taxes']);

        // Prepare data for PDF
        $data = [
            'invoice' => $invoice,
            'company' => $this->getCompanyInfo(),
            'settings' => $this->getPdfSettings(),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('accounting.invoices.pdf', $data);

        // Configure PDF settings
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial',
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'margin_right' => 10,
        ]);

        // Generate filename
        $filename = "invoice-{$invoice->invoice_number}-".now()->format('Y-m-d').'.pdf';
        $filePath = "invoices/pdf/{$filename}";

        // Save PDF to storage
        Storage::disk('local')->put($filePath, $pdf->output());

        return storage_path("app/{$filePath}");
    }

    /**
     * Get company information for PDF
     */
    protected function getCompanyInfo(): array
    {
        return [
            'name' => config('app.name', 'Connect Pure ERP'),
            'address' => config('company.address', 'Company Address'),
            'city' => config('company.city', 'City'),
            'postal_code' => config('company.postal_code', '12345'),
            'country' => config('company.country', 'Saudi Arabia'),
            'phone' => config('company.phone', '+966 XX XXX XXXX'),
            'email' => config('company.email', 'info@company.com'),
            'website' => config('company.website', 'www.company.com'),
            'tax_number' => config('company.tax_number', 'TAX123456789'),
            'logo' => config('company.logo', null),
        ];
    }

    /**
     * Get PDF generation settings
     */
    protected function getPdfSettings(): array
    {
        return [
            'show_logo' => true,
            'show_company_details' => true,
            'show_tax_details' => true,
            'currency_symbol' => 'SAR',
            'date_format' => 'd/m/Y',
            'decimal_places' => 2,
        ];
    }

    /**
     * Clean up old PDF files
     */
    public function cleanupOldPdfs(int $daysOld = 30): int
    {
        $cutoffDate = now()->subDays($daysOld);
        $files = Storage::disk('local')->files('invoices/pdf');
        $deletedCount = 0;

        foreach ($files as $file) {
            $lastModified = Storage::disk('local')->lastModified($file);

            if ($lastModified < $cutoffDate->timestamp) {
                Storage::disk('local')->delete($file);
                $deletedCount++;
            }
        }

        return $deletedCount;
    }

    /**
     * Get PDF file size in human readable format
     */
    public function getPdfFileSize(string $filePath): string
    {
        if (! file_exists($filePath)) {
            return '0 B';
        }

        $bytes = filesize($filePath);
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}
