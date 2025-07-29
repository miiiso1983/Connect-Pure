@extends('layouts.app')

@section('title', __('accounting.accounting_dashboard'))

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css" rel="stylesheet">
<style>
    .stat-card {
        @apply bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200;
    }
    
    .stat-icon {
        @apply w-12 h-12 rounded-lg flex items-center justify-center text-white text-xl;
    }
    
    .chart-container {
        @apply bg-white rounded-xl shadow-sm border border-gray-200 p-6;
    }
    
    .alert-card {
        @apply bg-white rounded-xl shadow-sm border-l-4 p-4 mb-4;
    }
    
    .alert-warning {
        @apply border-yellow-400 bg-yellow-50;
    }
    
    .alert-info {
        @apply border-blue-400 bg-blue-50;
    }
    
    .alert-success {
        @apply border-green-400 bg-green-50;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('accounting.accounting_dashboard') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('accounting.manage_finances_overview') }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.accounting.invoices.create') }}" class="btn-primary">
                <svg class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ __('accounting.create_invoice') }}
            </a>
            <a href="{{ route('modules.accounting.expenses.create') }}" class="btn-secondary">
                <svg class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                {{ __('accounting.record_expense') }}
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Revenue -->
        <div class="stat-card">
            <div class="flex items-center">
                <div class="stat-icon bg-gradient-to-r from-blue-500 to-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="{{ app()->getLocale() === 'ar' ? 'mr-4' : 'ml-4' }}">
                    <p class="text-sm font-medium text-gray-600">{{ __('accounting.monthly_revenue') }}</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($dashboardData['summary_stats']['monthly_revenue'] ?? 0, 2) }}</p>
                    <p class="text-xs text-green-600">+12% {{ __('accounting.from_last_month') }}</p>
                </div>
            </div>
        </div>

        <!-- Total Expenses -->
        <div class="stat-card">
            <div class="flex items-center">
                <div class="stat-icon bg-gradient-to-r from-red-500 to-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                    </svg>
                </div>
                <div class="{{ app()->getLocale() === 'ar' ? 'mr-4' : 'ml-4' }}">
                    <p class="text-sm font-medium text-gray-600">{{ __('accounting.monthly_expenses') }}</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($dashboardData['summary_stats']['monthly_expenses'] ?? 0, 2) }}</p>
                    <p class="text-xs text-red-600">+5% {{ __('accounting.from_last_month') }}</p>
                </div>
            </div>
        </div>

        <!-- Net Income -->
        <div class="stat-card">
            <div class="flex items-center">
                <div class="stat-icon bg-gradient-to-r from-green-500 to-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="{{ app()->getLocale() === 'ar' ? 'mr-4' : 'ml-4' }}">
                    <p class="text-sm font-medium text-gray-600">{{ __('accounting.net_income') }}</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format(($dashboardData['summary_stats']['monthly_revenue'] ?? 0) - ($dashboardData['summary_stats']['monthly_expenses'] ?? 0), 2) }}</p>
                    <p class="text-xs text-green-600">+18% {{ __('accounting.from_last_month') }}</p>
                </div>
            </div>
        </div>

        <!-- Outstanding Invoices -->
        <div class="stat-card">
            <div class="flex items-center">
                <div class="stat-icon bg-gradient-to-r from-yellow-500 to-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="{{ app()->getLocale() === 'ar' ? 'mr-4' : 'ml-4' }}">
                    <p class="text-sm font-medium text-gray-600">{{ __('accounting.outstanding_invoices') }}</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($dashboardData['summary_stats']['outstanding_invoices'] ?? 0, 2) }}</p>
                    <p class="text-xs text-gray-600">{{ $dashboardData['summary_stats']['draft_invoices'] ?? 0 }} {{ __('accounting.draft_invoices') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue vs Expenses Chart -->
        <div class="chart-container">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('accounting.revenue_vs_expenses') }}</h3>
                <p class="text-sm text-gray-500">{{ __('accounting.monthly_comparison') }}</p>
            </div>
            <div class="h-64">
                <canvas id="revenueExpenseChart"></canvas>
            </div>
        </div>

        <!-- Expense Categories Chart -->
        <div class="chart-container">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('accounting.expenses_by_category') }}</h3>
                <p class="text-sm text-gray-500">{{ __('accounting.current_month_breakdown') }}</p>
            </div>
            <div class="h-64">
                <canvas id="expenseCategoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity and Alerts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Invoices -->
        <div class="chart-container">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('accounting.recent_invoices') }}</h3>
                <a href="{{ route('modules.accounting.invoices.index') }}" class="text-sm text-blue-600 hover:text-blue-800">{{ __('accounting.view_all') }}</a>
            </div>
            <div class="space-y-3">
                @forelse($dashboardData['recent_invoices'] ?? [] as $invoice)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $invoice->invoice_number }}</p>
                            <p class="text-sm text-gray-600">{{ $invoice->customer->display_name ?? '' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900">${{ number_format($invoice->total_amount, 2) }}</p>
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-{{ $invoice->status_color }}-100 text-{{ $invoice->status_color }}-800">
                                {{ __('accounting.' . $invoice->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">{{ __('accounting.no_recent_invoices') }}</p>
                        <p class="text-xs text-gray-400">{{ __('accounting.start_by_creating_invoice') }}</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Expenses -->
        <div class="chart-container">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('accounting.recent_expenses') }}</h3>
                <a href="{{ route('modules.accounting.expenses.index') }}" class="text-sm text-blue-600 hover:text-blue-800">{{ __('accounting.view_all') }}</a>
            </div>
            <div class="space-y-3">
                @forelse($dashboardData['recent_expenses'] ?? [] as $expense)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $expense->description }}</p>
                            <p class="text-sm text-gray-600">{{ $expense->vendor->company_name ?? __('accounting.no_vendor') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900">${{ number_format($expense->total_amount, 2) }}</p>
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-{{ $expense->status === 'paid' ? 'green' : ($expense->status === 'pending' ? 'yellow' : 'gray') }}-100 text-{{ $expense->status === 'paid' ? 'green' : ($expense->status === 'pending' ? 'yellow' : 'gray') }}-800">
                                {{ __('accounting.' . $expense->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">{{ __('accounting.no_recent_expenses') }}</p>
                        <p class="text-xs text-gray-400">{{ __('accounting.start_by_recording_expense') }}</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Alerts -->
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('accounting.alerts') }}</h3>
            @forelse($dashboardData['alerts'] ?? [] as $alert)
                <div class="alert-card alert-{{ $alert['type'] }}">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            @if($alert['type'] === 'warning')
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif($alert['type'] === 'info')
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="{{ app()->getLocale() === 'ar' ? 'mr-3' : 'ml-3' }}">
                            <h4 class="text-sm font-medium text-gray-900">{{ $alert['title'] }}</h4>
                            <p class="text-sm text-gray-600">{{ $alert['message'] }}</p>
                            @if(isset($alert['action_url']))
                                <a href="{{ $alert['action_url'] }}" class="text-sm text-blue-600 hover:text-blue-800">{{ $alert['action_text'] }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">{{ __('accounting.all_current') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue vs Expenses Chart
    const revenueExpenseCtx = document.getElementById('revenueExpenseChart').getContext('2d');
    new Chart(revenueExpenseCtx, {
        type: 'line',
        data: {
            labels: @json($dashboardData['revenue_expense_chart']['labels'] ?? []),
            datasets: [{
                label: '{{ __("accounting.revenue") }}',
                data: @json($dashboardData['revenue_expense_chart']['revenue'] ?? []),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            }, {
                label: '{{ __("accounting.expenses") }}',
                data: @json($dashboardData['revenue_expense_chart']['expenses'] ?? []),
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Expense Categories Chart
    const expenseCategoryCtx = document.getElementById('expenseCategoryChart').getContext('2d');
    new Chart(expenseCategoryCtx, {
        type: 'doughnut',
        data: {
            labels: @json($dashboardData['expense_category_chart']['labels'] ?? []),
            datasets: [{
                data: @json($dashboardData['expense_category_chart']['amounts'] ?? []),
                backgroundColor: [
                    'rgb(239, 68, 68)',
                    'rgb(245, 158, 11)',
                    'rgb(34, 197, 94)',
                    'rgb(59, 130, 246)',
                    'rgb(147, 51, 234)',
                    'rgb(236, 72, 153)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush
