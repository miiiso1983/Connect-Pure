<?php

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Models\PaymentLink;
use App\Modules\Accounting\Services\PaymentLinkService;
use Illuminate\Http\Request;

class PaymentLinkController extends Controller
{
    public function show(string $token)
    {
        $link = PaymentLink::where('token', $token)->with('invoice.customer')->firstOrFail();

        if ($link->status !== 'pending' || $link->is_expired) {
            return response()->view('public.payments.expired', compact('link'), 410);
        }

        $invoice = $link->invoice;

        return view('public.payments.show', compact('link', 'invoice'));
    }

    public function simulateSuccess(Request $request, string $token)
    {
        $link = PaymentLink::where('token', $token)->with('invoice.customer')->firstOrFail();
        $invoice = $link->invoice;

        if ($link->status !== 'pending' || $link->is_expired) {
            return redirect()->to(url('/pay/'.$token))->withErrors(['error' => __('accounting.payment_link_not_valid')]);
        }

        // Use invoice method to add payment
        $amount = (float) ($link->amount ?? $invoice->balance_due);
        $payment = $invoice->addPayment($amount, [
            'payment_date' => now(),
            'method' => 'online',
            'reference_number' => 'PAYLINK-'.substr($token, 0, 8),
            'notes' => 'Simulated online payment via payment link',
        ]);

        // Update link status
        $link->status = 'paid';
        $link->paid_at = now();
        $link->save();

        return view('public.payments.thankyou', compact('link', 'invoice', 'payment'));
    }

    public function cancel(string $token)
    {
        $link = PaymentLink::where('token', $token)->with('invoice')->firstOrFail();
        if ($link->status === 'pending') {
            $link->status = 'cancelled';
            $link->save();
        }

        return redirect()->to(url('/pay/'.$token));
    }
}

