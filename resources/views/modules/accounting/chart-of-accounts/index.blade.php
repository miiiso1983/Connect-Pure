@extends('layouts.app')

@section('title', __('accounting.chart_of_accounts'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('accounting.chart_of_accounts') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('accounting.manage_account_structure') }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.accounting.index') }}" class="btn-secondary">
                {{ __('accounting.back_to_accounting') }}
            </a>
            <button onclick="exportAccounts()" class="btn-outline">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                {{ __('accounting.export') }}
            </button>
            <a href="{{ route('modules.accounting.chart-of-accounts.create') }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ __('accounting.add_account') }}
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        @php
            $accountTypes = ['asset', 'liability', 'equity', 'revenue', 'expense'];
            $typeColors = [
                'asset' => 'blue',
                'liability' => 'red', 
                'equity' => 'purple',
                'revenue' => 'green',
                'expense' => 'orange'
            ];
            $typeIcons = [
                'asset' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>',
                'liability' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z"></path></svg>',
                'equity' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>',
                'revenue' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>',
                'expense' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>'
            ];
        @endphp

        @foreach($accountTypes as $type)
            @php
                $count = $accounts->where('account_type', $type)->count();
                $color = $typeColors[$type];
                $icon = $typeIcons[$type];
            @endphp
            <x-stat-card
                :title="__(ucfirst($type) . ' Accounts')"
                :value="$count"
                :color="$color"
                :icon="str_replace('w-6 h-6', 'w-6 h-6 text-' . $color . '-600', $icon)"
            />
        @endforeach
    </div>

    <!-- Account Types Tabs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showAccountType('all')" class="account-type-tab active border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    {{ __('accounting.all_accounts') }}
                </button>
                @foreach($accountTypes as $type)
                    <button onclick="showAccountType('{{ $type }}')" class="account-type-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        {{ __(ucfirst($type)) }}
                    </button>
                @endforeach
            </nav>
        </div>

        <div class="p-6">
            <!-- Search and Filters -->
            <div class="mb-6">
                <div class="flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <div class="flex-1">
                        <input type="text" id="search-accounts" placeholder="{{ __('accounting.search_accounts') }}" 
                               class="form-input w-full" onkeyup="filterAccounts()">
                    </div>
                    <div>
                        <select id="status-filter" class="form-select" onchange="filterAccounts()">
                            <option value="">{{ __('accounting.all_statuses') }}</option>
                            <option value="active">{{ __('accounting.active') }}</option>
                            <option value="inactive">{{ __('accounting.inactive') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Accounts Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="accounts-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.account_code') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.account_name') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.type') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.current_balance') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.status') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('accounting.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($accounts as $account)
                            <tr class="account-row hover:bg-gray-50" data-type="{{ $account->account_type }}" data-status="{{ $account->is_active ? 'active' : 'inactive' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $account->account_code }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($account->parent_account_id)
                                            <span class="text-gray-400 mr-2">└─</span>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $account->account_name }}</div>
                                            @if($account->description)
                                                <div class="text-sm text-gray-500">{{ Str::limit($account->description, 50) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $typeColors[$account->account_type] }}-100 text-{{ $typeColors[$account->account_type] }}-800">
                                        {{ __(ucfirst($account->account_type)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium {{ $account->current_balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        ${{ number_format($account->current_balance, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $account->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $account->is_active ? __('accounting.active') : __('accounting.inactive') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                        <a href="{{ route('modules.accounting.chart-of-accounts.show', $account) }}" class="text-blue-600 hover:text-blue-900">
                                            {{ __('accounting.view') }}
                                        </a>
                                        <a href="{{ route('modules.accounting.chart-of-accounts.edit', $account) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ __('accounting.edit') }}
                                        </a>
                                        <form action="{{ route('modules.accounting.chart-of-accounts.destroy', $account) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" 
                                                    onclick="return confirm('{{ __('accounting.confirm_delete_account') }}')">
                                                {{ __('accounting.delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('accounting.no_accounts_found') }}</h3>
                                    <p class="text-gray-600 mb-6">{{ __('accounting.create_first_account') }}</p>
                                    <a href="{{ route('modules.accounting.chart-of-accounts.create') }}" class="btn-primary">
                                        {{ __('accounting.add_account') }}
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($accounts->hasPages())
                <div class="mt-6">
                    {{ $accounts->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Tab functionality
function showAccountType(type) {
    // Update tab appearance
    document.querySelectorAll('.account-type-tab').forEach(tab => {
        tab.classList.remove('active', 'border-blue-500', 'text-blue-600');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    
    event.target.classList.add('active', 'border-blue-500', 'text-blue-600');
    event.target.classList.remove('border-transparent', 'text-gray-500');
    
    // Filter accounts
    const rows = document.querySelectorAll('.account-row');
    rows.forEach(row => {
        if (type === 'all' || row.dataset.type === type) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Search and filter functionality
function filterAccounts() {
    const searchTerm = document.getElementById('search-accounts').value.toLowerCase();
    const statusFilter = document.getElementById('status-filter').value;
    const rows = document.querySelectorAll('.account-row');
    
    rows.forEach(row => {
        const accountCode = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
        const accountName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        const accountStatus = row.dataset.status;
        
        const matchesSearch = accountCode.includes(searchTerm) || accountName.includes(searchTerm);
        const matchesStatus = !statusFilter || accountStatus === statusFilter;
        
        if (matchesSearch && matchesStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Export functionality
function exportAccounts() {
    window.location.href = '{{ route("modules.accounting.chart-of-accounts.export") }}';
}
</script>

<style>
.account-type-tab.active {
    border-color: #3B82F6;
    color: #2563EB;
}
</style>
@endsection
