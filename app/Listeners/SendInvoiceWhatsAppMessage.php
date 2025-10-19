<?php

namespace App\Listeners;

use App\Events\InvoiceSubmitted;
use App\Models\WhatsAppMessageLog;
use App\Services\InvoicePdfService;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendInvoiceWhatsAppMessage implements ShouldQueue
{
    use InteractsWithQueue;

    protected WhatsAppService $whatsAppService;

    protected InvoicePdfService $pdfService;

    /**
     * Create the event listener.
     */
    public function __construct(WhatsAppService $whatsAppService, InvoicePdfService $pdfService)
    {
        $this->whatsAppService = $whatsAppService;
        $this->pdfService = $pdfService;
    }

    /**
     * Handle the event.
     */
    public function handle(InvoiceSubmitted $event): void
    {
        $invoice = $event->invoice;

        // Load customer relationship
        $invoice->load('customer');

        // Check if customer has WhatsApp number
        $whatsappNumber = $invoice->customer->whatsapp_number ?? $invoice->customer->phone;

        if (empty($whatsappNumber)) {
            Log::warning('Invoice WhatsApp notification skipped - no phone number', [
                'invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer->id,
            ]);

            return;
        }

        // Check if WhatsApp service is configured
        if (! $this->whatsAppService->isConfigured()) {
            Log::warning('Invoice WhatsApp notification skipped - service not configured', [
                'invoice_id' => $invoice->id,
            ]);

            return;
        }

        try {
            // Generate PDF
            $pdfPath = $this->pdfService->generateInvoicePdf($invoice);

            // Prepare invoice data for message
            $invoiceData = [
                'invoice_number' => $invoice->invoice_number,
                'invoice_date' => $invoice->invoice_date->format('d/m/Y'),
                'due_date' => $invoice->due_date->format('d/m/Y'),
                'total_amount' => number_format((float) $invoice->total_amount, 2),
                'currency' => $invoice->currency ?? 'SAR',
                'customer_name' => $invoice->customer->name,
            ];

            // Send WhatsApp message with PDF attachment
            $result = $this->whatsAppService->sendInvoiceMessage(
                $whatsappNumber,
                $invoiceData,
                $pdfPath
            );

            if ($result['success']) {
                $messageId = $result['text_message']['message_id'] ?? null;

                Log::info('Invoice WhatsApp notification sent successfully', [
                    'invoice_id' => $invoice->id,
                    'customer_id' => $invoice->customer->id,
                    'whatsapp_number' => $whatsappNumber,
                    'message_id' => $messageId,
                ]);

                // Update invoice to mark WhatsApp notification as sent
                $invoice->update([
                    'whatsapp_sent_at' => now(),
                    'whatsapp_message_id' => $messageId,
                ]);

                // Persist log row (sent)
                WhatsAppMessageLog::create([
                    'invoice_id' => $invoice->id,
                    'message_id' => $messageId,
                    'status' => 'sent',
                    'payload' => $result,
                ]);
            } else {
                Log::error('Failed to send invoice WhatsApp notification', [
                    'invoice_id' => $invoice->id,
                    'customer_id' => $invoice->customer->id,
                    'whatsapp_number' => $whatsappNumber,
                    'error' => $result['error'] ?? 'Unknown error',
                ]);

                // Persist log row (failed to send)
                WhatsAppMessageLog::create([
                    'invoice_id' => $invoice->id,
                    'message_id' => $result['text_message']['message_id'] ?? null,
                    'status' => 'failed',
                    'payload' => $result,
                ]);
            }

            // Clean up PDF file after sending (optional)
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }

        } catch (\Exception $e) {
            Log::error('Exception while sending invoice WhatsApp notification', [
                'invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw the exception to mark the job as failed
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(InvoiceSubmitted $event, \Throwable $exception): void
    {
        Log::error('Invoice WhatsApp notification job failed', [
            'invoice_id' => $event->invoice->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
