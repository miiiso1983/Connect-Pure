<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppMessageLog;
use App\Modules\Accounting\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WhatsAppWebhookController extends Controller
{
    /**
     * GET /webhooks/whatsapp — verification handshake for Meta
     */
    public function verify(Request $request): Response
    {
        // Facebook/Meta sends: hub.mode, hub.verify_token, hub.challenge
        $mode = $request->query('hub_mode') ?? $request->query('hub.mode');
        $token = $request->query('hub_verify_token') ?? $request->query('hub.verify_token');
        $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge');

        if ($mode === 'subscribe' && $token === config('services.whatsapp.webhook_verify_token')) {
            return new Response($challenge, 200);
        }

        return new Response('Forbidden', 403);
    }

    /**
     * POST /webhooks/whatsapp — handle status updates
     */
    public function handle(Request $request): Response
    {
        $payload = $request->all();

        // Quick acknowledgement to avoid retries
        $ok = new Response('OK', 200);

        try {
            Log::info('WhatsApp webhook received', [
                'payload' => $payload,
            ]);

            // Typical structure: entry[0].changes[0].value.statuses[*]
            $entries = $payload['entry'] ?? [];
            foreach ($entries as $entry) {
                $changes = $entry['changes'] ?? [];
                foreach ($changes as $change) {
                    $value = $change['value'] ?? [];

                    // Status updates for messages
                    if (! empty($value['statuses']) && is_array($value['statuses'])) {
                        foreach ($value['statuses'] as $status) {
                            $messageId = $status['id'] ?? null; // corresponds to our whatsapp_message_id
                            $statusText = $status['status'] ?? null; // sent, delivered, read, failed
                            $timestamp = $status['timestamp'] ?? null;
                            $errors = $status['errors'] ?? null;

                            if ($messageId) {
                                $invoice = Invoice::where('whatsapp_message_id', $messageId)->first();

                                // Persist log row
                                WhatsAppMessageLog::create([
                                    'invoice_id' => $invoice?->id,
                                    'message_id' => $messageId,
                                    'status' => $statusText,
                                    'payload' => $status,
                                ]);

                                if ($invoice) {
                                    Log::info('WhatsApp status for invoice', [
                                        'invoice_id' => $invoice->id,
                                        'message_id' => $messageId,
                                        'status' => $statusText,
                                        'timestamp' => $timestamp,
                                    ]);

                                    // Optionally mark viewed_at when WhatsApp marks as read
                                    if ($statusText === 'read') {
                                        try {
                                            $invoice->update(['viewed_at' => now()]);
                                        } catch (\Throwable $e) {
                                            // In case the column doesn't exist or is guarded; just log and continue
                                            Log::warning('Failed to update viewed_at on invoice from WhatsApp webhook', [
                                                'invoice_id' => $invoice->id,
                                                'error' => $e->getMessage(),
                                            ]);
                                        }
                                    }

                                    if ($statusText === 'failed' && $errors) {
                                        Log::error('WhatsApp message failed', [
                                            'invoice_id' => $invoice->id,
                                            'message_id' => $messageId,
                                            'errors' => $errors,
                                        ]);
                                    }
                                } else {
                                    Log::warning('Webhook status for unknown WhatsApp message id', [
                                        'message_id' => $messageId,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Error processing WhatsApp webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return $ok;
    }
}
