@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="modern-card p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ __('Security Settings') }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ __('Configure security and authentication settings') }}</p>
            </div>
            <a href="{{ route('admin.system-settings.index') }}" class="btn-secondary">
                {{ __('Back to Settings') }}
            </a>
        </div>
    </div>

    <div class="modern-card p-6">
        <form method="POST" action="{{ route('admin.system-settings.security.update') }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Session Lifetime -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Session Lifetime (minutes)') }}</label>
                    <input type="number" name="session_lifetime" value="{{ old('session_lifetime', config('session.lifetime', 120)) }}" class="form-input w-full" min="5" max="43200">
                    <p class="text-xs text-gray-500 mt-1">{{ __('How long user sessions should last before expiring') }}</p>
                </div>

                <!-- Password Min Length -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Minimum Password Length') }}</label>
                    <input type="number" name="password_min_length" value="{{ old('password_min_length', 8) }}" class="form-input w-full" min="6" max="32">
                </div>

                <!-- Require Strong Passwords -->
                <div class="flex items-center">
                    <input type="checkbox" name="require_strong_passwords" id="require_strong_passwords" value="1" {{ old('require_strong_passwords', true) ? 'checked' : '' }} class="form-checkbox">
                    <label for="require_strong_passwords" class="ml-2 text-sm text-gray-700">{{ __('Require strong passwords (uppercase, lowercase, numbers, symbols)') }}</label>
                </div>

                <!-- Two-Factor Authentication -->
                <div class="flex items-center">
                    <input type="checkbox" name="enable_2fa" id="enable_2fa" value="1" {{ old('enable_2fa', false) ? 'checked' : '' }} class="form-checkbox">
                    <label for="enable_2fa" class="ml-2 text-sm text-gray-700">{{ __('Enable Two-Factor Authentication (2FA)') }}</label>
                </div>

                <!-- Max Login Attempts -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Max Login Attempts') }}</label>
                    <input type="number" name="max_login_attempts" value="{{ old('max_login_attempts', 5) }}" class="form-input w-full" min="3" max="20">
                    <p class="text-xs text-gray-500 mt-1">{{ __('Number of failed login attempts before account lockout') }}</p>
                </div>

                <!-- Lockout Duration -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Lockout Duration (minutes)') }}</label>
                    <input type="number" name="lockout_duration" value="{{ old('lockout_duration', 15) }}" class="form-input w-full" min="1" max="1440">
                </div>

                <!-- Force HTTPS -->
                <div class="flex items-center">
                    <input type="checkbox" name="force_https" id="force_https" value="1" {{ old('force_https', false) ? 'checked' : '' }} class="form-checkbox">
                    <label for="force_https" class="ml-2 text-sm text-gray-700">{{ __('Force HTTPS for all connections') }}</label>
                </div>

                <!-- IP Whitelist -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Admin IP Whitelist (optional)') }}</label>
                    <textarea name="ip_whitelist" rows="3" class="form-input w-full" placeholder="192.168.1.1&#10;10.0.0.1">{{ old('ip_whitelist') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">{{ __('One IP address per line. Leave empty to allow all IPs.') }}</p>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('admin.system-settings.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn-primary">{{ __('Save Security Settings') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

