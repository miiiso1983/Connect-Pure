@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="modern-card p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ __('Email Settings') }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ __('Configure email server and notification settings') }}</p>
            </div>
            <a href="{{ route('admin.system-settings.index') }}" class="btn-secondary">
                {{ __('Back to Settings') }}
            </a>
        </div>
    </div>

    <div class="modern-card p-6">
        <form method="POST" action="{{ route('admin.system-settings.email.update') }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Mail Driver -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Mail Driver') }}</label>
                    <select name="mail_driver" class="form-input w-full">
                        <option value="smtp" {{ old('mail_driver', config('mail.default')) === 'smtp' ? 'selected' : '' }}>SMTP</option>
                        <option value="sendmail" {{ old('mail_driver', config('mail.default')) === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                        <option value="mailgun" {{ old('mail_driver', config('mail.default')) === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                        <option value="ses" {{ old('mail_driver', config('mail.default')) === 'ses' ? 'selected' : '' }}>Amazon SES</option>
                    </select>
                </div>

                <!-- SMTP Host -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('SMTP Host') }}</label>
                    <input type="text" name="mail_host" value="{{ old('mail_host', config('mail.mailers.smtp.host')) }}" class="form-input w-full">
                </div>

                <!-- SMTP Port -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('SMTP Port') }}</label>
                    <input type="number" name="mail_port" value="{{ old('mail_port', config('mail.mailers.smtp.port')) }}" class="form-input w-full">
                </div>

                <!-- SMTP Username -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('SMTP Username') }}</label>
                    <input type="text" name="mail_username" value="{{ old('mail_username', config('mail.mailers.smtp.username')) }}" class="form-input w-full">
                </div>

                <!-- SMTP Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('SMTP Password') }}</label>
                    <input type="password" name="mail_password" placeholder="••••••••" class="form-input w-full">
                    <p class="text-xs text-gray-500 mt-1">{{ __('Leave blank to keep current password') }}</p>
                </div>

                <!-- SMTP Encryption -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Encryption') }}</label>
                    <select name="mail_encryption" class="form-input w-full">
                        <option value="">{{ __('None') }}</option>
                        <option value="tls" {{ old('mail_encryption', config('mail.mailers.smtp.encryption')) === 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ old('mail_encryption', config('mail.mailers.smtp.encryption')) === 'ssl' ? 'selected' : '' }}>SSL</option>
                    </select>
                </div>

                <!-- From Address -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('From Email Address') }}</label>
                    <input type="email" name="mail_from_address" value="{{ old('mail_from_address', config('mail.from.address')) }}" class="form-input w-full">
                </div>

                <!-- From Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('From Name') }}</label>
                    <input type="text" name="mail_from_name" value="{{ old('mail_from_name', config('mail.from.name')) }}" class="form-input w-full">
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('admin.system-settings.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn-primary">{{ __('Save Email Settings') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

