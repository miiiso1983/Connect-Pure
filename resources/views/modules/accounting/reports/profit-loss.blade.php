@extends('layouts.app')

@section('title', __('accounting.profit_loss_report'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('accounting.profit_loss_report') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('accounting.revenue_expenses_analysis') }}</p>
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
        <form method="GET" action="{{ route('modules.accounting.reports.profit-loss') }}" class="flex items-end space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
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

    <!-- Report Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <x-stat-card
            title="{{ __('accounting.total_revenue') }}"
            :value="'$' . number_format($revenue->total ?? 0, 2)"
            color="green"
            :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('accounting.total_expenses') }}"
            :value="'$' . number_format($totalExpenses, 2)"
            color="red"
            :icon="'<svg class=\'w-6 h-6 text-red-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('accounting.net_profit') }}"
            :value="'$' . number_format($netProfit, 2)"
            :color="$netProfit >= 0 ? 'green' : 'red'"
            :icon="'<svg class=\'w-6 h-6 text-' . ($netProfit >= 0 ? 'green' : 'red') . '-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('accounting.profit_margin') }}"
            :value="number_format($profitMargin, 1) . '%'"
            :color="$profitMargin >= 0 ? 'blue' : 'red'"
            :icon="'<svg class=\'w-6 h-6 text-' . ($profitMargin >= 0 ? 'blue' : 'red') . '-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z\'></path></svg>'"
        />
    </div>

    <!-- Detailed Report -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Details -->
        <x-card title="{{ __('accounting.revenue_details') }}">
            <div class="space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-gray-200">
                    <span class="font-medium text-gray-900">{{ __('accounting.total_invoices') }}</span>
                    <span class="text-gray-600">{{ $revenue->count ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-200">
                    <span class="font-medium text-gray-900">{{ __('accounting.total_revenue') }}</span>
                    <span class="font-bold text-green-600">${{ number_format($revenue->total ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="font-medium text-gray-900">{{ __('accounting.average_invoice') }}</span>
                    <span class="text-gray-600">${{ number_format(($revenue->count ?? 0) > 0 ? ($revenue->total ?? 0) / $revenue->count : 0, 2) }}</span>
                </div>
            </div>
        </x-card>

        <!-- Expenses by Category -->
        <x-card title="{{ __('accounting.expenses_by_category') }}">
            <div class="space-y-3">
                @forelse($expensesByCategory as $expense)
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <div>
                            <span class="font-medium text-gray-900">{{ ucfirst($expense->category) }}</span>
                            <span class="text-sm text-gray-500 block">{{ $expense->count }} {{ __('accounting.transactions') }}</span>
                        </div>
                        <span class="font-bold text-red-600">${{ number_format($expense->total, 2) }}</span>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500">{{ __('accounting.no_expenses_found') }}</p>
                    </div>
                @endforelse
            </div>
        </x-card>
    </div>

    <!-- Monthly Breakdown -->
    @if($monthlyBreakdown->count() > 0)
    <x-card title="{{ __('accounting.monthly_breakdown') }}">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.month') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.revenue') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.expenses') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.net_profit') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.margin') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($monthlyBreakdown as $month)
                        @php
                            $monthProfit = $month->revenue - $month->expenses;
                            $monthMargin = $month->revenue > 0 ? ($monthProfit / $month->revenue) * 100 : 0;
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::createFromDate($month->year, $month->month, 1)->format('M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-medium">
                                ${{ number_format($month->revenue, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">
                                ${{ number_format($month->expenses, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $monthProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ${{ number_format($monthProfit, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm {{ $monthMargin >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($monthMargin, 1) }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
    @endif
</div>
@endsection
