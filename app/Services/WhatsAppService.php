<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiUrl;

    protected $accessToken;

    protected $phoneNumberId;

    protected $businessAccountId;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url', 'https://graph.facebook.com/v18.0');
        $this->accessToken = config('services.whatsapp.access_token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
        $this->businessAccountId = config('services.whatsapp.business_account_id');
    }

    /**
     * Send a text message via WhatsApp
     */
    public function sendTextMessage(string $to, string $message): array
    {
        $url = "{$this->apiUrl}/{$this->phoneNumberId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $this->formatPhoneNumber($to),
            'type' => 'text',
            'text' => [
                'body' => $message,
            ],
        ];

        return $this->makeRequest($url, $payload);
    }

    /**
     * Send a document (PDF) via WhatsApp
     */
    public function sendDocument(string $to, string $filePath, string $filename, ?string $caption = null): array
    {
        // First upload the document to get media ID
        $mediaId = $this->uploadMedia($filePath, 'document');

        if (! $mediaId) {
            return ['success' => false, 'error' => 'Failed to upload document'];
        }

        $url = "{$this->apiUrl}/{$this->phoneNumberId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $this->formatPhoneNumber($to),
            'type' => 'document',
            'document' => [
                'id' => $mediaId,
                'filename' => $filename,
            ],
        ];

        if ($caption) {
            $payload['document']['caption'] = $caption;
        }

        return $this->makeRequest($url, $payload);
    }

    /**
     * Send invoice with template message
     */
    public function sendInvoiceMessage(string $to, array $invoiceData, ?string $pdfPath = null): array
    {
        $message = $this->buildInvoiceMessage($invoiceData);

        // Send text message first
        $textResult = $this->sendTextMessage($to, $message);

        // If PDF is provided, send it as attachment
        if ($pdfPath && file_exists($pdfPath)) {
            $filename = "Invoice-{$invoiceData['invoice_number']}.pdf";
            $documentResult = $this->sendDocument($to, $pdfPath, $filename, "Invoice {$invoiceData['invoice_number']} - PDF Copy");

            return [
                'success' => $textResult['success'] && $documentResult['success'],
                'text_message' => $textResult,
                'document_message' => $documentResult,
            ];
        }

        return $textResult;
    }

    /**
     * Upload media to WhatsApp
     */
    protected function uploadMedia(string $filePath, string $type = 'document'): ?string
    {
        $url = "{$this->apiUrl}/{$this->phoneNumberId}/media";

        try {
            $response = Http::withToken($this->accessToken)
                ->attach('file', file_get_contents($filePath), basename($filePath))
                ->post($url, [
                    'messaging_product' => 'whatsapp',
                    'type' => $type,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return $data['id'] ?? null;
            }

            Log::error('WhatsApp media upload failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('WhatsApp media upload exception', [
                'error' => $e->getMessage(),
                'file' => $filePath,
            ]);

            return null;
        }
    }

    /**
     * Make HTTP request to WhatsApp API
     */
    protected function makeRequest(string $url, array $payload): array
    {
        try {
            $response = Http::withToken($this->accessToken)
                ->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('WhatsApp message sent successfully', [
                    'to' => $payload['to'],
                    'type' => $payload['type'],
                    'message_id' => $data['messages'][0]['id'] ?? null,
                ]);

                return [
                    'success' => true,
                    'message_id' => $data['messages'][0]['id'] ?? null,
                    'data' => $data,
                ];
            }

            $errorData = $response->json();
            Log::error('WhatsApp API error', [
                'status' => $response->status(),
                'error' => $errorData,
                'payload' => $payload,
            ]);

            return [
                'success' => false,
                'error' => $errorData['error']['message'] ?? 'Unknown error',
                'error_code' => $errorData['error']['code'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('WhatsApp request exception', [
                'error' => $e->getMessage(),
                'url' => $url,
                'payload' => $payload,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build invoice message text
     */
    protected function buildInvoiceMessage(array $invoiceData): string
    {
        $companyName = config('app.name', 'Connect Pure ERP');

        $message = "🧾 *Invoice from {$companyName}*\n\n".
               "📄 Invoice #: {$invoiceData['invoice_number']}\n".
               "📅 Date: {$invoiceData['invoice_date']}\n".
               "⏰ Due Date: {$invoiceData['due_date']}\n".
               "💰 Amount: {$invoiceData['total_amount']} {$invoiceData['currency']}\n\n".
               "Dear {$invoiceData['customer_name']},\n\n".
               "We have generated a new invoice for you. Please find the details above.\n\n".
               "📎 The PDF invoice is attached to this message.\n\n";

        if (!empty($invoiceData['payment_link_url'])) {
            $message .= "🔗 Pay securely online: {$invoiceData['payment_link_url']}\n\n";
        }

        $message .= "💳 Payment can be made through:\n".
               "• Bank Transfer\n".
               "• Online Payment Portal\n".
               "• Cash/Cheque\n\n".
               "📞 For any questions, please contact us.\n\n".
               'Thank you for your business! 🙏';

        return $message;
    }

    /**
     * Format phone number for WhatsApp API
     */
    protected function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove all non-numeric characters
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);

        // If number doesn't start with country code, assume it's local
        if (! str_starts_with($cleaned, '966') && ! str_starts_with($cleaned, '+966')) {
            // Add Saudi Arabia country code if not present
            if (str_starts_with($cleaned, '0')) {
                $cleaned = '966'.substr($cleaned, 1);
            } else {
                $cleaned = '966'.$cleaned;
            }
        }

        // Remove + if present
        $cleaned = ltrim($cleaned, '+');

        return $cleaned;
    }

    /**
     * Validate WhatsApp configuration
     */
    public function isConfigured(): bool
    {
        return ! empty($this->accessToken) &&
               ! empty($this->phoneNumberId) &&
               ! empty($this->businessAccountId);
    }

    /**
     * Get WhatsApp business profile
     */
    public function getBusinessProfile(): array
    {
        $url = "{$this->apiUrl}/{$this->phoneNumberId}";

        try {
            $response = Http::withToken($this->accessToken)->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            return ['error' => 'Failed to fetch business profile'];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
