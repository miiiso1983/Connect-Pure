<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class WhatsAppController extends Controller
{
    protected WhatsAppService $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Display WhatsApp configuration page
     */
    public function index()
    {
        $config = [
            'api_url' => config('services.whatsapp.api_url'),
            'access_token' => config('services.whatsapp.access_token') ? '***' . substr(config('services.whatsapp.access_token'), -4) : null,
            'phone_number_id' => config('services.whatsapp.phone_number_id'),
            'business_account_id' => config('services.whatsapp.business_account_id'),
            'webhook_verify_token' => config('services.whatsapp.webhook_verify_token') ? '***' . substr(config('services.whatsapp.webhook_verify_token'), -4) : null,
        ];

        $isConfigured = $this->whatsAppService->isConfigured();
        $businessProfile = null;

        if ($isConfigured) {
            $businessProfile = $this->whatsAppService->getBusinessProfile();
        }

        return view('admin.whatsapp.index', compact('config', 'isConfigured', 'businessProfile'));
    }

    /**
     * Update WhatsApp configuration
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'access_token' => 'required|string',
            'phone_number_id' => 'required|string',
            'business_account_id' => 'required|string',
            'webhook_verify_token' => 'nullable|string',
        ]);

        // Update environment file
        $this->updateEnvFile([
            'WHATSAPP_ACCESS_TOKEN' => $validated['access_token'],
            'WHATSAPP_PHONE_NUMBER_ID' => $validated['phone_number_id'],
            'WHATSAPP_BUSINESS_ACCOUNT_ID' => $validated['business_account_id'],
            'WHATSAPP_WEBHOOK_VERIFY_TOKEN' => $validated['webhook_verify_token'] ?? '',
        ]);

        // Clear config cache
        Artisan::call('config:clear');

        return redirect()->route('admin.whatsapp.index')
            ->with('success', 'WhatsApp configuration updated successfully');
    }

    /**
     * Test WhatsApp connection
     */
    public function test(Request $request)
    {
        $validated = $request->validate([
            'test_number' => 'required|string',
            'test_message' => 'nullable|string',
        ]);

        $testMessage = $validated['test_message'] ?? 'This is a test message from Connect Pure ERP WhatsApp integration.';

        $result = $this->whatsAppService->sendTextMessage(
            $validated['test_number'],
            $testMessage
        );

        if ($result['success']) {
            return redirect()->route('admin.whatsapp.index')
                ->with('success', 'Test message sent successfully! Message ID: ' . $result['message_id']);
        } else {
            return redirect()->route('admin.whatsapp.index')
                ->with('error', 'Failed to send test message: ' . $result['error']);
        }
    }

    /**
     * Get business profile information
     */
    public function profile()
    {
        $profile = $this->whatsAppService->getBusinessProfile();
        
        return response()->json($profile);
    }

    /**
     * Update environment file
     */
    protected function updateEnvFile(array $data)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        file_put_contents($envFile, $envContent);
    }
}
