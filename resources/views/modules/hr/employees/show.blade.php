@extends('layouts.app')

@section('title', $employee->full_name . ' - ' . __('hr.employee_details'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.hr.employees.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $employee->full_name }}</h1>
                <p class="text-gray-600 mt-1">{{ $employee->role->name ?? __('hr.no_role_assigned') }} • {{ $employee->department->name ?? __('hr.no_department') }}</p>
            </div>
        </div>
        <div class="mt-4 lg:mt-0 flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.hr.employees.edit', $employee) }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                {{ __('hr.edit_employee') }}
            </a>
            <button onclick="generateReport()" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                {{ __('hr.generate_report') }}
            </button>
        </div>
    </div>

    <!-- Employee Profile Card -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-purple-600">
            <div class="flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                <div class="flex-shrink-0">
                    @if($employee->avatar)
                        <img class="h-20 w-20 rounded-full border-4 border-white object-cover" src="{{ Storage::url($employee->avatar) }}" alt="{{ $employee->full_name }}">
                    @else
                        <div class="h-20 w-20 rounded-full border-4 border-white bg-white flex items-center justify-center">
                            <span class="text-2xl font-bold text-gray-600">
                                {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                            </span>
                        </div>
                    @endif
                </div>
                <div class="text-white">
                    <h2 class="text-2xl font-bold">{{ $employee->full_name }}</h2>
                    <p class="text-blue-100">{{ $employee->employee_id }}</p>
                    <div class="flex items-center mt-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $employee->status === 'active' ? 'bg-green-100 text-green-800' : 
                               ($employee->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($employee->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ __('hr.basic_information') }}</h3>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('hr.email') }}</dt>
                            <dd class="text-sm text-gray-900">{{ $employee->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('hr.phone') }}</dt>
                            <dd class="text-sm text-gray-900">{{ $employee->phone ?? __('hr.not_provided') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('hr.date_of_birth') }}</dt>
                            <dd class="text-sm text-gray-900">{{ $employee->date_of_birth ? $employee->date_of_birth->format('M d, Y') : __('hr.not_provided') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('hr.gender') }}</dt>
                            <dd class="text-sm text-gray-900">{{ $employee->gender ? ucfirst($employee->gender) : __('hr.not_provided') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Employment Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ __('hr.employment_information') }}</h3>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('hr.hire_date') }}</dt>
                            <dd class="text-sm text-gray-900">{{ $employee->hire_date->format('M d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('hr.employment_type') }}</dt>
                            <dd class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $employee->employment_type)) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('hr.salary') }}</dt>
                            <dd class="text-sm text-gray-900">${{ number_format($employee->salary, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('hr.manager') }}</dt>
                            <dd class="text-sm text-gray-900">
                                @if($employee->manager)
                                    <a href="{{ route('modules.hr.employees.show', $employee->manager) }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $employee->manager->full_name }}
                                    </a>
                                @else
                                    {{ __('hr.no_manager_assigned') }}
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Contact Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ __('hr.contact_information') }}</h3>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('hr.address') }}</dt>
                            <dd class="text-sm text-gray-900">{{ $employee->address ?? __('hr.not_provided') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('hr.city') }}</dt>
                            <dd class="text-sm text-gray-900">{{ $employee->city ?? __('hr.not_provided') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('hr.state') }}</dt>
                            <dd class="text-sm text-gray-900">{{ $employee->state ?? __('hr.not_provided') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('hr.emergency_contact') }}</dt>
                            <dd class="text-sm text-gray-900">{{ $employee->emergency_contact ?? __('hr.not_provided') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Section -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showTab('attendance')" class="tab-button active" data-tab="attendance">
                    {{ __('hr.attendance') }}
                </button>
                <button onclick="showTab('leave')" class="tab-button" data-tab="leave">
                    {{ __('hr.leave_requests') }}
                </button>
                <button onclick="showTab('performance')" class="tab-button" data-tab="performance">
                    {{ __('hr.performance') }}
                </button>
                <button onclick="showTab('documents')" class="tab-button" data-tab="documents">
                    {{ __('hr.documents') }}
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Attendance Tab -->
            <div id="attendance-tab" class="tab-content">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('hr.recent_attendance') }}</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('hr.date') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('hr.check_in') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('hr.check_out') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('hr.hours_worked') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('hr.status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($employee->attendanceRecords()->latest()->take(10)->get() as $attendance)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $attendance->date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $attendance->check_in ? $attendance->check_in->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $attendance->check_out ? $attendance->check_out->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $attendance->hours_worked ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $attendance->status === 'present' ? 'bg-green-100 text-green-800' : 
                                               ($attendance->status === 'absent' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        {{ __('hr.no_attendance_records') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Leave Tab -->
            <div id="leave-tab" class="tab-content hidden">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('hr.leave_requests') }}</h3>
                <div class="space-y-4">
                    @forelse($employee->leaveRequests()->latest()->take(5)->get() as $leave)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ $leave->leave_type }}</h4>
                                    <p class="text-sm text-gray-500">
                                        {{ $leave->start_date->format('M d, Y') }} - {{ $leave->end_date->format('M d, Y') }}
                                        ({{ $leave->days }} {{ __('hr.days') }})
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $leave->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                       ($leave->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </div>
                            @if($leave->reason)
                                <p class="text-sm text-gray-600 mt-2">{{ $leave->reason }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500">{{ __('hr.no_leave_requests') }}</p>
                    @endforelse
                </div>
            </div>

            <!-- Performance Tab -->
            <div id="performance-tab" class="tab-content hidden">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('hr.performance_overview') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-blue-900">{{ __('hr.overall_rating') }}</h4>
                        <p class="text-2xl font-bold text-blue-600">4.2/5</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-green-900">{{ __('hr.completed_goals') }}</h4>
                        <p class="text-2xl font-bold text-green-600">8/10</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-purple-900">{{ __('hr.training_hours') }}</h4>
                        <p class="text-2xl font-bold text-purple-600">24h</p>
                    </div>
                </div>
            </div>

            <!-- Documents Tab -->
            <div id="documents-tab" class="tab-content hidden">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('hr.employee_documents') }}</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="text-sm text-gray-900">{{ __('hr.employment_contract') }}</span>
                        </div>
                        <button class="text-blue-600 hover:text-blue-800 text-sm">{{ __('hr.download') }}</button>
                    </div>
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="text-sm text-gray-900">{{ __('hr.id_copy') }}</span>
                        </div>
                        <button class="text-blue-600 hover:text-blue-800 text-sm">{{ __('hr.download') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    // Show selected tab content
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    // Add active class to selected tab button
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
}

function generateReport() {
    alert('{{ __("hr.generating_employee_report") }}...');
}
</script>

<style>
.tab-button {
    @apply py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300;
}

.tab-button.active {
    @apply border-blue-500 text-blue-600;
}
</style>
@endpush
@endsection
