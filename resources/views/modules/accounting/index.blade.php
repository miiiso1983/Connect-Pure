@extends('layouts.app')

@section('title', __('accounting.accounting_dashboard'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('accounting.accounting_finance') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('accounting.comprehensive_financial_management') }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.accounting.reports.index') }}" class="btn-secondary">
                {{ __('accounting.view_reports') }}
            </a>
            <a href="{{ route('modules.accounting.invoices.create') }}" class="btn-primary">
                {{ __('accounting.create_invoice') }}
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <x-stat-card
            title="{{ __('accounting.monthly_revenue') }}"
            :value="'$' . number_format($dashboardData['monthly_revenue'] ?? 0, 2)"
            color="blue"
            :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('accounting.monthly_expenses') }}"
            :value="'$' . number_format($dashboardData['monthly_expenses'] ?? 0, 2)"
            color="red"
            :icon="'<svg class=\'w-6 h-6 text-red-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('accounting.net_income') }}"
            :value="'$' . number_format($dashboardData['net_income'] ?? 0, 2)"
            color="green"
            :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('accounting.outstanding_invoices') }}"
            :value="'$' . number_format($dashboardData['outstanding_invoices'] ?? 0, 2)"
            color="orange"
            :icon="'<svg class=\'w-6 h-6 text-orange-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\'></path></svg>'"
        />
    </div>

    <!-- Accounting Modules Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
        <!-- Invoices Module -->
        <a href="{{ route('modules.accounting.invoices.index') }}" class="group">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200 group-hover:border-blue-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 group-hover:text-blue-600">{{ __('accounting.invoices') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('accounting.manage_customer_invoices') }}</p>
                    </div>
                </div>
            </div>
        </a>

        <!-- Expenses Module -->
        <a href="{{ route('modules.accounting.expenses.index') }}" class="group">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200 group-hover:border-red-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center group-hover:bg-red-200 transition-colors">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 group-hover:text-red-600">{{ __('accounting.expenses') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('accounting.track_business_expenses') }}</p>
                    </div>
                </div>
            </div>
        </a>

        <!-- Customers Module -->
        <a href="{{ route('modules.accounting.customers.index') }}" class="group">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200 group-hover:border-green-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 group-hover:text-green-600">{{ __('accounting.customers') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('accounting.manage_customer_database') }}</p>
                    </div>
                </div>
            </div>
        </a>

        <!-- Vendors Module -->
        <a href="{{ route('modules.accounting.vendors.index') }}" class="group">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200 group-hover:border-purple-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 group-hover:text-purple-600">{{ __('accounting.vendors') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('accounting.manage_vendor_relationships') }}</p>
                    </div>
                </div>
            </div>
        </a>

        <!-- Reports Module -->
        <a href="{{ route('modules.accounting.reports.index') }}" class="group">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200 group-hover:border-indigo-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center group-hover:bg-indigo-200 transition-colors">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 group-hover:text-indigo-600">{{ __('accounting.reports') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('accounting.financial_reports_analytics') }}</p>
                    </div>
                </div>
            </div>
        </a>

        <!-- Recurring Payments Module -->
        <a href="{{ route('modules.accounting.recurring.index') }}" class="group">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200 group-hover:border-yellow-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center group-hover:bg-yellow-200 transition-colors">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 group-hover:text-yellow-600">{{ __('accounting.recurring') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('accounting.automated_recurring_payments') }}</p>
                    </div>
                </div>
            </div>
        </a>

        <!-- Chart of Accounts Module -->
        <a href="{{ route('modules.accounting.chart-of-accounts.index') }}" class="group">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200 group-hover:border-teal-300">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center group-hover:bg-teal-200 transition-colors">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 group-hover:text-teal-600">{{ __('accounting.chart_of_accounts') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('accounting.manage_account_structure') }}</p>
                    </div>
                </div>
            </div>
        </a>

        <!-- Settings Module -->
        <a href="{{ route('modules.accounting.currencies.index') }}" class="group">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200 group-hover:border-gray-400">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 group-hover:text-gray-600">{{ __('accounting.settings') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('accounting.currencies_taxes_settings') }}</p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <x-card title="{{ __('accounting.recent_invoices') }}">
                @if(isset($dashboardData['recent_invoices']) && $dashboardData['recent_invoices']->count() > 0)
                    <div class="space-y-4">
                        @foreach($dashboardData['recent_invoices']->take(5) as $invoice)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</h4>
                                            <p class="text-sm text-gray-500">{{ $invoice->customer->display_name }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">${{ number_format($invoice->total_amount, 2) }}</p>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $invoice->status_color }}-100 text-{{ $invoice->status_color }}-800">
                                            {{ __('accounting.' . $invoice->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($dashboardData['recent_invoices']->count() > 5)
                        <div class="mt-4 text-center">
                            <a href="{{ route('modules.accounting.invoices.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                {{ __('accounting.view_all_invoices') }}
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500">{{ __('accounting.no_recent_invoices') }}</p>
                        <a href="{{ route('modules.accounting.invoices.create') }}" class="mt-2 inline-block text-blue-600 hover:text-blue-800">
                            {{ __('accounting.create_first_invoice') }}
                        </a>
                    </div>
                @endif
            </x-card>
        </div>

        <div>
            <x-card title="{{ __('accounting.financial_overview') }}">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">{{ __('accounting.revenue') }}</span>
                        <span class="text-sm font-bold text-gray-900">45%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: 45%"></div>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">{{ __('accounting.expenses') }}</span>
                        <span class="text-sm font-bold text-gray-900">25%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-600 h-2 rounded-full" style="width: 25%"></div>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">{{ __('accounting.profit') }}</span>
                        <span class="text-sm font-bold text-gray-900">20%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: 20%"></div>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">{{ __('accounting.outstanding') }}</span>
                        <span class="text-sm font-bold text-gray-900">10%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-600 h-2 rounded-full" style="width: 10%"></div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

</div>
@endsection
