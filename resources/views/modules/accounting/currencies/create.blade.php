@extends('layouts.app')

@section('title', __('accounting.add_currency'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('accounting.add_currency') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('accounting.create_new_currency') }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.accounting.currencies.index') }}" class="btn-secondary">
                {{ __('accounting.back_to_currencies') }}
            </a>
        </div>
    </div>

    <!-- Form -->
    <x-card title="{{ __('accounting.currency_information') }}">
        <form action="{{ route('modules.accounting.currencies.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Currency Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('accounting.currency_code') }} *
                    </label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}" 
                           placeholder="USD" maxlength="3" class="form-input uppercase" required>
                    <p class="text-xs text-gray-500 mt-1">{{ __('accounting.three_letter_iso_code') }}</p>
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Currency Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('accounting.currency_name') }} *
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" 
                           placeholder="US Dollar" class="form-input" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Currency Symbol -->
                <div>
                    <label for="symbol" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('accounting.currency_symbol') }} *
                    </label>
                    <input type="text" id="symbol" name="symbol" value="{{ old('symbol') }}" 
                           placeholder="$" maxlength="10" class="form-input" required>
                    @error('symbol')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Exchange Rate -->
                <div>
                    <label for="exchange_rate" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('accounting.exchange_rate') }} *
                    </label>
                    <input type="number" id="exchange_rate" name="exchange_rate" value="{{ old('exchange_rate', '1.000000') }}" 
                           step="0.000001" min="0.000001" max="999999.999999" class="form-input" required>
                    <p class="text-xs text-gray-500 mt-1">{{ __('accounting.rate_against_base_currency') }}</p>
                    @error('exchange_rate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Decimal Places -->
                <div>
                    <label for="decimal_places" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('accounting.decimal_places') }} *
                    </label>
                    <select id="decimal_places" name="decimal_places" class="form-select" required>
                        <option value="0" {{ old('decimal_places') == '0' ? 'selected' : '' }}>0</option>
                        <option value="1" {{ old('decimal_places') == '1' ? 'selected' : '' }}>1</option>
                        <option value="2" {{ old('decimal_places', '2') == '2' ? 'selected' : '' }}>2</option>
                        <option value="3" {{ old('decimal_places') == '3' ? 'selected' : '' }}>3</option>
                        <option value="4" {{ old('decimal_places') == '4' ? 'selected' : '' }}>4</option>
                        <option value="5" {{ old('decimal_places') == '5' ? 'selected' : '' }}>5</option>
                        <option value="6" {{ old('decimal_places') == '6' ? 'selected' : '' }}>6</option>
                    </select>
                    @error('decimal_places')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Symbol Position -->
                <div>
                    <label for="symbol_position" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('accounting.symbol_position') }} *
                    </label>
                    <select id="symbol_position" name="symbol_position" class="form-select" required>
                        <option value="before" {{ old('symbol_position', 'before') == 'before' ? 'selected' : '' }}>
                            {{ __('accounting.before_amount') }} ($1,234.56)
                        </option>
                        <option value="after" {{ old('symbol_position') == 'after' ? 'selected' : '' }}>
                            {{ __('accounting.after_amount') }} (1,234.56$)
                        </option>
                    </select>
                    @error('symbol_position')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Thousands Separator -->
                <div>
                    <label for="thousands_separator" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('accounting.thousands_separator') }} *
                    </label>
                    <select id="thousands_separator" name="thousands_separator" class="form-select" required>
                        <option value="," {{ old('thousands_separator', ',') == ',' ? 'selected' : '' }}>
                            {{ __('accounting.comma') }} (1,234.56)
                        </option>
                        <option value="." {{ old('thousands_separator') == '.' ? 'selected' : '' }}>
                            {{ __('accounting.period') }} (1.234,56)
                        </option>
                        <option value=" " {{ old('thousands_separator') == ' ' ? 'selected' : '' }}>
                            {{ __('accounting.space') }} (1 234.56)
                        </option>
                        <option value="" {{ old('thousands_separator') == '' ? 'selected' : '' }}>
                            {{ __('accounting.none') }} (1234.56)
                        </option>
                    </select>
                    @error('thousands_separator')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Decimal Separator -->
                <div>
                    <label for="decimal_separator" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('accounting.decimal_separator') }} *
                    </label>
                    <select id="decimal_separator" name="decimal_separator" class="form-select" required>
                        <option value="." {{ old('decimal_separator', '.') == '.' ? 'selected' : '' }}>
                            {{ __('accounting.period') }} (1,234.56)
                        </option>
                        <option value="," {{ old('decimal_separator') == ',' ? 'selected' : '' }}>
                            {{ __('accounting.comma') }} (1.234,56)
                        </option>
                    </select>
                    @error('decimal_separator')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Checkboxes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" value="1" 
                           {{ old('is_active', true) ? 'checked' : '' }} class="form-checkbox">
                    <label for="is_active" class="ml-2 text-sm text-gray-700">
                        {{ __('accounting.active_currency') }}
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="is_base_currency" name="is_base_currency" value="1" 
                           {{ old('is_base_currency') ? 'checked' : '' }} class="form-checkbox">
                    <label for="is_base_currency" class="ml-2 text-sm text-gray-700">
                        {{ __('accounting.set_as_base_currency') }}
                    </label>
                    <p class="text-xs text-gray-500 ml-2">{{ __('accounting.base_currency_note') }}</p>
                </div>
            </div>

            <!-- Preview -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-2">{{ __('accounting.format_preview') }}</h4>
                <div id="format-preview" class="text-lg font-mono text-gray-700">
                    $1,234.56
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                <a href="{{ route('modules.accounting.currencies.index') }}" class="btn-secondary">
                    {{ __('accounting.cancel') }}
                </a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ __('accounting.create_currency') }}
                </button>
            </div>
        </form>
    </x-card>
</div>

<script>
// Update format preview
function updatePreview() {
    const symbol = document.getElementById('symbol').value || '$';
    const symbolPosition = document.getElementById('symbol_position').value;
    const thousandsSeparator = document.getElementById('thousands_separator').value;
    const decimalSeparator = document.getElementById('decimal_separator').value;
    const decimalPlaces = parseInt(document.getElementById('decimal_places').value) || 2;
    
    let amount = '1234';
    if (thousandsSeparator) {
        amount = '1' + thousandsSeparator + '234';
    }
    
    if (decimalPlaces > 0) {
        amount += decimalSeparator + '56'.padEnd(decimalPlaces, '0').substring(0, decimalPlaces);
    }
    
    const preview = symbolPosition === 'before' ? symbol + amount : amount + symbol;
    document.getElementById('format-preview').textContent = preview;
}

// Add event listeners
document.addEventListener('DOMContentLoaded', function() {
    const inputs = ['symbol', 'symbol_position', 'thousands_separator', 'decimal_separator', 'decimal_places'];
    inputs.forEach(id => {
        document.getElementById(id).addEventListener('change', updatePreview);
        document.getElementById(id).addEventListener('input', updatePreview);
    });
    
    // Initial preview
    updatePreview();
    
    // Auto-uppercase currency code
    document.getElementById('code').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
});
</script>
@endsection
