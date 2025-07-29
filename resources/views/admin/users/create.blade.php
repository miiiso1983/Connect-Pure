@extends('layouts.app')

@section('title', __('admin.create_user'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('admin.create_user') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('admin.create_new_user_account') }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                {{ __('admin.back_to_users') }}
            </a>
        </div>
    </div>

    <!-- User Creation Form -->
    <x-card title="{{ __('admin.user_information') }}">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('admin.full_name') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-input @error('name') border-red-300 @enderror"
                               placeholder="{{ __('admin.enter_full_name') }}"
                               value="{{ old('name') }}"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('admin.email_address') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-input @error('email') border-red-300 @enderror"
                               placeholder="{{ __('admin.enter_email_address') }}"
                               value="{{ old('email') }}"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Password -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('admin.password') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-input @error('password') border-red-300 @enderror"
                               placeholder="{{ __('admin.enter_password') }}"
                               required>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('admin.confirm_password') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               class="form-input @error('password_confirmation') border-red-300 @enderror"
                               placeholder="{{ __('admin.confirm_password') }}"
                               required>
                        @error('password_confirmation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Role Assignment -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">
                        {{ __('admin.assign_roles') }}
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($roles as $role)
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" 
                                       name="roles[]" 
                                       value="{{ $role->id }}" 
                                       {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}
                                       class="form-checkbox h-4 w-4 text-blue-600">
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">{{ $role->name }}</h4>
                                            <p class="text-xs text-gray-500">{{ $role->slug }}</p>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            @if($role->level > 0)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                    L{{ $role->level }}
                                                </span>
                                            @endif
                                            @if($role->inherit_permissions)
                                                <svg class="w-3 h-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="{{ __('admin.inherits_permissions') }}">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                    @if($role->description)
                                        <p class="text-xs text-gray-400 mt-1">{{ Str::limit($role->description, 60) }}</p>
                                    @endif
                                    
                                    <!-- Permission Count -->
                                    <div class="flex items-center mt-2 text-xs text-gray-500">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                        {{ count($role->permissions ?? []) }} {{ __('admin.permissions') }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('roles')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }} pt-6 border-t border-gray-200 mt-6">
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                    {{ __('admin.cancel') }}
                </a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ __('admin.create_user') }}
                </button>
            </div>
        </form>
    </x-card>

    <!-- Password Requirements -->
    <x-card title="{{ __('admin.password_requirements') }}">
        <div class="text-sm text-gray-600">
            <p class="mb-2">{{ __('admin.password_must_contain') }}:</p>
            <ul class="list-disc list-inside space-y-1">
                <li>{{ __('admin.password_min_length') }}</li>
                <li>{{ __('admin.password_mixed_case') }}</li>
                <li>{{ __('admin.password_numbers') }}</li>
                <li>{{ __('admin.password_special_chars') }}</li>
            </ul>
        </div>
    </x-card>
</div>
@endsection
