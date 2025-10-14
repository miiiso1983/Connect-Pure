<?php

namespace App\Providers;

use App\Events\InvoiceSubmitted;
use App\Listeners\SendInvoiceWhatsAppMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class WhatsAppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register WhatsApp service as singleton
        $this->app->singleton(\App\Services\WhatsAppService::class, function ($app) {
            return new \App\Services\WhatsAppService;
        });

        // Register PDF service as singleton
        $this->app->singleton(\App\Services\InvoicePdfService::class, function ($app) {
            return new \App\Services\InvoicePdfService;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register event listeners
        Event::listen(
            InvoiceSubmitted::class,
            SendInvoiceWhatsAppMessage::class
        );
    }
}
