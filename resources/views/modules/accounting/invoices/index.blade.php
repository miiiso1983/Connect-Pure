@extends('layouts.app')

@section('title', __('accounting.invoices'))

@section('content')
<div class="space-y-8">
    <!-- Professional Header -->
    <div class="relative">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600/10 via-purple-600/10 to-pink-600/10 rounded-3xl"></div>
        <div class="relative modern-card p-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-2xl">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold text-gradient mb-2">{{ __('accounting.invoices') }}</h1>
                        <p class="text-lg text-gray-600 font-medium">{{ __('accounting.manage_invoices_billing') }}</p>
                        <div class="flex items-center mt-3 space-x-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Last updated: {{ now()->format('M j, Y H:i') }}
                            </div>
                            <div class="flex items-center text-sm text-blue-600">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mr-2 animate-pulse"></div>
                                Live data
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('modules.accounting.invoices.index') }}" class="btn btn-outline">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        {{ __('accounting.view_all') }}
                    </a>
                    <a href="{{ route('modules.accounting.invoices.create') }}" class="btn btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        {{ __('accounting.create_invoice') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <x-stat-card
            title="{{ __('accounting.total_invoices') }}"
            :value="$stats['total_invoices'] ?? 0"
            color="blue"
            :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('accounting.paid_invoices') }}"
            :value="$stats['paid_invoices'] ?? 0"
            color="green"
            :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('accounting.pending_invoices') }}"
            :value="$stats['pending_invoices'] ?? 0"
            color="orange"
            :icon="'<svg class=\'w-6 h-6 text-orange-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('accounting.overdue_invoices') }}"
            :value="$stats['overdue_invoices'] ?? 0"
            color="red"
            :icon="'<svg class=\'w-6 h-6 text-red-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z\'></path></svg>'"
        />
    </div>

    <!-- Invoices Table -->
    <x-card title="{{ __('accounting.invoices_list') }}">
        @if($invoices->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('accounting.invoice_number') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('accounting.customer') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('accounting.date') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('accounting.amount') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('accounting.status') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('accounting.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($invoices as $invoice)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $invoice->invoice_number }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-blue-600">
                                                    {{ substr($invoice->customer->display_name, 0, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $invoice->customer->display_name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $invoice->invoice_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($invoice->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $invoice->status_color }}-100 text-{{ $invoice->status_color }}-800">
                                        {{ __('accounting.' . $invoice->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('modules.accounting.invoices.show', $invoice) }}" class="text-blue-600 hover:text-blue-900">
                                            {{ __('accounting.view') }}
                                        </a>
                                        <a href="{{ route('modules.accounting.invoices.edit', $invoice) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ __('accounting.edit') }}
                                        </a>

                                        @php
                                            $canSendWhatsapp = $invoice->status === 'draft' && optional($invoice->customer)->whatsapp_number || optional($invoice->customer)->phone;
                                        @endphp

                                        @if($canSendWhatsapp)
                                            <form action="{{ route('modules.accounting.invoices.send', $invoice) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-md bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                                                    onclick="return confirm('Send this invoice to the customer via WhatsApp?');"
                                                    title="Send via WhatsApp">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M20.52 3.48A11.91 11.91 0 0012.06 0C5.7 0 .54 5.16.54 11.52c0 2.03.53 4.02 1.54 5.78L0 24l6.85-2c1.67.91 3.56 1.39 5.48 1.39h.01c6.36 0 11.52-5.16 11.52-11.52 0-3.08-1.2-5.98-3.34-8.14zM12.34 21.5h-.01c-1.7 0-3.36-.45-4.82-1.31l-.35-.2-4.07 1.2 1.2-3.97-.23-.37a9.7 9.7 0 01-1.46-5.03c0-5.34 4.35-9.69 9.69-9.69a9.64 9.64 0 016.86 2.84 9.6 9.6 0 012.83 6.85c0 5.34-4.35 9.69-9.64 9.69z"/>
                                                        <path d="M17.44 13.7c-.3-.15-1.76-.86-2.03-.96-.27-.1-.47-.15-.67.15-.2.3-.77.96-.95 1.16-.17.2-.35.22-.65.07-.3-.15-1.27-.47-2.42-1.5-.9-.8-1.5-1.78-1.67-2.08-.17-.3-.02-.46.13-.61.14-.14.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.5-.17 0-.37-.02-.57-.02-.2 0-.52.07-.8.37-.27.3-1.05 1.03-1.05 2.5s1.08 2.9 1.23 3.1c.15.2 2.12 3.23 5.14 4.53.72.31 1.27.5 1.7.64.72.23 1.37.2 1.88.12.57-.08 1.76-.72 2.01-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35z"/>
                                                    </svg>
                                                    <span>Send via WhatsApp</span>
                                                </button>
                                            </form>
                                        @elseif($invoice->whatsapp_sent_at)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" title="WhatsApp sent at {{ optional($invoice->whatsapp_sent_at)->format('Y-m-d H:i') }}">
                                                WhatsApp sent
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($invoices->hasPages())
                <div class="mt-4">
                    {{ $invoices->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500">{{ __('accounting.no_invoices_found') }}</p>
                <a href="{{ route('modules.accounting.invoices.create') }}" class="mt-2 inline-block text-blue-600 hover:text-blue-800">
                    {{ __('accounting.create_first_invoice') }}
                </a>
            </div>
        @endif
    </x-card>
</div>
@endsection
