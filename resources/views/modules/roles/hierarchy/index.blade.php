@extends('layouts.app')

@section('title', __('roles.hierarchy'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('roles.hierarchy') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('roles.hierarchy_tree') }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.roles.index') }}" class="btn-secondary">
                {{ __('roles.back') }}
            </a>
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
            title="{{ __('roles.root_roles') }}"
            :value="$stats['root_roles']"
            color="green"
            :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('roles.max_depth') }}"
            :value="$stats['max_depth']"
            color="purple"
            :icon="'<svg class=\'w-6 h-6 text-purple-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('roles.roles_with_inheritance') }}"
            :value="$stats['roles_with_inheritance']"
            color="orange"
            :icon="'<svg class=\'w-6 h-6 text-orange-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4\'></path></svg>'"
        />
    </div>

    <!-- Hierarchy Tree -->
    <x-card title="{{ __('roles.hierarchy_tree') }}">
        @if(count($hierarchyTree) > 0)
            <div class="space-y-4">
                @foreach($hierarchyTree as $rootRole)
                    @include('modules.roles.hierarchy.tree-node', ['role' => $rootRole, 'level' => 0])
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('roles.no_hierarchy_found') }}</h3>
                <p class="text-gray-600 mb-6">{{ __('roles.create_roles_to_build_hierarchy') }}</p>
                <a href="{{ route('modules.roles.roles.create') }}" class="btn-primary">
                    {{ __('roles.create_first_role') }}
                </a>
            </div>
        @endif
    </x-card>

    <!-- Legend -->
    <x-card title="{{ __('roles.hierarchy_legend') }}">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-blue-500 rounded mr-2"></div>
                <span class="text-sm text-gray-700">{{ __('roles.root_role') }}</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                <span class="text-sm text-gray-700">{{ __('roles.child_role') }}</span>
            </div>
            <div class="flex items-center">
                <svg class="w-4 h-4 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4"></path>
                </svg>
                <span class="text-sm text-gray-700">{{ __('roles.inherits_permissions') }}</span>
            </div>
            <div class="flex items-center">
                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18 21l-2.636-2.636M5.636 5.636L3 3l2.636 2.636"></path>
                </svg>
                <span class="text-sm text-gray-700">{{ __('roles.no_inheritance') }}</span>
            </div>
        </div>
    </x-card>
</div>
@endsection
