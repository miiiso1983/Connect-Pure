@extends('layouts.app')

@section('title', 'Invoice #'.($invoice->invoice_number ?? $invoice->id))

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="relative">
        <div class="relative modern-card p-6 flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-bold">Invoice #{{ $invoice->invoice_number ?? $invoice->id }}</h1>
                <p class="text-gray-600 mt-1">Customer: {{ optional($invoice->customer)->display_name ?? optional($invoice->customer)->name ?? '—' }}</p>
                <div class="mt-3 flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $invoice->status_color ?? 'gray' }}-100 text-{{ $invoice->status_color ?? 'gray' }}-800">
                        {{ strtoupper($invoice->status) }}
                    </span>
                    @if($invoice->whatsapp_sent_at)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" title="Sent at {{ $invoice->whatsapp_sent_at->format('Y-m-d H:i') }}">
                            WhatsApp sent
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('modules.accounting.invoices.edit', $invoice) }}" class="btn btn-outline">Edit</a>

                @php
                    $canSendWhatsapp = $invoice->status === 'draft' && (optional($invoice->customer)->whatsapp_number || optional($invoice->customer)->phone);
                @endphp
                @if($canSendWhatsapp)
                    <form action="{{ route('modules.accounting.invoices.send', $invoice) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn btn-primary bg-green-600 hover:bg-green-700"
                            onclick="return confirm('{{ __('accounting.confirm_send_invoice') }}');">
                            {{ __('accounting.send_invoice') }} (WhatsApp)
                        </button>
                    </form>
                @endif

                <a href="{{ route('modules.accounting.invoices.pdf', $invoice) }}" class="btn btn-outline">{{ __('accounting.download_pdf') }}</a>

                <form action="{{ route('modules.accounting.invoices.payment-link', $invoice) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-outline">{{ __('accounting.create_payment_link') }}</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-card>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Invoice Date</span>
                    <span class="font-medium">{{ optional($invoice->invoice_date)->format('Y-m-d') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Due Date</span>
                    <span class="font-medium">{{ optional($invoice->due_date)->format('Y-m-d') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Currency</span>
                    <span class="font-medium">{{ $invoice->currency ?? 'SAR' }}</span>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Amount</span>
                    <span class="font-semibold">${{ number_format((float) $invoice->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Paid</span>
                    <span class="font-medium">${{ number_format((float) $invoice->paid_amount, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Balance Due</span>
                    <span class="font-medium">${{ number_format((float) $invoice->balance_due, 2) }}</span>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Customer Phone</span>
                    <span class="font-medium">{{ optional($invoice->customer)->whatsapp_number ?? optional($invoice->customer)->phone ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Reference</span>
                    <span class="font-medium">{{ $invoice->reference_number ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">PO Number</span>
                    <span class="font-medium">{{ $invoice->po_number ?? '—' }}</span>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Items -->
    <x-card title="Items">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tax %</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($invoice->items as $item)
                        <tr>
                            <td class="px-6 py-4 text-sm">
                                <div class="font-medium text-gray-900">{{ $item->name }}</div>
                                @if(!empty($item->description))
                                    <div class="text-gray-500">{{ $item->description }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-sm">${{ number_format((float) $item->rate, 2) }}</td>
                            <td class="px-6 py-4 text-sm">{{ number_format((float) $item->tax_rate, 2) }}</td>
                            <td class="px-6 py-4 text-sm">${{ number_format((float) ($item->quantity * $item->rate), 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center text-gray-500">No items</td>
                        </tr>
    <!-- Payment Links -->
    <x-card title="{{ __('accounting.payment_links') }}">
        @if (session('payment_link_url'))
            <div class="mb-4 p-3 rounded bg-green-50 text-green-800 flex items-center justify-between">
                <span>{{ __('accounting.payment_link_created') }}: <a class="underline" href="{{ session('payment_link_url') }}" target="_blank">{{ session('payment_link_url') }}</a></span>
                <button type="button" class="btn btn-outline" onclick="navigator.clipboard.writeText('{{ session('payment_link_url') }}'); this.innerText='{{ __('accounting.copied') }}';">{{ __('accounting.copy_link') }}</button>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.link') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.amount') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.expires_at') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($invoice->paymentLinks as $plink)
                        @php $plinkUrl = url('/pay/'.$plink->token); @endphp
                        <tr>
                            <td class="px-6 py-4 text-sm">
                                <a class="text-blue-600 underline" href="{{ $plinkUrl }}" target="_blank">{{ $plinkUrl }}</a>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ number_format((float)($plink->amount ?? $invoice->balance_due), 2) }} {{ $invoice->currency }}</td>
                            <td class="px-6 py-4 text-sm">{{ ucfirst($plink->status) }}</td>
                            <td class="px-6 py-4 text-sm">{{ $plink->expires_at ? $plink->expires_at->format('Y-m-d H:i') : '—' }}</td>
                            <td class="px-6 py-4 text-sm">
                                <button type="button" class="text-gray-700 hover:text-gray-900" onclick="navigator.clipboard.writeText('{{ $plinkUrl }}'); this.innerText='{{ __('accounting.copied') }}';">{{ __('accounting.copy_link') }}</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center text-gray-500">{{ __('accounting.no_payment_links') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection

