@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="modern-card p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ __('Maintenance Mode') }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ __('Enable or disable maintenance mode for the application') }}</p>
            </div>
            <a href="{{ route('admin.system-settings.index') }}" class="btn-secondary">
                {{ __('Back to Settings') }}
            </a>
        </div>
    </div>

    @php
        $isDown = app()->isDownForMaintenance();
    @endphp

    <!-- Current Status -->
    <div class="modern-card p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold">{{ __('Current Status') }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    @if($isDown)
                        {{ __('The application is currently in maintenance mode') }}
                    @else
                        {{ __('The application is currently running normally') }}
                    @endif
                </p>
            </div>
            <div>
                @if($isDown)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ __('Maintenance Mode Active') }}
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ __('Running Normally') }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Toggle Maintenance Mode -->
    <div class="modern-card p-6">
        <h2 class="text-lg font-semibold mb-4">{{ __('Maintenance Mode Control') }}</h2>
        
        <form method="POST" action="{{ route('admin.system-settings.maintenance.toggle') }}">
            @csrf
            
            <div class="space-y-6">
                @if(!$isDown)
                    <!-- Enable Maintenance Mode -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Maintenance Message') }}</label>
                        <textarea name="message" rows="3" class="form-input w-full" placeholder="{{ __('We are currently performing scheduled maintenance. We will be back shortly.') }}">{{ old('message') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">{{ __('This message will be displayed to visitors during maintenance') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Retry After (seconds)') }}</label>
                        <input type="number" name="retry" value="{{ old('retry', 60) }}" class="form-input w-full" min="0">
                        <p class="text-xs text-gray-500 mt-1">{{ __('Suggested time for visitors to retry (0 for no suggestion)') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Secret Access Token (optional)') }}</label>
                        <input type="text" name="secret" value="{{ old('secret') }}" class="form-input w-full" placeholder="my-secret-token">
                        <p class="text-xs text-gray-500 mt-1">{{ __('Allow access during maintenance by visiting: /?secret=your-token') }}</p>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">{{ __('Warning') }}</h3>
                                <p class="mt-1 text-sm text-yellow-700">
                                    {{ __('Enabling maintenance mode will make the application unavailable to all users except administrators.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="btn-warning" onclick="return confirm('{{ __('Are you sure you want to enable maintenance mode?') }}')">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            {{ __('Enable Maintenance Mode') }}
                        </button>
                    </div>
                @else
                    <!-- Disable Maintenance Mode -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800">{{ __('Ready to Resume') }}</h3>
                                <p class="mt-1 text-sm text-green-700">
                                    {{ __('Click the button below to disable maintenance mode and make the application available to all users.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="btn-success">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ __('Disable Maintenance Mode') }}
                        </button>
                    </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Information -->
    <div class="modern-card p-6 mt-6">
        <h2 class="text-lg font-semibold mb-4">{{ __('About Maintenance Mode') }}</h2>
        <div class="prose prose-sm max-w-none text-gray-600">
            <ul class="list-disc list-inside space-y-2">
                <li>{{ __('Maintenance mode displays a custom page to visitors while you perform updates or maintenance') }}</li>
                <li>{{ __('Administrators can still access the application during maintenance mode') }}</li>
                <li>{{ __('You can provide a secret token to allow specific users to bypass maintenance mode') }}</li>
                <li>{{ __('The retry-after header suggests when visitors should check back') }}</li>
                <li>{{ __('You can also enable/disable maintenance mode via command line: php artisan down / php artisan up') }}</li>
            </ul>
        </div>
    </div>
</div>
@endsection

