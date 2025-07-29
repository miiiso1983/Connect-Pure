@extends('layouts.app')

@section('title', __('accounting.vendor_report'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('accounting.vendor_report') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('accounting.vendor_expense_analysis') }}</p>
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
        <form method="GET" action="{{ route('modules.accounting.reports.vendors') }}" class="flex items-end space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
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

    <!-- Vendor Report -->
    <x-card title="{{ __('accounting.vendor_expense_analysis') }}">
        @if($vendors->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.vendor') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.contact_info') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.total_expenses') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.expense_count') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.average_expense') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($vendors as $vendor)
                            @php
                                $expenseCount = $vendor->expenses_count ?? 0;
                                $totalExpenses = $vendor->expenses_sum_amount ?? 0;
                                $averageExpense = $expenseCount > 0 ? $totalExpenses / $expenseCount : 0;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-orange-600">
                                                    {{ strtoupper(substr($vendor->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $vendor->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $vendor->company ?? __('accounting.individual') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $vendor->email }}</div>
                                    <div class="text-sm text-gray-500">{{ $vendor->phone }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-red-600">${{ number_format($totalExpenses, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $expenseCount }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${{ number_format($averageExpense, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $vendor->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $vendor->is_active ? __('accounting.active') : __('accounting.inactive') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $vendors->appends(request()->query())->links() }}
            </div>

            <!-- Summary Statistics -->
            <div class="mt-6 bg-gray-50 rounded-lg p-6">
                <h4 class="font-medium text-gray-900 mb-4">{{ __('accounting.summary_statistics') }}</h4>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $vendors->total() }}</div>
                        <div class="text-sm text-gray-600">{{ __('accounting.total_vendors') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">${{ number_format($vendors->sum('expenses_sum_amount'), 2) }}</div>
                        <div class="text-sm text-gray-600">{{ __('accounting.total_expenses') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $vendors->sum('expenses_count') }}</div>
                        <div class="text-sm text-gray-600">{{ __('accounting.total_transactions') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">
                            ${{ number_format($vendors->sum('expenses_count') > 0 ? $vendors->sum('expenses_sum_amount') / $vendors->sum('expenses_count') : 0, 2) }}
                        </div>
                        <div class="text-sm text-gray-600">{{ __('accounting.average_expense') }}</div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('accounting.no_vendors_found') }}</h3>
                <p class="text-gray-600 mb-6">{{ __('accounting.no_vendor_data_for_period') }}</p>
                <a href="{{ route('modules.accounting.vendors.create') }}" class="btn-primary">
                    {{ __('accounting.add_vendor') }}
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
