@extends('layouts.app')

@section('title', __('accounting.cash_flow_statement'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('accounting.cash_flow_statement') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('accounting.operating_investing_financing_activities') }}</p>
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

    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('modules.accounting.reports.cash-flow') }}" class="flex items-end space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('accounting.start_date') }}</label>
                <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="form-input">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('accounting.end_date') }}</label>
                <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="form-input">
            </div>
            <button type="submit" class="btn-primary">{{ __('accounting.generate_report') }}</button>
        </form>
    </div>

    <!-- Cash Flow Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <x-stat-card
            title="{{ __('accounting.operating_cash_flow') }}"
            :value="'$' . number_format($netOperatingCashFlow, 2)"
            :color="$netOperatingCashFlow >= 0 ? 'green' : 'red'"
            :icon="'<svg class=\'w-6 h-6 text-' . ($netOperatingCashFlow >= 0 ? 'green' : 'red') . '-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('accounting.investing_cash_flow') }}"
            :value="'$' . number_format($netInvestingCashFlow, 2)"
            :color="$netInvestingCashFlow >= 0 ? 'green' : 'red'"
            :icon="'<svg class=\'w-6 h-6 text-' . ($netInvestingCashFlow >= 0 ? 'green' : 'red') . '-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('accounting.financing_cash_flow') }}"
            :value="'$' . number_format($netFinancingCashFlow, 2)"
            :color="$netFinancingCashFlow >= 0 ? 'green' : 'red'"
            :icon="'<svg class=\'w-6 h-6 text-' . ($netFinancingCashFlow >= 0 ? 'green' : 'red') . '-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('accounting.net_cash_flow') }}"
            :value="'$' . number_format($netCashFlow, 2)"
            :color="$netCashFlow >= 0 ? 'green' : 'red'"
            :icon="'<svg class=\'w-6 h-6 text-' . ($netCashFlow >= 0 ? 'green' : 'red') . '-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1\'></path></svg>'"
        />
    </div>

    <!-- Cash Flow Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Operating Activities -->
        <x-card title="{{ __('accounting.operating_activities') }}">
            <div class="space-y-3">
                @foreach($operatingCashFlow as $key => $value)
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-900">{{ __(ucfirst(str_replace('_', ' ', $key))) }}</span>
                        <span class="font-bold {{ $value >= 0 ? 'text-green-600' : 'text-red-600' }}">${{ number_format($value, 2) }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between items-center py-3 border-t-2 border-gray-300 mt-4">
                    <span class="font-bold text-gray-900">{{ __('accounting.net_operating_cash_flow') }}</span>
                    <span class="font-bold {{ $netOperatingCashFlow >= 0 ? 'text-green-600' : 'text-red-600' }} text-lg">${{ number_format($netOperatingCashFlow, 2) }}</span>
                </div>
            </div>
        </x-card>

        <!-- Investing Activities -->
        <x-card title="{{ __('accounting.investing_activities') }}">
            <div class="space-y-3">
                @foreach($investingCashFlow as $key => $value)
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-900">{{ __(ucfirst(str_replace('_', ' ', $key))) }}</span>
                        <span class="font-bold {{ $value >= 0 ? 'text-green-600' : 'text-red-600' }}">${{ number_format($value, 2) }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between items-center py-3 border-t-2 border-gray-300 mt-4">
                    <span class="font-bold text-gray-900">{{ __('accounting.net_investing_cash_flow') }}</span>
                    <span class="font-bold {{ $netInvestingCashFlow >= 0 ? 'text-green-600' : 'text-red-600' }} text-lg">${{ number_format($netInvestingCashFlow, 2) }}</span>
                </div>
            </div>
        </x-card>

        <!-- Financing Activities -->
        <x-card title="{{ __('accounting.financing_activities') }}">
            <div class="space-y-3">
                @foreach($financingCashFlow as $key => $value)
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="font-medium text-gray-900">{{ __(ucfirst(str_replace('_', ' ', $key))) }}</span>
                        <span class="font-bold {{ $value >= 0 ? 'text-green-600' : 'text-red-600' }}">${{ number_format($value, 2) }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between items-center py-3 border-t-2 border-gray-300 mt-4">
                    <span class="font-bold text-gray-900">{{ __('accounting.net_financing_cash_flow') }}</span>
                    <span class="font-bold {{ $netFinancingCashFlow >= 0 ? 'text-green-600' : 'text-red-600' }} text-lg">${{ number_format($netFinancingCashFlow, 2) }}</span>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Net Cash Flow Summary -->
    <x-card title="{{ __('accounting.net_cash_flow_summary') }}">
        <div class="bg-gray-50 rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">{{ __('accounting.cash_flow_breakdown') }}</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>{{ __('accounting.operating_activities') }}:</span>
                            <span class="font-medium {{ $netOperatingCashFlow >= 0 ? 'text-green-600' : 'text-red-600' }}">${{ number_format($netOperatingCashFlow, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>{{ __('accounting.investing_activities') }}:</span>
                            <span class="font-medium {{ $netInvestingCashFlow >= 0 ? 'text-green-600' : 'text-red-600' }}">${{ number_format($netInvestingCashFlow, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>{{ __('accounting.financing_activities') }}:</span>
                            <span class="font-medium {{ $netFinancingCashFlow >= 0 ? 'text-green-600' : 'text-red-600' }}">${{ number_format($netFinancingCashFlow, 2) }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-2 {{ $netCashFlow >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            <span class="font-bold">{{ __('accounting.net_cash_flow') }}:</span>
                            <span class="font-bold">${{ number_format($netCashFlow, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">{{ __('accounting.cash_flow_analysis') }}</h4>
                    @if($netCashFlow >= 0)
                        <div class="flex items-center text-green-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="font-medium">{{ __('accounting.positive_cash_flow') }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">{{ __('accounting.company_generating_cash') }}</p>
                    @else
                        <div class="flex items-center text-red-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span class="font-medium">{{ __('accounting.negative_cash_flow') }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">{{ __('accounting.company_using_more_cash') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </x-card>

    <!-- Daily Cash Flow Chart (if available) -->
    @if($dailyCashFlow->count() > 0)
    <x-card title="{{ __('accounting.daily_cash_flow_trend') }}">
        <div class="h-64 flex items-center justify-center text-gray-500">
            <div class="text-center">
                <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <p>{{ __('accounting.chart_placeholder') }}</p>
                <p class="text-sm">{{ __('accounting.daily_cash_flow_data_available') }}</p>
            </div>
        </div>
    </x-card>
    @endif

    <!-- Report Footer -->
    <div class="text-center text-sm text-gray-500 mt-8">
        <p>{{ __('accounting.report_generated_on') }} {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>{{ __('accounting.period') }}: {{ \Carbon\Carbon::parse($startDate)->format('F j, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('F j, Y') }}</p>
    </div>
</div>
@endsection
