@extends('layouts.app')

@section('title', __('accounting.balance_sheet'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('accounting.balance_sheet') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('accounting.assets_liabilities_equity') }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.accounting.reports.index') }}" class="btn-secondary">
                {{ __('accounting.back_to_reports') }}
            </a>
            <button onclick="window.print()" class="btn-primary">
                {{ __('accounting.print_report') }}
            </button>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('modules.accounting.reports.balance-sheet') }}" class="flex items-end space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <div>
                <label for="as_of_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('accounting.as_of_date') }}</label>
                <input type="date" id="as_of_date" name="as_of_date" value="{{ $asOfDate }}" class="form-input">
            </div>
            <button type="submit" class="btn-primary">{{ __('accounting.generate_report') }}</button>
        </form>
    </div>

    <!-- Balance Sheet Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-stat-card
            title="{{ __('accounting.total_assets') }}"
            :value="'$' . number_format($totalAssets, 2)"
            color="blue"
            :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('accounting.total_liabilities') }}"
            :value="'$' . number_format($totalLiabilities, 2)"
            color="red"
            :icon="'<svg class=\'w-6 h-6 text-red-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('accounting.total_equity') }}"
            :value="'$' . number_format($totalEquity, 2)"
            color="green"
            :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1\'></path></svg>'"
        />
    </div>

    <!-- Balance Sheet Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Assets -->
        <x-card title="{{ __('accounting.assets') }}">
            <div class="space-y-3">
                @foreach($assets as $key => $value)
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-900">{{ __(ucfirst(str_replace('_', ' ', $key))) }}</span>
                        <span class="font-bold text-blue-600">${{ number_format($value, 2) }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between items-center py-3 border-t-2 border-gray-300 mt-4">
                    <span class="font-bold text-gray-900">{{ __('accounting.total_assets') }}</span>
                    <span class="font-bold text-blue-600 text-lg">${{ number_format($totalAssets, 2) }}</span>
                </div>
            </div>
        </x-card>

        <!-- Liabilities -->
        <x-card title="{{ __('accounting.liabilities') }}">
            <div class="space-y-3">
                @foreach($liabilities as $key => $value)
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-900">{{ __(ucfirst(str_replace('_', ' ', $key))) }}</span>
                        <span class="font-bold text-red-600">${{ number_format($value, 2) }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between items-center py-3 border-t-2 border-gray-300 mt-4">
                    <span class="font-bold text-gray-900">{{ __('accounting.total_liabilities') }}</span>
                    <span class="font-bold text-red-600 text-lg">${{ number_format($totalLiabilities, 2) }}</span>
                </div>
            </div>
        </x-card>

        <!-- Equity -->
        <x-card title="{{ __('accounting.equity') }}">
            <div class="space-y-3">
                @foreach($equity as $key => $value)
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-900">{{ __(ucfirst(str_replace('_', ' ', $key))) }}</span>
                        <span class="font-bold text-green-600">${{ number_format($value, 2) }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between items-center py-3 border-t-2 border-gray-300 mt-4">
                    <span class="font-bold text-gray-900">{{ __('accounting.total_equity') }}</span>
                    <span class="font-bold text-green-600 text-lg">${{ number_format($totalEquity, 2) }}</span>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Balance Verification -->
    <x-card title="{{ __('accounting.balance_verification') }}">
        <div class="bg-gray-50 rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">{{ __('accounting.accounting_equation') }}</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>{{ __('accounting.assets') }}:</span>
                            <span class="font-medium">${{ number_format($totalAssets, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>{{ __('accounting.liabilities') }} + {{ __('accounting.equity') }}:</span>
                            <span class="font-medium">${{ number_format($totalLiabilities + $totalEquity, 2) }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-2 {{ abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01 ? 'text-green-600' : 'text-red-600' }}">
                            <span class="font-bold">{{ __('accounting.difference') }}:</span>
                            <span class="font-bold">${{ number_format($totalAssets - ($totalLiabilities + $totalEquity), 2) }}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">{{ __('accounting.balance_status') }}</h4>
                    @if(abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01)
                        <div class="flex items-center text-green-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="font-medium">{{ __('accounting.balance_sheet_balanced') }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">{{ __('accounting.accounting_equation_satisfied') }}</p>
                    @else
                        <div class="flex items-center text-red-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span class="font-medium">{{ __('accounting.balance_sheet_unbalanced') }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">{{ __('accounting.review_entries_for_errors') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </x-card>

    <!-- Report Footer -->
    <div class="text-center text-sm text-gray-500 mt-8">
        <p>{{ __('accounting.report_generated_on') }} {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>{{ __('accounting.as_of') }} {{ \Carbon\Carbon::parse($asOfDate)->format('F j, Y') }}</p>
    </div>
</div>
@endsection
