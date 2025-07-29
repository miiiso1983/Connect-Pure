@extends('layouts.app')

@section('title', __('accounting.currencies'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('accounting.currencies') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('accounting.manage_currencies_exchange_rates') }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.accounting.index') }}" class="btn-secondary">
                {{ __('accounting.back_to_accounting') }}
            </a>
            <button onclick="updateExchangeRates()" class="btn-outline">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                {{ __('accounting.update_rates') }}
            </button>
            <a href="{{ route('modules.accounting.currencies.create') }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ __('accounting.add_currency') }}
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <x-stat-card
            title="{{ __('accounting.total_currencies') }}"
            :value="$summary['total_currencies']"
            color="blue"
            :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('accounting.active_currencies') }}"
            :value="$summary['active_currencies']"
            color="green"
            :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('accounting.base_currency') }}"
            :value="$summary['base_currency'] ? $summary['base_currency']->code : __('accounting.not_set')"
            color="purple"
            :icon="'<svg class=\'w-6 h-6 text-purple-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('accounting.last_updated') }}"
            :value="$summary['last_updated'] ? $summary['last_updated']->diffForHumans() : __('accounting.never')"
            color="orange"
            :icon="'<svg class=\'w-6 h-6 text-orange-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />
    </div>

    <!-- Filters -->
    <x-card title="{{ __('accounting.filters') }}">
        <form method="GET" action="{{ route('modules.accounting.currencies.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">{{ __('accounting.search') }}</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" 
                       placeholder="{{ __('accounting.search_currencies') }}" class="form-input">
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">{{ __('accounting.status') }}</label>
                <select id="status" name="status" class="form-select">
                    <option value="">{{ __('accounting.all_statuses') }}</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('accounting.active') }}</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('accounting.inactive') }}</option>
                </select>
            </div>

            <div class="flex items-end space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    {{ __('accounting.search') }}
                </button>
                <a href="{{ route('modules.accounting.currencies.index') }}" class="btn-secondary">
                    {{ __('accounting.clear') }}
                </a>
            </div>
        </form>
    </x-card>

    <!-- Currencies List -->
    <x-card title="{{ __('accounting.currencies_list') }}">
        @if($currencies->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.currency') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.exchange_rate') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.format') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.last_updated') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($currencies as $currency)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-sm font-bold text-blue-600">{{ $currency->symbol }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 flex items-center">
                                                {{ $currency->code }}
                                                @if($currency->is_base_currency)
                                                    <span class="ml-2 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                                        {{ __('accounting.base') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $currency->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($currency->is_base_currency)
                                            <span class="text-purple-600 font-medium">1.000000</span>
                                        @else
                                            {{ number_format($currency->exchange_rate, 6) }}
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ __('accounting.per_base_currency') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $currency->symbol_position === 'before' ? $currency->symbol : '' }}1{{ $currency->thousands_separator }}234{{ $currency->decimal_separator }}56{{ $currency->symbol_position === 'after' ? $currency->symbol : '' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $currency->decimal_places }} {{ __('accounting.decimal_places') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $currency->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $currency->is_active ? __('accounting.active') : __('accounting.inactive') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $currency->updated_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                        <a href="{{ route('modules.accounting.currencies.show', $currency) }}" class="text-blue-600 hover:text-blue-900">
                                            {{ __('accounting.view') }}
                                        </a>
                                        <a href="{{ route('modules.accounting.currencies.edit', $currency) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ __('accounting.edit') }}
                                        </a>
                                        @if(!$currency->is_base_currency)
                                            <form action="{{ route('modules.accounting.currencies.destroy', $currency) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" 
                                                        onclick="return confirm('{{ __('accounting.confirm_delete_currency') }}')">
                                                    {{ __('accounting.delete') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $currencies->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('accounting.no_currencies_found') }}</h3>
                <p class="text-gray-600 mb-6">{{ __('accounting.no_currencies_match_criteria') }}</p>
                <a href="{{ route('modules.accounting.currencies.create') }}" class="btn-primary">
                    {{ __('accounting.add_first_currency') }}
                </a>
            </div>
        @endif
    </x-card>
</div>

<script>
function updateExchangeRates() {
    if (confirm('{{ __('accounting.confirm_update_exchange_rates') }}')) {
        // Show loading state
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>{{ __('accounting.updating') }}...';
        button.disabled = true;

        // Make AJAX request to update rates
        fetch('{{ route('modules.accounting.currencies.update-exchange-rates') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || '{{ __('accounting.error_updating_rates') }}');
                button.innerHTML = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            alert('{{ __('accounting.error_updating_rates') }}');
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
}
</script>
@endsection
