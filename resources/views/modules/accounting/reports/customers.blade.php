@extends('layouts.app')

@section('title', __('accounting.customer_report'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('accounting.customer_report') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('accounting.customer_revenue_analysis') }}</p>
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
        <form method="GET" action="{{ route('modules.accounting.reports.customers') }}" class="flex items-end space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
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

    <!-- Customer Report -->
    <x-card title="{{ __('accounting.customer_revenue_analysis') }}">
        @if($customers->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.customer') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.contact_info') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.total_revenue') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.invoice_count') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.average_invoice') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($customers as $customer)
                            @php
                                $invoiceCount = $customer->invoices_count ?? 0;
                                $totalRevenue = $customer->invoices_sum_total_amount ?? 0;
                                $averageInvoice = $invoiceCount > 0 ? $totalRevenue / $invoiceCount : 0;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-blue-600">
                                                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $customer->company ?? __('accounting.individual') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $customer->email }}</div>
                                    <div class="text-sm text-gray-500">{{ $customer->phone }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-green-600">${{ number_format($totalRevenue, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $invoiceCount }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${{ number_format($averageInvoice, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $customer->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $customer->is_active ? __('accounting.active') : __('accounting.inactive') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $customers->appends(request()->query())->links() }}
            </div>

            <!-- Summary Statistics -->
            <div class="mt-6 bg-gray-50 rounded-lg p-6">
                <h4 class="font-medium text-gray-900 mb-4">{{ __('accounting.summary_statistics') }}</h4>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $customers->total() }}</div>
                        <div class="text-sm text-gray-600">{{ __('accounting.total_customers') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">${{ number_format($customers->sum('invoices_sum_total_amount'), 2) }}</div>
                        <div class="text-sm text-gray-600">{{ __('accounting.total_revenue') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $customers->sum('invoices_count') }}</div>
                        <div class="text-sm text-gray-600">{{ __('accounting.total_invoices') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">
                            ${{ number_format($customers->sum('invoices_count') > 0 ? $customers->sum('invoices_sum_total_amount') / $customers->sum('invoices_count') : 0, 2) }}
                        </div>
                        <div class="text-sm text-gray-600">{{ __('accounting.average_invoice') }}</div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('accounting.no_customers_found') }}</h3>
                <p class="text-gray-600 mb-6">{{ __('accounting.no_customer_data_for_period') }}</p>
                <a href="{{ route('modules.accounting.customers.create') }}" class="btn-primary">
                    {{ __('accounting.add_customer') }}
                </a>
            </div>
        @endif
    </x-card>

    <!-- Report Footer -->
    <div class="text-center text-sm text-gray-500 mt-8">
        <p>{{ __('accounting.report_generated_on') }} {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>{{ __('accounting.period') }}: {{ \Carbon\Carbon::parse($startDate)->format('F j, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('F j, Y') }}</p>
    </div>
</div>
@endsection
