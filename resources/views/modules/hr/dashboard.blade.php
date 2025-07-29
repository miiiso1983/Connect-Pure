@extends('layouts.app')

@section('title', __('hr.hr_management'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('hr.dashboard') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('hr.overview') }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.hr.employees.create') }}" class="btn-primary">
                {{ __('hr.add_employee') }}
            </a>
            <a href="{{ route('modules.hr.leave-requests.create') }}" class="btn-secondary">
                {{ __('hr.request_leave') }}
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Employees -->
        <x-stat-card
            title="{{ __('hr.total_employees') }}"
            :value="$dashboardData['statistics']['total_employees']"
            color="blue"
            :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z\'></path></svg>'"
        />

        <!-- Active Employees -->
        <x-stat-card
            title="{{ __('hr.active_employees') }}"
            :value="$dashboardData['statistics']['active_employees']"
            color="green"
            :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />

        <!-- Departments -->
        <x-stat-card
            title="{{ __('hr.departments_count') }}"
            :value="$dashboardData['statistics']['departments_count']"
            color="purple"
            :icon="'<svg class=\'w-6 h-6 text-purple-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4\'></path></svg>'"
        />

        <!-- Pending Leave Requests -->
        <x-stat-card
            title="{{ __('hr.pending_leave_requests') }}"
            :value="$dashboardData['statistics']['pending_leave_requests']"
            color="orange"
            :icon="'<svg class=\'w-6 h-6 text-orange-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />
    </div>

    <!-- Attendance Today -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('hr.attendance_today') }}</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">{{ __('hr.present') }}</span>
                    <span class="text-sm font-medium text-green-600">{{ $dashboardData['statistics']['present_today'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">{{ __('hr.late') }}</span>
                    <span class="text-sm font-medium text-yellow-600">{{ $dashboardData['statistics']['late_today'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">{{ __('hr.absent') }}</span>
                    <span class="text-sm font-medium text-red-600">{{ $dashboardData['statistics']['absent_today'] }}</span>
                </div>
                <div class="pt-2 border-t">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-900">{{ __('hr.attendance_rate') }}</span>
                        <span class="text-sm font-bold text-blue-600">{{ $dashboardData['statistics']['attendance_rate'] }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Salary Overview -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('hr.monthly_payroll') }}</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">{{ __('hr.average_salary') }}</span>
                    <span class="text-sm font-medium text-gray-900">${{ number_format($dashboardData['statistics']['average_salary'], 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">{{ __('hr.total_salary_expense') }}</span>
                    <span class="text-sm font-medium text-gray-900">${{ number_format($dashboardData['statistics']['total_salary_expense'], 0) }}</span>
                </div>
                <div class="pt-2 border-t">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-900">{{ __('hr.monthly_payroll') }}</span>
                        <span class="text-sm font-bold text-green-600">${{ number_format($dashboardData['statistics']['monthly_payroll'], 0) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('hr.quick_actions') }}</h3>
            <div class="space-y-3">
                <a href="{{ route('modules.hr.employees.create') }}" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md">
                    {{ __('hr.add_employee') }}
                </a>
                <a href="{{ route('modules.hr.leave-requests.create') }}" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md">
                    {{ __('hr.request_leave') }}
                </a>
                <a href="{{ route('modules.hr.attendance.create') }}" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md">
                    {{ __('hr.record_attendance') }}
                </a>
                <a href="{{ route('modules.hr.payroll.create') }}" class="block w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md">
                    {{ __('hr.create_payroll') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Hires -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('hr.recent_hires') }}</h3>
            </div>
            <div class="p-6">
                @if($dashboardData['recent_activities']['recent_hires']->count() > 0)
                    <div class="space-y-3">
                        @foreach($dashboardData['recent_activities']['recent_hires'] as $employee)
                            <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $employee->display_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $employee->department->display_name }} • {{ $employee->hire_date->format('M d, Y') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">{{ __('hr.no_recent_hires') }}</p>
                @endif
            </div>
        </div>

        <!-- Recent Leave Requests -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('hr.recent_leave_requests') }}</h3>
            </div>
            <div class="p-6">
                @if($dashboardData['recent_activities']['recent_leave_requests']->count() > 0)
                    <div class="space-y-3">
                        @foreach($dashboardData['recent_activities']['recent_leave_requests'] as $request)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $request->status_color }}-100 text-{{ $request->status_color }}-800">
                                            {{ $request->status_text }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $request->employee->display_name }}</p>
                                        <p class="text-sm text-gray-500">{{ $request->leave_type_text }} • {{ $request->total_days }} {{ __('hr.days') }}</p>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $request->created_at->diffForHumans() }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">{{ __('hr.no_recent_leave_requests') }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Department Distribution Chart -->
    @if(count($dashboardData['distributions']['department_distribution']) > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('hr.employees_by_department') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($dashboardData['distributions']['department_distribution'] as $dept)
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-medium text-gray-900">{{ $dept['name'] }}</h4>
                        <span class="text-lg font-bold text-blue-600">{{ $dept['count'] }}</span>
                    </div>
                    <div class="mt-2">
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($dept['count'] / $dashboardData['statistics']['active_employees']) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    // Auto-refresh dashboard data every 5 minutes
    setInterval(function() {
        fetch('{{ route("modules.hr.quick-stats") }}')
            .then(response => response.json())
            .then(data => {
                // Update quick stats if needed
                console.log('Dashboard data refreshed', data);
            })
            .catch(error => console.error('Error refreshing dashboard:', error));
    }, 300000); // 5 minutes
</script>
@endpush
@endsection
