@extends('layouts.app')

@section('title', __('accounting.add_account'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('accounting.add_account') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('accounting.create_new_account') }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.accounting.chart-of-accounts.index') }}" class="btn-secondary">
                {{ __('accounting.back_to_accounts') }}
            </a>
        </div>
    </div>

    <!-- Form -->
    <x-card title="{{ __('accounting.account_information') }}">
        <form action="{{ route('modules.accounting.chart-of-accounts.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Account Code -->
                <div>
                    <label for="account_code" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('accounting.account_code') }} *
                    </label>
                    <input type="text" id="account_code" name="account_code" value="{{ old('account_code') }}" 
                           placeholder="1000" class="form-input" required>
                    <p class="text-xs text-gray-500 mt-1">{{ __('accounting.unique_account_code') }}</p>
                    @error('account_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Name -->
                <div>
                    <label for="account_name" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('accounting.account_name') }} *
                    </label>
                    <input type="text" id="account_name" name="account_name" value="{{ old('account_name') }}" 
                           placeholder="Cash in Bank" class="form-input" required>
                    @error('account_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Type -->
                <div>
                    <label for="account_type" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('accounting.account_type') }} *
                    </label>
                    <select id="account_type" name="account_type" class="form-select" required>
                        <option value="">{{ __('accounting.select_account_type') }}</option>
                        <option value="asset" {{ old('account_type') == 'asset' ? 'selected' : '' }}>{{ __('accounting.asset') }}</option>
                        <option value="liability" {{ old('account_type') == 'liability' ? 'selected' : '' }}>{{ __('accounting.liability') }}</option>
                        <option value="equity" {{ old('account_type') == 'equity' ? 'selected' : '' }}>{{ __('accounting.equity') }}</option>
                        <option value="revenue" {{ old('account_type') == 'revenue' ? 'selected' : '' }}>{{ __('accounting.revenue') }}</option>
                        <option value="expense" {{ old('account_type') == 'expense' ? 'selected' : '' }}>{{ __('accounting.expense') }}</option>
                    </select>
                    @error('account_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Parent Account -->
                <div>
                    <label for="parent_account_id" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('accounting.parent_account') }}
                    </label>
                    <select id="parent_account_id" name="parent_account_id" class="form-select">
                        <option value="">{{ __('accounting.no_parent_account') }}</option>
                        @foreach($parentAccounts as $parentAccount)
                            <option value="{{ $parentAccount->id }}" {{ old('parent_account_id') == $parentAccount->id ? 'selected' : '' }}>
                                {{ $parentAccount->account_code }} - {{ $parentAccount->account_name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">{{ __('accounting.optional_parent_account') }}</p>
                    @error('parent_account_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Normal Balance -->
                <div>
                    <label for="normal_balance" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('accounting.normal_balance') }} *
                    </label>
                    <select id="normal_balance" name="normal_balance" class="form-select" required>
                        <option value="">{{ __('accounting.select_normal_balance') }}</option>
                        <option value="debit" {{ old('normal_balance') == 'debit' ? 'selected' : '' }}>{{ __('accounting.debit') }}</option>
                        <option value="credit" {{ old('normal_balance') == 'credit' ? 'selected' : '' }}>{{ __('accounting.credit') }}</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">{{ __('accounting.normal_balance_help') }}</p>
                    @error('normal_balance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Opening Balance -->
                <div>
                    <label for="opening_balance" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('accounting.opening_balance') }}
                    </label>
                    <input type="number" id="opening_balance" name="opening_balance" value="{{ old('opening_balance', '0.00') }}" 
                           step="0.01" class="form-input">
                    <p class="text-xs text-gray-500 mt-1">{{ __('accounting.initial_account_balance') }}</p>
                    @error('opening_balance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('accounting.description') }}
                </label>
                <textarea id="description" name="description" rows="3" class="form-textarea" 
                          placeholder="{{ __('accounting.account_description_placeholder') }}">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Checkboxes -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" value="1" 
                           {{ old('is_active', true) ? 'checked' : '' }} class="form-checkbox">
                    <label for="is_active" class="ml-2 text-sm text-gray-700">
                        {{ __('accounting.active_account') }}
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="is_system" name="is_system" value="1" 
                           {{ old('is_system') ? 'checked' : '' }} class="form-checkbox">
                    <label for="is_system" class="ml-2 text-sm text-gray-700">
                        {{ __('accounting.system_account') }}
                    </label>
                    <p class="text-xs text-gray-500 ml-2">{{ __('accounting.system_account_note') }}</p>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="allow_manual_entries" name="allow_manual_entries" value="1" 
                           {{ old('allow_manual_entries', true) ? 'checked' : '' }} class="form-checkbox">
                    <label for="allow_manual_entries" class="ml-2 text-sm text-gray-700">
                        {{ __('accounting.allow_manual_entries') }}
                    </label>
                </div>
            </div>

            <!-- Account Type Help -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-medium text-blue-900 mb-2">{{ __('accounting.account_type_guide') }}</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800">
                    <div>
                        <strong>{{ __('accounting.asset') }}:</strong> {{ __('accounting.asset_description') }}
                        <br><strong>{{ __('accounting.liability') }}:</strong> {{ __('accounting.liability_description') }}
                        <br><strong>{{ __('accounting.equity') }}:</strong> {{ __('accounting.equity_description') }}
                    </div>
                    <div>
                        <strong>{{ __('accounting.revenue') }}:</strong> {{ __('accounting.revenue_description') }}
                        <br><strong>{{ __('accounting.expense') }}:</strong> {{ __('accounting.expense_description') }}
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                <a href="{{ route('modules.accounting.chart-of-accounts.index') }}" class="btn-secondary">
                    {{ __('accounting.cancel') }}
                </a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ __('accounting.create_account') }}
                </button>
            </div>
        </form>
    </x-card>
</div>

<script>
// Auto-suggest normal balance based on account type
document.getElementById('account_type').addEventListener('change', function() {
    const accountType = this.value;
    const normalBalanceSelect = document.getElementById('normal_balance');
    
    // Clear current selection
    normalBalanceSelect.value = '';
    
    // Suggest normal balance based on account type
    if (accountType === 'asset' || accountType === 'expense') {
        normalBalanceSelect.value = 'debit';
    } else if (accountType === 'liability' || accountType === 'equity' || accountType === 'revenue') {
        normalBalanceSelect.value = 'credit';
    }
});

// Auto-generate account code suggestion
document.getElementById('account_type').addEventListener('change', function() {
    const accountType = this.value;
    const accountCodeInput = document.getElementById('account_code');
    
    // Only suggest if field is empty
    if (!accountCodeInput.value) {
        const suggestions = {
            'asset': '1000',
            'liability': '2000',
            'equity': '3000',
            'revenue': '4000',
            'expense': '5000'
        };
        
        if (suggestions[accountType]) {
            accountCodeInput.placeholder = suggestions[accountType];
        }
    }
});
</script>
@endsection
