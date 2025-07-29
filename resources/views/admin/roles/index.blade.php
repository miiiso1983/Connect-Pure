@extends('layouts.app')

@section('title', __('roles.role_management'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('roles.role_management') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('roles.manage_system_roles_and_permissions') }}</p>
        </div>
        <div class="mt-4 lg:mt-0 flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('admin.roles.permission-matrix') }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                {{ __('roles.permission_matrix') }}
            </a>
            <a href="{{ route('admin.roles.create') }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ __('roles.create_role') }}
            </a>
        </div>
    </div>

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($roles as $role)
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">
                            {{ $role->localized_name }}
                        </h3>
                        <p class="text-sm text-gray-600 mb-3">
                            {{ $role->localized_description }}
                        </p>
                        
                        <!-- Role Stats -->
                        <div class="flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }} text-sm text-gray-500">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                {{ $role->users_count }} {{ __('roles.users') }}
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                {{ count($role->permissions ?? []) }} {{ __('roles.permissions') }}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status Badge -->
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $role->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $role->is_active ? __('roles.active') : __('roles.inactive') }}
                    </span>
                </div>

                <!-- Permissions Preview -->
                @if($role->permissions && count($role->permissions) > 0)
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('roles.key_permissions') }}:</h4>
                        <div class="flex flex-wrap gap-1">
                            @foreach(array_slice($role->permissions, 0, 3) as $permission)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ __('roles.' . $permission) }}
                                </span>
                            @endforeach
                            @if(count($role->permissions) > 3)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                    +{{ count($role->permissions) - 3 }} {{ __('roles.more') }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <div class="flex space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <a href="{{ route('admin.roles.show', $role) }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            {{ __('roles.view_details') }}
                        </a>
                        <a href="{{ route('admin.roles.edit', $role) }}" 
                           class="text-green-600 hover:text-green-800 text-sm font-medium">
                            {{ __('roles.edit') }}
                        </a>
                    </div>
                    
                    <div class="flex space-x-1 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <form action="{{ route('admin.roles.clone', $role) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="text-gray-400 hover:text-gray-600 p-1 rounded"
                                    title="{{ __('roles.clone_role') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </form>
                        
                        @if($role->users_count == 0)
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-400 hover:text-red-600 p-1 rounded"
                                        title="{{ __('roles.delete_role') }}"
                                        onclick="return confirm('{{ __('roles.confirm_delete_role') }}')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($roles->isEmpty())
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('roles.no_roles_found') }}</h3>
            <p class="text-gray-500 mb-4">{{ __('roles.create_first_role_to_get_started') }}</p>
            <a href="{{ route('admin.roles.create') }}" class="btn-primary">
                {{ __('roles.create_role') }}
            </a>
        </div>
    @endif

    <!-- Quick Stats -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('roles.system_overview') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $roles->count() }}</div>
                <div class="text-sm text-gray-600">{{ __('roles.total_roles') }}</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $roles->where('is_active', true)->count() }}</div>
                <div class="text-sm text-gray-600">{{ __('roles.active_roles') }}</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ $roles->sum('users_count') }}</div>
                <div class="text-sm text-gray-600">{{ __('roles.total_assignments') }}</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-orange-600">{{ count(\App\Models\Role::getAllPermissions()) }}</div>
                <div class="text-sm text-gray-600">{{ __('roles.available_permissions') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
