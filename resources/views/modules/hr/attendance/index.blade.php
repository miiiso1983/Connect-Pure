@extends('layouts.app')

@section('title', __('hr.attendance_tracking'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('hr.attendance_tracking') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('hr.monitor_employee_attendance_and_working_hours') }}</p>
        </div>
        <div class="mt-4 lg:mt-0 flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <button onclick="bulkCheckIn()" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                {{ __('hr.bulk_check_in') }}
            </button>
            <a href="{{ route('modules.hr.attendance.reports') }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                {{ __('hr.reports') }}
            </a>
        </div>
    </div>

    <!-- Quick Check-In/Out Panel -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('hr.quick_attendance') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Check In -->
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-lg font-medium text-green-900">{{ __('hr.check_in') }}</h4>
                    <div class="text-2xl font-bold text-green-600" id="currentTime">
                        {{ now()->format('H:i:s') }}
                    </div>
                </div>
                <form id="checkInForm" class="space-y-3">
                    @csrf
                    <div>
                        <select name="employee_id" class="form-select" required>
                            <option value="">{{ __('hr.select_employee') }}</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <textarea name="notes" placeholder="{{ __('hr.optional_notes') }}" class="form-textarea" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn-primary w-full">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        {{ __('hr.check_in_now') }}
                    </button>
                </form>
            </div>

            <!-- Check Out -->
            <div class="bg-red-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-lg font-medium text-red-900">{{ __('hr.check_out') }}</h4>
                    <div class="text-sm text-red-600">
                        {{ __('hr.current_date') }}: {{ now()->format('M d, Y') }}
                    </div>
                </div>
                <form id="checkOutForm" class="space-y-3">
                    @csrf
                    <div>
                        <select name="employee_id" class="form-select" required>
                            <option value="">{{ __('hr.select_employee') }}</option>
                            @foreach($checkedInEmployees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <textarea name="notes" placeholder="{{ __('hr.optional_notes') }}" class="form-textarea" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn-danger w-full">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        {{ __('hr.check_out_now') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('hr.present_today') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $summary['present_today'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('hr.absent_today') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $summary['absent_today'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('hr.late_arrivals') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $summary['late_today'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('hr.avg_hours_today') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($summary['avg_hours_today'], 1) }}h</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
        <form method="GET" action="{{ route('modules.hr.attendance.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('hr.employee') }}</label>
                <select name="employee_id" class="form-select">
                    <option value="">{{ __('hr.all_employees') }}</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('hr.status') }}</label>
                <select name="status" class="form-select">
                    <option value="">{{ __('hr.all_statuses') }}</option>
                    <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>{{ __('hr.present') }}</option>
                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>{{ __('hr.absent') }}</option>
                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>{{ __('hr.late') }}</option>
                    <option value="half_day" {{ request('status') == 'half_day' ? 'selected' : '' }}>{{ __('hr.half_day') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('hr.date_from') }}</label>
                <input type="date" name="date_from" value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}" class="form-input">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('hr.date_to') }}</label>
                <input type="date" name="date_to" value="{{ request('date_to', now()->format('Y-m-d')) }}" class="form-input">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('hr.search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('hr.search_employees') }}" class="form-input">
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="btn-primary">
                    {{ __('hr.filter') }}
                </button>
                <a href="{{ route('modules.hr.attendance.index') }}" class="btn-secondary">
                    {{ __('hr.clear') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Attendance Records Table -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('hr.attendance_records') }}</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('hr.employee') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('hr.date') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('hr.check_in') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('hr.check_out') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('hr.hours_worked') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('hr.status') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('hr.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($attendanceRecords as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                            <span class="text-white font-medium text-sm">
                                                {{ substr($record->employee->first_name, 0, 1) }}{{ substr($record->employee->last_name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $record->employee->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $record->employee->department->name ?? __('hr.no_department') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $record->date->format('M d, Y') }}
                                <div class="text-xs text-gray-500">{{ $record->date->format('l') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $record->check_in ? $record->check_in->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $record->check_out ? $record->check_out->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $record->hours_worked ? number_format($record->hours_worked, 1) . 'h' : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'present' => 'bg-green-100 text-green-800',
                                        'absent' => 'bg-red-100 text-red-800',
                                        'late' => 'bg-yellow-100 text-yellow-800',
                                        'half_day' => 'bg-blue-100 text-blue-800',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$record->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                    @if(!$record->check_out && $record->check_in)
                                        <button onclick="checkOut({{ $record->employee_id }})" 
                                                class="text-red-600 hover:text-red-900">
                                            {{ __('hr.check_out') }}
                                        </button>
                                    @endif
                                    
                                    <button onclick="editAttendance({{ $record->id }})" 
                                            class="text-blue-600 hover:text-blue-900">
                                        {{ __('hr.edit') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">{{ __('hr.no_attendance_records_found') }}</p>
                                    <p class="mt-1">{{ __('hr.start_tracking_attendance') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($attendanceRecords->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $attendanceRecords->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Update current time every second
setInterval(function() {
    const now = new Date();
    document.getElementById('currentTime').textContent = now.toLocaleTimeString();
}, 1000);

// Check In Form
document.getElementById('checkInForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/modules/hr/attendance/check-in', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || '{{ __("hr.error_occurred") }}');
        }
    });
});

// Check Out Form
document.getElementById('checkOutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/modules/hr/attendance/check-out', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || '{{ __("hr.error_occurred") }}');
        }
    });
});

function checkOut(employeeId) {
    if (confirm('{{ __("hr.confirm_check_out") }}')) {
        fetch('/modules/hr/attendance/check-out', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ employee_id: employeeId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || '{{ __("hr.error_occurred") }}');
            }
        });
    }
}

function editAttendance(recordId) {
    alert('{{ __("hr.edit_attendance_feature_coming_soon") }}');
}

function bulkCheckIn() {
    alert('{{ __("hr.bulk_check_in_feature_coming_soon") }}');
}
</script>
@endpush
@endsection
