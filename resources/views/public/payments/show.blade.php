@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-12">
  <div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold mb-4">{{ __('accounting.pay_invoice') }}</h1>
    <p class="mb-2">{{ __('accounting.invoice_number') }}: <strong>{{ $invoice->invoice_number }}</strong></p>
    <p class="mb-2">{{ __('accounting.customer') }}: <strong>{{ $invoice->customer->name ?? $invoice->customer->company_name }}</strong></p>
    <p class="mb-2">{{ __('accounting.amount_due') }}: <strong>{{ number_format((float)($link->amount ?? $invoice->balance_due), 2) }} {{ $invoice->currency }}</strong></p>
    <p class="mb-6 text-gray-600">{{ __('accounting.secure_payment_message') }}</p>

    <form method="POST" action="{{ url('/pay/'.$link->token.'/simulate-success') }}">
      @csrf
      <button class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">{{ __('accounting.pay_now') }}</button>
    </form>
  </div>
</div>
@endsection

