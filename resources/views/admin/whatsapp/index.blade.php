@extends('layouts.app')

@section('title', __('admin.whatsapp_configuration'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('admin.whatsapp_configuration') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('admin.configure_whatsapp_business_api') }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="btn-secondary">
                {{ __('admin.back_to_dashboard') }}
            </a>
        </div>
    </div>

    <!-- Configuration Status -->
    <x-card title="{{ __('admin.configuration_status') }}">
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $isConfigured ? 'bg-green-100' : 'bg-red-100' }}">
                    @if($isConfigured)
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @else
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @endif
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium {{ $isConfigured ? 'text-green-900' : 'text-red-900' }}">
                    {{ $isConfigured ? __('admin.whatsapp_configured') : __('admin.whatsapp_not_configured') }}
                </h3>
                <p class="text-sm {{ $isConfigured ? 'text-green-600' : 'text-red-600' }}">
                    {{ $isConfigured ? __('admin.whatsapp_ready_to_send') : __('admin.whatsapp_needs_configuration') }}
                </p>
            </div>
        </div>
    </x-card>

    <!-- Configuration Form -->
    <x-card title="{{ __('admin.whatsapp_api_settings') }}">
        <form action="{{ route('admin.whatsapp.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Access Token -->
                <div>
                    <label for="access_token" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.access_token') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="password" 
                           id="access_token" 
                           name="access_token" 
                           class="form-input @error('access_token') border-red-300 @enderror"
                           placeholder="{{ __('admin.enter_access_token') }}"
                           value="{{ old('access_token') }}">
                    <p class="mt-1 text-xs text-gray-500">{{ __('admin.access_token_help') }}</p>
                    @error('access_token')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Number ID -->
                <div>
                    <label for="phone_number_id" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.phone_number_id') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="phone_number_id" 
                           name="phone_number_id" 
                           class="form-input @error('phone_number_id') border-red-300 @enderror"
                           placeholder="{{ __('admin.enter_phone_number_id') }}"
                           value="{{ old('phone_number_id', $config['phone_number_id']) }}">
                    <p class="mt-1 text-xs text-gray-500">{{ __('admin.phone_number_id_help') }}</p>
                    @error('phone_number_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Business Account ID -->
                <div>
                    <label for="business_account_id" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.business_account_id') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="business_account_id" 
                           name="business_account_id" 
                           class="form-input @error('business_account_id') border-red-300 @enderror"
                           placeholder="{{ __('admin.enter_business_account_id') }}"
                           value="{{ old('business_account_id', $config['business_account_id']) }}">
                    <p class="mt-1 text-xs text-gray-500">{{ __('admin.business_account_id_help') }}</p>
                    @error('business_account_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Webhook Verify Token -->
                <div>
                    <label for="webhook_verify_token" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.webhook_verify_token') }}
                    </label>
                    <input type="text" 
                           id="webhook_verify_token" 
                           name="webhook_verify_token" 
                           class="form-input @error('webhook_verify_token') border-red-300 @enderror"
                           placeholder="{{ __('admin.enter_webhook_verify_token') }}"
                           value="{{ old('webhook_verify_token') }}">
                    <p class="mt-1 text-xs text-gray-500">{{ __('admin.webhook_verify_token_help') }}</p>
                    @error('webhook_verify_token')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }} pt-6 border-t border-gray-200 mt-6">
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ __('admin.save_configuration') }}
                </button>
            </div>
        </form>
    </x-card>

    <!-- Test WhatsApp -->
    @if($isConfigured)
    <x-card title="{{ __('admin.test_whatsapp') }}">
        <form action="{{ route('admin.whatsapp.test') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="test_number" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.test_phone_number') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="test_number" 
                           name="test_number" 
                           class="form-input @error('test_number') border-red-300 @enderror"
                           placeholder="{{ __('admin.enter_test_phone_number') }}"
                           value="{{ old('test_number') }}">
                    <p class="mt-1 text-xs text-gray-500">{{ __('admin.test_phone_number_help') }}</p>
                    @error('test_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="test_message" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.test_message') }}
                    </label>
                    <textarea id="test_message" 
                              name="test_message" 
                              rows="3"
                              class="form-input @error('test_message') border-red-300 @enderror"
                              placeholder="{{ __('admin.enter_test_message') }}">{{ old('test_message', 'This is a test message from Connect Pure ERP WhatsApp integration.') }}</textarea>
                    @error('test_message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end pt-4">
                <button type="submit" class="btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    {{ __('admin.send_test_message') }}
                </button>
            </div>
        </form>
    </x-card>
    @endif

    <!-- Current Configuration -->
    <x-card title="{{ __('admin.current_configuration') }}">
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.api_url') }}</label>
                    <p class="text-sm text-gray-900 font-mono">{{ $config['api_url'] }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.access_token') }}</label>
                    <p class="text-sm text-gray-900 font-mono">{{ $config['access_token'] ?? __('admin.not_configured') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.phone_number_id') }}</label>
                    <p class="text-sm text-gray-900 font-mono">{{ $config['phone_number_id'] ?? __('admin.not_configured') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.business_account_id') }}</label>
                    <p class="text-sm text-gray-900 font-mono">{{ $config['business_account_id'] ?? __('admin.not_configured') }}</p>
                </div>
            </div>
        </div>
    </x-card>

    <!-- Setup Instructions -->
    <x-card title="{{ __('admin.setup_instructions') }}">
        <div class="prose max-w-none">
            <h4>{{ __('admin.whatsapp_business_api_setup') }}</h4>
            <ol>
                <li>{{ __('admin.setup_step_1') }}</li>
                <li>{{ __('admin.setup_step_2') }}</li>
                <li>{{ __('admin.setup_step_3') }}</li>
                <li>{{ __('admin.setup_step_4') }}</li>
                <li>{{ __('admin.setup_step_5') }}</li>
            </ol>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-4">
                <h5 class="text-blue-800 font-medium">{{ __('admin.important_note') }}</h5>
                <p class="text-blue-700 text-sm mt-1">{{ __('admin.whatsapp_business_note') }}</p>
            </div>
        </div>
    </x-card>
</div>
@endsection
