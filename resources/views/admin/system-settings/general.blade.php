@extends('layouts.app')

@section('title', 'General Settings')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.system-settings.index') }}" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">General Settings</h1>
                        <p class="text-gray-600 mt-1">Configure basic system information and preferences</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form action="{{ route('admin.system-settings.general.update') }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Application Information -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Application Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="app_name" class="block text-sm font-medium text-gray-700 mb-2">Application Name</label>
                        <input type="text" id="app_name" name="app_name" value="{{ old('app_name', $settings['app_name']) }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        @error('app_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="language" class="block text-sm font-medium text-gray-700 mb-2">Default Language</label>
                        <select id="language" name="language" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <option value="en" {{ old('language', $settings['language']) == 'en' ? 'selected' : '' }}>English</option>
                            <option value="ar" {{ old('language', $settings['language']) == 'ar' ? 'selected' : '' }}>Arabic</option>
                            <option value="es" {{ old('language', $settings['language']) == 'es' ? 'selected' : '' }}>Spanish</option>
                            <option value="fr" {{ old('language', $settings['language']) == 'fr' ? 'selected' : '' }}>French</option>
                        </select>
                        @error('language')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="app_description" class="block text-sm font-medium text-gray-700 mb-2">Application Description</label>
                        <textarea id="app_description" name="app_description" rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">{{ old('app_description', $settings['app_description']) }}</textarea>
                        @error('app_description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Company Information -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Company Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                        <input type="text" id="company_name" name="company_name" value="{{ old('company_name', $settings['company_name']) }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        @error('company_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="company_email" class="block text-sm font-medium text-gray-700 mb-2">Company Email</label>
                        <input type="email" id="company_email" name="company_email" value="{{ old('company_email', $settings['company_email']) }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        @error('company_email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="company_phone" class="block text-sm font-medium text-gray-700 mb-2">Company Phone</label>
                        <input type="text" id="company_phone" name="company_phone" value="{{ old('company_phone', $settings['company_phone']) }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        @error('company_phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Default Currency</label>
                        <select id="currency" name="currency" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <option value="USD" {{ old('currency', $settings['currency']) == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                            <option value="EUR" {{ old('currency', $settings['currency']) == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="GBP" {{ old('currency', $settings['currency']) == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                            <option value="AED" {{ old('currency', $settings['currency']) == 'AED' ? 'selected' : '' }}>AED - UAE Dirham</option>
                            <option value="SAR" {{ old('currency', $settings['currency']) == 'SAR' ? 'selected' : '' }}>SAR - Saudi Riyal</option>
                        </select>
                        @error('currency')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="company_address" class="block text-sm font-medium text-gray-700 mb-2">Company Address</label>
                        <textarea id="company_address" name="company_address" rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">{{ old('company_address', $settings['company_address']) }}</textarea>
                        @error('company_address')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Regional Settings -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Regional Settings</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                        <select id="timezone" name="timezone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <option value="UTC" {{ old('timezone', $settings['timezone']) == 'UTC' ? 'selected' : '' }}>UTC</option>
                            <option value="America/New_York" {{ old('timezone', $settings['timezone']) == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                            <option value="America/Chicago" {{ old('timezone', $settings['timezone']) == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                            <option value="America/Denver" {{ old('timezone', $settings['timezone']) == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                            <option value="America/Los_Angeles" {{ old('timezone', $settings['timezone']) == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                            <option value="Europe/London" {{ old('timezone', $settings['timezone']) == 'Europe/London' ? 'selected' : '' }}>London</option>
                            <option value="Europe/Paris" {{ old('timezone', $settings['timezone']) == 'Europe/Paris' ? 'selected' : '' }}>Paris</option>
                            <option value="Asia/Dubai" {{ old('timezone', $settings['timezone']) == 'Asia/Dubai' ? 'selected' : '' }}>Dubai</option>
                            <option value="Asia/Riyadh" {{ old('timezone', $settings['timezone']) == 'Asia/Riyadh' ? 'selected' : '' }}>Riyadh</option>
                        </select>
                        @error('timezone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_format" class="block text-sm font-medium text-gray-700 mb-2">Date Format</label>
                        <select id="date_format" name="date_format" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <option value="Y-m-d" {{ old('date_format', $settings['date_format']) == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                            <option value="m/d/Y" {{ old('date_format', $settings['date_format']) == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                            <option value="d/m/Y" {{ old('date_format', $settings['date_format']) == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                            <option value="d-m-Y" {{ old('date_format', $settings['date_format']) == 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY</option>
                        </select>
                        @error('date_format')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="time_format" class="block text-sm font-medium text-gray-700 mb-2">Time Format</label>
                        <select id="time_format" name="time_format" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <option value="H:i:s" {{ old('time_format', $settings['time_format']) == 'H:i:s' ? 'selected' : '' }}>24 Hour (HH:MM:SS)</option>
                            <option value="h:i:s A" {{ old('time_format', $settings['time_format']) == 'h:i:s A' ? 'selected' : '' }}>12 Hour (hh:mm:ss AM/PM)</option>
                            <option value="H:i" {{ old('time_format', $settings['time_format']) == 'H:i' ? 'selected' : '' }}>24 Hour (HH:MM)</option>
                            <option value="h:i A" {{ old('time_format', $settings['time_format']) == 'h:i A' ? 'selected' : '' }}>12 Hour (hh:mm AM/PM)</option>
                        </select>
                        @error('time_format')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.system-settings.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-colors duration-200">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
        {{ session('error') }}
    </div>
@endif
@endsection
