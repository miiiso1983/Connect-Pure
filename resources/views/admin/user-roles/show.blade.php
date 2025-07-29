@extends('layouts.app')

@section('title', __('admin.user_role_management'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('admin.user_role_management') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('admin.manage_user_roles_for', ['user' => $user->name]) }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('admin.user-roles.index') }}" class="btn-secondary">
                {{ __('admin.back_to_users') }}
            </a>
        </div>
    </div>

    <!-- User Information -->
    <x-card title="{{ __('admin.user_information') }}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0 h-16 w-16">
                    <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center">
                        <span class="text-xl font-medium text-gray-700">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </span>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    <p class="text-xs text-gray-400">{{ __('admin.user_id') }}: {{ $user->id }}</p>
                </div>
            </div>
            
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">{{ __('admin.account_status') }}:</span>
                    <span class="text-sm font-medium {{ $user->email_verified_at ? 'text-green-600' : 'text-red-600' }}">
                        {{ $user->email_verified_at ? __('admin.verified') : __('admin.unverified') }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">{{ __('admin.joined_date') }}:</span>
                    <span class="text-sm text-gray-900">{{ $user->created_at ? $user->created_at->format('M j, Y') : 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">{{ __('admin.current_roles') }}:</span>
                    <span class="text-sm font-medium text-blue-600">{{ $user->roles->count() }}</span>
                </div>
            </div>
        </div>
    </x-card>

    <!-- Current Roles -->
    <x-card title="{{ __('admin.current_roles') }}">
        @if($user->roles->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($user->roles as $role)
                    <div class="flex items-center justify-between p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">{{ $role->name }}</h4>
                                <p class="text-xs text-gray-500">{{ $role->slug }}</p>
                                @if($role->description)
                                    <p class="text-xs text-gray-400 mt-1">{{ Str::limit($role->description, 40) }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <!-- Role Level -->
                            @if($role->level > 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    L{{ $role->level }}
                                </span>
                            @endif
                            
                            <!-- Status -->
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $role->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $role->is_active ? __('admin.active') : __('admin.inactive') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('admin.no_roles_assigned') }}</h3>
                <p class="text-gray-600">{{ __('admin.user_has_no_roles') }}</p>
            </div>
        @endif
    </x-card>

    <!-- Role Assignment Form -->
    <x-card title="{{ __('admin.assign_roles') }}">
        <form action="{{ route('admin.user-roles.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <p class="text-gray-600">{{ __('admin.select_roles_to_assign') }}</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($availableRoles as $role)
                        <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                   {{ $user->roles->contains($role->id) ? 'checked' : '' }}
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
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }} pt-6 border-t border-gray-200 mt-6">
                <a href="{{ route('admin.user-roles.index') }}" class="btn-secondary">
                    {{ __('admin.cancel') }}
                </a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ __('admin.update_roles') }}
                </button>
            </div>
        </form>
    </x-card>

    <!-- Role Permissions Preview -->
    @if($user->roles->count() > 0)
        <x-card title="{{ __('admin.effective_permissions') }}">
            <div class="space-y-4">
                <p class="text-gray-600">{{ __('admin.permissions_from_assigned_roles') }}</p>
                
                @php
                    $allPermissions = [];
                    foreach($user->roles as $role) {
                        if ($role->permissions) {
                            $allPermissions = array_merge($allPermissions, $role->permissions);
                        }
                    }
                    $uniquePermissions = array_unique($allPermissions);
                    sort($uniquePermissions);
                @endphp
                
                @if(count($uniquePermissions) > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                        @foreach($uniquePermissions as $permission)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $permission }}
                            </span>
                        @endforeach
                    </div>
                    
                    <div class="text-sm text-gray-500 mt-4">
                        {{ __('admin.total_unique_permissions') }}: {{ count($uniquePermissions) }}
                    </div>
                @else
                    <p class="text-gray-500">{{ __('admin.no_permissions_assigned') }}</p>
                @endif
            </div>
        </x-card>
    @endif
</div>
@endsection
