@extends('layouts.app')

@section('title', $currency->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $currency->name }}</h1>
            <p class="text-gray-600 mt-1">{{ $currency->code }} - {{ __('accounting.currency_details') }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.accounting.currencies.index') }}" class="btn-secondary">
                {{ __('accounting.back_to_currencies') }}
            </a>
            <a href="{{ route('modules.accounting.currencies.edit', $currency) }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                {{ __('accounting.edit_currency') }}
            </a>
        </div>
    </div>

    <!-- Currency Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Basic Information -->
        <div class="lg:col-span-2">
            <x-card title="{{ __('accounting.currency_information') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('accounting.currency_code') }}</label>
                        <div class="mt-1 text-lg font-semibold text-gray-900">{{ $currency->code }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('accounting.currency_name') }}</label>
                        <div class="mt-1 text-lg text-gray-900">{{ $currency->name }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('accounting.currency_symbol') }}</label>
                        <div class="mt-1 text-lg font-semibold text-gray-900">{{ $currency->symbol }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('accounting.exchange_rate') }}</label>
                        <div class="mt-1 text-lg text-gray-900">
                            @if($currency->is_base_currency)
                                <span class="text-purple-600 font-semibold">1.000000 ({{ __('accounting.base') }})</span>
                            @else
                                {{ number_format($currency->exchange_rate, 6) }}
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('accounting.format_example') }}</label>
                        <div class="mt-1 text-lg font-mono text-gray-900">
                            {{ $currency->symbol_position === 'before' ? $currency->symbol : '' }}1{{ $currency->thousands_separator }}234{{ $currency->decimal_separator }}{{ str_repeat('5', $currency->decimal_places) }}{{ $currency->symbol_position === 'after' ? $currency->symbol : '' }}
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('accounting.status') }}</label>
                        <div class="mt-1">
                            <span class="inline-flex px-2 py-1 text-sm font-semibold rounded-full {{ $currency->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $currency->is_active ? __('accounting.active') : __('accounting.inactive') }}
                            </span>
                            @if($currency->is_base_currency)
                                <span class="ml-2 inline-flex px-2 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                                    {{ __('accounting.base_currency') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Usage Statistics -->
        <div>
            <x-card title="{{ __('accounting.usage_statistics') }}">
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ __('accounting.invoices') }}</span>
                        <span class="font-semibold text-gray-900">{{ number_format($stats['invoices_count']) }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ __('accounting.expenses') }}</span>
                        <span class="font-semibold text-gray-900">{{ number_format($stats['expenses_count']) }}</span>
                    </div>
                    
                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">{{ __('accounting.total_invoice_amount') }}</span>
                            <span class="font-semibold text-green-600">
                                {{ $currency->symbol_position === 'before' ? $currency->symbol : '' }}{{ number_format($stats['total_invoice_amount'], $currency->decimal_places) }}{{ $currency->symbol_position === 'after' ? $currency->symbol : '' }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-sm text-gray-600">{{ __('accounting.total_expense_amount') }}</span>
                            <span class="font-semibold text-red-600">
                                {{ $currency->symbol_position === 'before' ? $currency->symbol : '' }}{{ number_format($stats['total_expense_amount'], $currency->decimal_places) }}{{ $currency->symbol_position === 'after' ? $currency->symbol : '' }}
                            </span>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Format Details -->
    <x-card title="{{ __('accounting.format_settings') }}">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('accounting.decimal_places') }}</label>
                <div class="mt-1 text-lg text-gray-900">{{ $currency->decimal_places }}</div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('accounting.symbol_position') }}</label>
                <div class="mt-1 text-lg text-gray-900">
                    {{ $currency->symbol_position === 'before' ? __('accounting.before_amount') : __('accounting.after_amount') }}
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('accounting.thousands_separator') }}</label>
                <div class="mt-1 text-lg font-mono text-gray-900">
                    @if($currency->thousands_separator === ',')
                        {{ __('accounting.comma') }} (,)
                    @elseif($currency->thousands_separator === '.')
                        {{ __('accounting.period') }} (.)
                    @elseif($currency->thousands_separator === ' ')
                        {{ __('accounting.space') }} ( )
                    @else
                        {{ __('accounting.none') }}
                    @endif
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('accounting.decimal_separator') }}</label>
                <div class="mt-1 text-lg font-mono text-gray-900">
                    @if($currency->decimal_separator === '.')
                        {{ __('accounting.period') }} (.)
                    @else
                        {{ __('accounting.comma') }} (,)
                    @endif
                </div>
            </div>
        </div>
    </x-card>

    <!-- Recent Transactions -->
    @if($currency->invoices()->count() > 0 || $currency->expenses()->count() > 0)
    <x-card title="{{ __('accounting.recent_transactions') }}">
        <div class="space-y-4">
            @if($currency->invoices()->latest()->take(5)->count() > 0)
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">{{ __('accounting.recent_invoices') }}</h4>
                    <div class="space-y-2">
                        @foreach($currency->invoices()->latest()->take(5)->get() as $invoice)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <div>
                                    <span class="font-medium text-gray-900">{{ $invoice->invoice_number }}</span>
                                    <span class="text-sm text-gray-500 ml-2">{{ $invoice->customer->name ?? 'N/A' }}</span>
                                </div>
                                <span class="font-semibold text-green-600">
                                    {{ $currency->symbol_position === 'before' ? $currency->symbol : '' }}{{ number_format($invoice->total_amount, $currency->decimal_places) }}{{ $currency->symbol_position === 'after' ? $currency->symbol : '' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($currency->expenses()->latest()->take(5)->count() > 0)
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">{{ __('accounting.recent_expenses') }}</h4>
                    <div class="space-y-2">
                        @foreach($currency->expenses()->latest()->take(5)->get() as $expense)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <div>
                                    <span class="font-medium text-gray-900">{{ $expense->expense_number }}</span>
                                    <span class="text-sm text-gray-500 ml-2">{{ $expense->vendor->name ?? 'N/A' }}</span>
                                </div>
                                <span class="font-semibold text-red-600">
                                    {{ $currency->symbol_position === 'before' ? $currency->symbol : '' }}{{ number_format($expense->amount, $currency->decimal_places) }}{{ $currency->symbol_position === 'after' ? $currency->symbol : '' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </x-card>
    @endif

    <!-- Actions -->
    <div class="flex justify-end space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
        @if(!$currency->is_base_currency)
            <form action="{{ route('modules.accounting.currencies.destroy', $currency) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger" 
                        onclick="return confirm('{{ __('accounting.confirm_delete_currency') }}')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    {{ __('accounting.delete_currency') }}
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
