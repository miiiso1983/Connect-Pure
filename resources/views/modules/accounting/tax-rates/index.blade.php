@extends('layouts.app')

@section('title', __('accounting.tax_rates'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('accounting.tax_rates') }}</h1>
            <p class="text-gray-600">{{ __('accounting.manage_tax_rates') }}</p>
        </div>
        <a href="#" class="btn-primary disabled:opacity-50" aria-disabled="true">{{ __('common.create') }}</a>
    </div>

    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.name') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.rate') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.status') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($taxRates as $taxRate)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $taxRate->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($taxRate->rate, 2) }}{{ $taxRate->type === 'percentage' ? '%' : '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $taxRate->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $taxRate->is_active ? __('common.active') : __('common.inactive') }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-500">{{ __('accounting.no_tax_rates_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $taxRates->links() }}
        </div>
    </div>
</div>
@endsection

