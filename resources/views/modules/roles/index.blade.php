@extends('layouts.app')

@section('title', __('erp.roles'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('erp.roles') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('erp.roles_description') }}</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <x-stat-card
            title="{{ __('roles.total_roles') }}"
            :value="$stats['total_roles']"
            color="blue"
            :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('roles.active_roles') }}"
            :value="$stats['active_roles']"
            color="green"
            :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('roles.total_permissions') }}"
            :value="$stats['total_permissions']"
            color="purple"
            :icon="'<svg class=\'w-6 h-6 text-purple-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('roles.active_users') }}"
            :value="$stats['active_users']"
            color="orange"
            :icon="'<svg class=\'w-6 h-6 text-orange-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 715.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z\'></path></svg>'"
        />
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-card title="{{ __('roles.role_management') }}">
            <div class="space-y-4">
                <p class="text-gray-600">{{ __('roles.manage_system_roles') }}</p>
                <div class="flex space-x-3">
                    <a href="{{ route('modules.roles.roles.index') }}" class="btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        {{ __('roles.view_roles') }}
                    </a>
                    <a href="{{ route('modules.roles.roles.create') }}" class="btn-outline">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        {{ __('roles.create_role') }}
                    </a>
                </div>
            </div>
        </x-card>

        <x-card title="{{ __('roles.user_management') }}">
            <div class="space-y-4">
                <p class="text-gray-600">{{ __('roles.manage_user_roles') }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">{{ __('roles.users_without_roles') }}</span>
                    <span class="font-semibold text-orange-600">{{ $stats['users_without_roles'] }}</span>
                </div>
                <a href="{{ route('modules.roles.users.index') }}" class="btn-primary w-full">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    {{ __('roles.manage_users') }}
                </a>
            </div>
        </x-card>

        <x-card title="{{ __('roles.permissions') }}">
            <div class="space-y-4">
                <p class="text-gray-600">{{ __('roles.manage_permissions') }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">{{ __('roles.permission_groups') }}</span>
                    <span class="font-semibold text-blue-600">{{ count($permissionGroups) }}</span>
                </div>
                <a href="{{ route('modules.roles.permissions.index') }}" class="btn-primary w-full">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    {{ __('roles.view_permissions') }}
                </a>
            </div>
        </x-card>

        <x-card title="{{ __('roles.hierarchy') }}">
            <div class="space-y-4">
                <p class="text-gray-600">{{ __('roles.manage_role_hierarchy') }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">{{ __('roles.max_depth') }}</span>
                    <span class="font-semibold text-purple-600">{{ $stats['max_depth'] ?? 0 }}</span>
                </div>
                <a href="{{ route('modules.roles.hierarchy.index') }}" class="btn-primary w-full">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                    {{ __('roles.view_hierarchy') }}
                </a>
            </div>
        </x-card>
    </div>

    <!-- Recent Activities and Roles Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Role Assignments -->
        <x-card title="{{ __('roles.recent_assignments') }}">
            @if($recentAssignments->count() > 0)
                <div class="space-y-3">
                    @foreach($recentAssignments as $assignment)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <div>
                                <div class="font-medium text-gray-900">{{ $assignment->user_name }}</div>
                                <div class="text-sm text-gray-500">{{ $assignment->email }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-blue-600">{{ $assignment->role_name }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($assignment->assigned_at)->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">{{ __('roles.no_recent_assignments') }}</p>
            @endif
        </x-card>

        <!-- Roles Overview -->
        <x-card title="{{ __('roles.roles_overview') }}">
            @if($roles->count() > 0)
                <div class="space-y-3">
                    @foreach($roles->take(5) as $role)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <div class="font-medium text-gray-900">{{ $role->name }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($role->description, 30) }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900">{{ $role->users_count }} {{ __('roles.users') }}</div>
                                <div class="text-xs text-gray-500">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $role->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $role->is_active ? __('roles.active') : __('roles.inactive') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($roles->count() > 5)
                    <div class="mt-4 text-center">
                        <a href="{{ route('modules.roles.roles.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            {{ __('roles.view_all_roles') }}
                        </a>
                    </div>
                @endif
            @else
                <p class="text-gray-500 text-center py-4">{{ __('roles.no_roles_found') }}</p>
            @endif
        </x-card>
    </div>
</div>
@endsection
