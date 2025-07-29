@extends('layouts.app')

@section('title', __('erp.hr'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('erp.hr') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('erp.hr_description') }}</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <x-stat-card
            title="{{ __('hr.total_employees') }}"
            :value="$stats['total_employees']"
            color="blue"
            :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('hr.active_employees') }}"
            :value="$stats['active_employees']"
            color="green"
            :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('hr.on_leave') }}"
            :value="$stats['on_leave']"
            color="yellow"
            :icon="'<svg class=\'w-6 h-6 text-yellow-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('hr.new_hires') }}"
            :value="$stats['new_hires']"
            color="purple"
            :icon="'<svg class=\'w-6 h-6 text-purple-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z\'></path></svg>'"
        />
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-card title="{{ __('hr.employee_management') }}">
            <div class="space-y-4">
                <p class="text-gray-600">{{ __('hr.manage_employee_records') }}</p>
                <div class="flex space-x-3">
                    <a href="{{ route('modules.hr.employees.index') }}" class="btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ __('hr.view_employees') }}
                    </a>
                    <a href="{{ route('modules.hr.employees.create') }}" class="btn-outline">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        {{ __('hr.add_employee') }}
                    </a>
                </div>
            </div>
        </x-card>

        <x-card title="{{ __('hr.leave_management') }}">
            <div class="space-y-4">
                <p class="text-gray-600">{{ __('hr.manage_leave_requests') }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">{{ __('hr.pending_requests') }}</span>
                    <span class="font-semibold text-orange-600">{{ $stats['pending_leave_requests'] }}</span>
                </div>
                <a href="{{ route('modules.hr.leave-requests.index') }}" class="btn-primary w-full">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ __('hr.manage_leave') }}
                </a>
            </div>
        </x-card>

        <x-card title="{{ __('hr.departments') }}">
            <div class="space-y-4">
                <p class="text-gray-600">{{ __('hr.manage_departments') }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">{{ __('hr.total_departments') }}</span>
                    <span class="font-semibold text-blue-600">{{ $stats['departments'] }}</span>
                </div>
                <a href="{{ route('modules.hr.departments.index') }}" class="btn-primary w-full">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    {{ __('hr.view_departments') }}
                </a>
            </div>
        </x-card>
    </div>

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Hires -->
        <x-card title="{{ __('hr.recent_hires') }}">
            @if($recentHires->count() > 0)
                <div class="space-y-3">
                    @foreach($recentHires as $hire)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <div>
                                <div class="font-medium text-gray-900">{{ $hire->first_name }} {{ $hire->last_name }}</div>
                                <div class="text-sm text-gray-500">{{ $hire->department->name ?? 'N/A' }} • {{ $hire->role->name ?? 'N/A' }}</div>
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $hire->hire_date ? $hire->hire_date->diffForHumans() : 'N/A' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">{{ __('hr.no_recent_hires') }}</p>
            @endif
        </x-card>

        <!-- Pending Leave Requests -->
        <x-card title="{{ __('hr.pending_leave_requests') }}">
            @if($pendingLeaveRequests->count() > 0)
                <div class="space-y-3">
                    @foreach($pendingLeaveRequests as $request)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <div>
                                <div class="font-medium text-gray-900">{{ $request->employee->first_name }} {{ $request->employee->last_name }}</div>
                                <div class="text-sm text-gray-500">{{ $request->leave_type }} • {{ $request->days_requested }} {{ __('hr.days') }}</div>
                            </div>
                            <div class="text-sm text-orange-600 font-medium">
                                {{ __('hr.pending') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">{{ __('hr.no_pending_requests') }}</p>
            @endif
        </x-card>
    </div>
</div>
@endsection
