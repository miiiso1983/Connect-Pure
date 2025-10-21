@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-12">
  <div class="bg-white rounded-lg shadow p-6 text-center">
    <h1 class="text-2xl font-bold mb-4">{{ __('accounting.payment_successful') }}</h1>
    <p class="mb-2">{{ __('accounting.thank_you_for_payment') }}</p>
    <p class="mb-2">{{ __('accounting.invoice_number') }}: <strong>{{ $invoice->invoice_number }}</strong></p>
    <p class="mb-2">{{ __('accounting.paid_amount') }}: <strong>{{ number_format((float)$payment->amount, 2) }} {{ $payment->currency }}</strong></p>
  </div>
</div>
@endsection

