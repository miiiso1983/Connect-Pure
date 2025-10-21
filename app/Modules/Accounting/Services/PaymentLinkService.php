<?php

namespace App\Modules\Accounting\Services;

use App\Modules\Accounting\Models\Invoice;
use App\Modules\Accounting\Models\PaymentLink;
use Illuminate\Support\Str;

class PaymentLinkService
{
    public function createForInvoice(Invoice $invoice, ?float $amount = null, ?\DateTimeInterface $expiresAt = null): PaymentLink
    {
        // Reuse existing pending non-expired link if exists
        $existing = PaymentLink::where('invoice_id', $invoice->id)
            ->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if ($existing) {
            return $existing;
        }

        $link = new PaymentLink();
        $link->invoice_id = $invoice->id;
        $link->token = (string) Str::uuid();
        $link->amount = $amount ?? (float) $invoice->balance_due;
        $link->currency = $invoice->currency ?? 'SAR';
        $link->status = 'pending';
        $link->expires_at = $expiresAt ? \Carbon\Carbon::instance(\DateTimeImmutable::createFromInterface($expiresAt)) : now()->addDays(7);
        $link->save();

        return $link;
    }

    public function buildUrl(PaymentLink $link): string
    {
        return url('/pay/'.$link->token);
    }
}

