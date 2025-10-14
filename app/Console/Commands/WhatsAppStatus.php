<?php

namespace App\Console\Commands;

use App\Modules\Accounting\Models\Customer;
use App\Modules\Accounting\Models\Invoice;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class WhatsAppStatus extends Command
{
    protected $signature = 'whatsapp:status';

    protected $description = 'Display WhatsApp integration status and statistics';

    public function handle()
    {
        $this->info('');
        $this->info('📱 WHATSAPP INTEGRATION STATUS');
        $this->info('===============================');

        // Check service configuration
        $whatsAppService = app(WhatsAppService::class);
        $isConfigured = $whatsAppService->isConfigured();

        $this->info('🔧 Configuration Status:');
        if ($isConfigured) {
            $this->info('   ✅ WhatsApp Business API is configured');
            $this->info('   ✅ Ready to send invoice notifications');
        } else {
            $this->error('   ❌ WhatsApp Business API is NOT configured');
            $this->error('   ❌ Please configure API credentials in admin panel');
        }
        $this->info('');

        // Configuration details
        $this->info('⚙️ Configuration Details:');
        $this->info('   • API URL: '.config('services.whatsapp.api_url', 'Not set'));
        $this->info('   • Access Token: '.(config('services.whatsapp.access_token') ? 'Configured' : 'Not set'));
        $this->info('   • Phone Number ID: '.(config('services.whatsapp.phone_number_id') ?: 'Not set'));
        $this->info('   • Business Account ID: '.(config('services.whatsapp.business_account_id') ?: 'Not set'));
        $this->info('');

        // Customer statistics
        $totalCustomers = Customer::count();
        $customersWithWhatsApp = Customer::whereNotNull('whatsapp_number')->count();
        $customersWithPhone = Customer::whereNotNull('phone')->count();

        $this->info('👥 Customer Statistics:');
        $this->info("   • Total Customers: {$totalCustomers}");
        $this->info("   • With WhatsApp Numbers: {$customersWithWhatsApp}");
        $this->info("   • With Phone Numbers: {$customersWithPhone}");
        $this->info('   • WhatsApp Coverage: '.($totalCustomers > 0 ? round(($customersWithWhatsApp / $totalCustomers) * 100, 1) : 0).'%');
        $this->info('');

        // Invoice statistics
        $totalInvoices = Invoice::count();
        $invoicesWithWhatsApp = Invoice::whereNotNull('whatsapp_sent_at')->count();
        $recentWhatsAppInvoices = Invoice::whereNotNull('whatsapp_sent_at')
            ->where('whatsapp_sent_at', '>=', now()->subDays(30))
            ->count();

        $this->info('🧾 Invoice Statistics:');
        $this->info("   • Total Invoices: {$totalInvoices}");
        $this->info("   • Sent via WhatsApp: {$invoicesWithWhatsApp}");
        $this->info("   • WhatsApp in Last 30 Days: {$recentWhatsAppInvoices}");
        $this->info('   • WhatsApp Usage Rate: '.($totalInvoices > 0 ? round(($invoicesWithWhatsApp / $totalInvoices) * 100, 1) : 0).'%');
        $this->info('');

        // Recent WhatsApp activity
        $recentActivity = Invoice::whereNotNull('whatsapp_sent_at')
            ->with('customer')
            ->orderBy('whatsapp_sent_at', 'desc')
            ->limit(5)
            ->get();

        if ($recentActivity->count() > 0) {
            $this->info('📊 Recent WhatsApp Activity:');
            foreach ($recentActivity as $invoice) {
                $this->info("   • Invoice {$invoice->invoice_number} to {$invoice->customer->name} - ".
                           $invoice->whatsapp_sent_at->format('M j, Y H:i'));
            }
        } else {
            $this->info('📊 Recent WhatsApp Activity: No recent activity');
        }
        $this->info('');

        // System requirements
        $this->info('🔍 System Requirements:');
        $this->info('   • PHP Extensions:');
        $this->info('     - cURL: '.(extension_loaded('curl') ? '✅ Installed' : '❌ Missing'));
        $this->info('     - JSON: '.(extension_loaded('json') ? '✅ Installed' : '❌ Missing'));
        $this->info('     - OpenSSL: '.(extension_loaded('openssl') ? '✅ Installed' : '❌ Missing'));
        $this->info('   • Storage:');
        $this->info('     - Writable: '.(is_writable(storage_path()) ? '✅ Yes' : '❌ No'));
        $this->info('     - PDF Directory: '.(is_dir(storage_path('app/invoices')) ? '✅ Exists' : '❌ Missing'));
        $this->info('');

        // Integration features
        $this->info('🚀 Available Features:');
        $this->info('   ✅ Automatic invoice notifications');
        $this->info('   ✅ PDF invoice generation and attachment');
        $this->info('   ✅ Professional message templates');
        $this->info('   ✅ Admin configuration interface');
        $this->info('   ✅ Test message functionality');
        $this->info('   ✅ Delivery tracking and logging');
        $this->info('   ✅ Queue-based background processing');
        $this->info('   ✅ Error handling and retry logic');
        $this->info('');

        // Quick actions
        $this->info('⚡ Quick Actions:');
        $this->info('   • Configure WhatsApp: /admin/whatsapp');
        $this->info('   • Create Invoice: /modules/accounting/invoices/create');
        $this->info('   • View Invoices: /modules/accounting/invoices');
        $this->info('   • Test Integration: Admin Panel > WhatsApp > Test');
        $this->info('');

        // Business profile (if configured)
        if ($isConfigured) {
            try {
                $profile = $whatsAppService->getBusinessProfile();
                if (! isset($profile['error'])) {
                    $this->info('🏢 Business Profile:');
                    $this->info('   • Phone Number: '.($profile['display_phone_number'] ?? 'Not available'));
                    $this->info('   • Verified: '.($profile['verified_name'] ?? 'Not available'));
                    $this->info('   • Quality Rating: '.($profile['quality_rating'] ?? 'Not available'));
                }
            } catch (\Exception $e) {
                $this->warn('   ⚠️ Could not fetch business profile: '.$e->getMessage());
            }
            $this->info('');
        }

        // Status summary
        if ($isConfigured && $customersWithWhatsApp > 0) {
            $this->info('🎉 STATUS: WhatsApp integration is READY and OPERATIONAL!');
            $this->info('   Your customers will receive invoice notifications via WhatsApp.');
        } elseif ($isConfigured) {
            $this->warn('⚠️ STATUS: WhatsApp is configured but no customers have WhatsApp numbers.');
            $this->warn('   Add WhatsApp numbers to customer profiles to enable notifications.');
        } else {
            $this->error('❌ STATUS: WhatsApp integration requires configuration.');
            $this->error('   Please configure WhatsApp Business API in the admin panel.');
        }

        $this->info('');
        $this->info('📖 For detailed setup instructions, see: docs/WHATSAPP_INTEGRATION.md');
        $this->info('===============================');

        return 0;
    }
}
