@extends('layouts.app')

@section('title', $account->account_name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $account->account_name }}</h1>
            <p class="text-gray-600 mt-1">{{ $account->account_code }} - {{ __(ucfirst($account->account_type)) }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.accounting.chart-of-accounts.index') }}" class="btn-secondary">
                {{ __('accounting.back_to_accounts') }}
            </a>
            <a href="{{ route('modules.accounting.chart-of-accounts.edit', $account) }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                {{ __('accounting.edit_account') }}
            </a>
        </div>
    </div>

    <!-- Account Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Basic Information -->
        <div class="lg:col-span-2">
            <x-card title="{{ __('accounting.account_information') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('accounting.account_code') }}</label>
                        <div class="mt-1 text-lg font-semibold text-gray-900">{{ $account->account_code }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('accounting.account_name') }}</label>
                        <div class="mt-1 text-lg text-gray-900">{{ $account->account_name }}</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('accounting.account_type') }}</label>
                        <div class="mt-1">
                            <span class="inline-flex px-2 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ __(ucfirst($account->account_type)) }}
                            </span>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('accounting.normal_balance') }}</label>
                        <div class="mt-1 text-lg text-gray-900">{{ __(ucfirst($account->normal_balance)) }}</div>
                    </div>
                    
                    @if($account->parentAccount)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('accounting.parent_account') }}</label>
                        <div class="mt-1">
                            <a href="{{ route('modules.accounting.chart-of-accounts.show', $account->parentAccount) }}" 
                               class="text-blue-600 hover:text-blue-900">
                                {{ $account->parentAccount->account_code }} - {{ $account->parentAccount->account_name }}
                            </a>
                        </div>
                    </div>
                    @endif
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('accounting.status') }}</label>
                        <div class="mt-1">
                            <span class="inline-flex px-2 py-1 text-sm font-semibold rounded-full {{ $account->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $account->is_active ? __('accounting.active') : __('accounting.inactive') }}
                            </span>
                            @if($account->is_system)
                                <span class="ml-2 inline-flex px-2 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                                    {{ __('accounting.system_account') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($account->description)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">{{ __('accounting.description') }}</label>
                    <div class="mt-1 text-gray-900">{{ $account->description }}</div>
                </div>
                @endif
            </x-card>
        </div>

        <!-- Balance Information -->
        <div>
            <x-card title="{{ __('accounting.balance_information') }}">
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ __('accounting.opening_balance') }}</span>
                        <span class="font-semibold text-gray-900">${{ number_format($account->opening_balance, 2) }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ __('accounting.current_balance') }}</span>
                        <span class="font-semibold {{ $account->current_balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            ${{ number_format($account->current_balance, 2) }}
                        </span>
                    </div>
                    
                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">{{ __('accounting.balance_change') }}</span>
                            @php
                                $change = $account->current_balance - $account->opening_balance;
                            @endphp
                            <span class="font-semibold {{ $change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $change >= 0 ? '+' : '' }}${{ number_format($change, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Sub Accounts -->
            @if($account->subAccounts->count() > 0)
            <x-card title="{{ __('accounting.sub_accounts') }}">
                <div class="space-y-2">
                    @foreach($account->subAccounts as $subAccount)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <div>
                                <a href="{{ route('modules.accounting.chart-of-accounts.show', $subAccount) }}" 
                                   class="font-medium text-blue-600 hover:text-blue-900">
                                    {{ $subAccount->account_code }}
                                </a>
                                <div class="text-sm text-gray-500">{{ $subAccount->account_name }}</div>
                            </div>
                            <span class="font-semibold {{ $subAccount->current_balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ${{ number_format($subAccount->current_balance, 2) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </x-card>
            @endif
        </div>
    </div>

    <!-- Recent Transactions -->
    <x-card title="{{ __('accounting.recent_transactions') }}">
        <div class="text-center py-8">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-500">{{ __('accounting.transaction_history_coming_soon') }}</p>
            <p class="text-sm text-gray-400 mt-1">{{ __('accounting.journal_entries_will_appear_here') }}</p>
        </div>
    </x-card>

    <!-- Actions -->
    <div class="flex justify-end space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
        @if(!$account->is_system)
            <form action="{{ route('modules.accounting.chart-of-accounts.destroy', $account) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger" 
                        onclick="return confirm('{{ __('accounting.confirm_delete_account') }}')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    {{ __('accounting.delete_account') }}
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
