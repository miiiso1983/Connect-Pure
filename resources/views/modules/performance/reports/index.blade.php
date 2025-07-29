@extends('layouts.app')

@section('title', __('erp.performance_reports'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('erp.performance_reports') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('erp.generate_and_view_performance_reports') }}</p>
        </div>
        <a href="{{ route('modules.performance.dashboard') }}" class="btn-secondary">
            {{ __('erp.back_to_dashboard') }}
        </a>
    </div>

    <!-- Report Filters -->
    <x-card title="{{ __('erp.report_filters') }}">
        <form method="GET" action="{{ route('modules.performance.reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">{{ __('erp.report_type') }}</label>
                <select name="type" id="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="weekly" {{ request('type', 'weekly') === 'weekly' ? 'selected' : '' }}>{{ __('erp.weekly_report') }}</option>
                    <option value="monthly" {{ request('type') === 'monthly' ? 'selected' : '' }}>{{ __('erp.monthly_report') }}</option>
                </select>
            </div>
            
            <div>
                <label for="employee" class="block text-sm font-medium text-gray-700 mb-1">{{ __('erp.employee') }}</label>
                <input type="text" name="employee" id="employee" value="{{ request('employee') }}" 
                       placeholder="{{ __('erp.all_employees') }}" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('erp.start_date') }}</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="btn-primary w-full">
                    {{ __('erp.generate_report') }}
                </button>
            </div>
        </form>
    </x-card>

    <!-- Report Results -->
    @if(isset($data))
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Report Summary -->
            <div class="lg:col-span-2">
                <x-card title="{{ __('erp.report_summary') }} - {{ $data['period'] }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-blue-900">{{ __('erp.tasks_created') }}</p>
                                    <p class="text-2xl font-bold text-blue-600">{{ $data['tasks_created'] }}</p>
                                </div>
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-green-900">{{ __('erp.tasks_completed') }}</p>
                                    <p class="text-2xl font-bold text-green-600">{{ $data['tasks_completed'] }}</p>
                                </div>
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-red-900">{{ __('erp.tasks_overdue') }}</p>
                                    <p class="text-2xl font-bold text-red-600">{{ $data['tasks_overdue'] }}</p>
                                </div>
                                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-purple-900">{{ __('erp.avg_completion_time') }}</p>
                                    <p class="text-2xl font-bold text-purple-600">{{ $data['avg_completion_time'] ?? __('erp.no_data') }}</p>
                                </div>
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Report Actions -->
            <div>
                <x-card title="{{ __('erp.report_actions') }}">
                    <div class="space-y-3">
                        <button onclick="window.print()" class="w-full btn-primary">
                            {{ __('erp.print_report') }}
                        </button>
                        
                        <button onclick="exportReport('pdf')" class="w-full btn-secondary">
                            {{ __('erp.export_pdf') }}
                        </button>
                        
                        <button onclick="exportReport('excel')" class="w-full btn-secondary">
                            {{ __('erp.export_excel') }}
                        </button>
                        
                        <a href="{{ route('modules.performance.analytics') }}" class="w-full btn-secondary block text-center">
                            {{ __('erp.view_analytics') }}
                        </a>
                    </div>
                </x-card>
            </div>
        </div>

        <!-- Detailed Performance Chart -->
        <x-performance.performance-chart 
            chartId="reportChart"
            title="{{ __('erp.performance_overview') }} - {{ $data['period'] }}"
            type="bar"
            height="400"
        />
    @else
        <!-- Default Report Templates -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Weekly Report Template -->
            <x-card title="{{ __('erp.weekly_report') }}">
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <div>
                            <h3 class="font-medium text-gray-900">{{ __('erp.weekly_performance') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('erp.last_7_days') }}</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-700">{{ __('erp.weekly_report_description') }}</p>
                    <a href="{{ route('modules.performance.reports.index', ['type' => 'weekly']) }}" class="btn-primary w-full block text-center">
                        {{ __('erp.generate_weekly_report') }}
                    </a>
                </div>
            </x-card>

            <!-- Monthly Report Template -->
            <x-card title="{{ __('erp.monthly_report') }}">
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <h3 class="font-medium text-gray-900">{{ __('erp.monthly_performance') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('erp.current_month') }}</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-700">{{ __('erp.monthly_report_description') }}</p>
                    <a href="{{ route('modules.performance.reports.index', ['type' => 'monthly']) }}" class="btn-primary w-full block text-center">
                        {{ __('erp.generate_monthly_report') }}
                    </a>
                </div>
            </x-card>

            <!-- Custom Report Template -->
            <x-card title="{{ __('erp.custom_report') }}">
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                        </svg>
                        <div>
                            <h3 class="font-medium text-gray-900">{{ __('erp.custom_date_range') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('erp.flexible_reporting') }}</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-700">{{ __('erp.custom_report_description') }}</p>
                    <button onclick="showCustomReportModal()" class="btn-primary w-full">
                        {{ __('erp.create_custom_report') }}
                    </button>
                </div>
            </x-card>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <x-performance.kpi-widget 
                title="{{ __('erp.total_tasks_this_week') }}"
                value="24"
                color="blue"
                :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01\'></path></svg>'"
            />
            
            <x-performance.kpi-widget 
                title="{{ __('erp.completed_this_week') }}"
                value="18"
                color="green"
                :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
            />
            
            <x-performance.kpi-widget 
                title="{{ __('erp.team_efficiency') }}"
                value="87%"
                color="purple"
                :icon="'<svg class=\'w-6 h-6 text-purple-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 10V3L4 14h7v7l9-11h-7z\'></path></svg>'"
            />
            
            <x-performance.kpi-widget 
                title="{{ __('erp.avg_task_time') }}"
                value="2.3d"
                color="yellow"
                :icon="'<svg class=\'w-6 h-6 text-yellow-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
            />
        </div>
    @endif
</div>

<!-- Custom Report Modal -->
<div id="customReportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('erp.create_custom_report') }}</h3>
                <form action="{{ route('modules.performance.reports.index') }}" method="GET" class="space-y-4">
                    <div>
                        <label for="modal_type" class="block text-sm font-medium text-gray-700 mb-1">{{ __('erp.report_type') }}</label>
                        <select name="type" id="modal_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="weekly">{{ __('erp.weekly_report') }}</option>
                            <option value="monthly">{{ __('erp.monthly_report') }}</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="modal_employee" class="block text-sm font-medium text-gray-700 mb-1">{{ __('erp.employee') }}</label>
                        <input type="text" name="employee" id="modal_employee" placeholder="{{ __('erp.all_employees') }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="modal_start_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('erp.start_date') }}</label>
                        <input type="date" name="start_date" id="modal_start_date" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    
                    <div class="flex space-x-3 pt-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <button type="submit" class="flex-1 btn-primary">
                            {{ __('erp.generate_report') }}
                        </button>
                        <button type="button" onclick="hideCustomReportModal()" class="flex-1 btn-secondary">
                            {{ __('erp.cancel') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showCustomReportModal() {
    document.getElementById('customReportModal').classList.remove('hidden');
}

function hideCustomReportModal() {
    document.getElementById('customReportModal').classList.add('hidden');
}

function exportReport(format) {
    // In a real application, you would make an AJAX request to export the report
    alert('{{ __("erp.exporting_report") }} ' + format.toUpperCase() + '...');
}

@if(isset($data))
// Initialize report chart
document.addEventListener('DOMContentLoaded', function() {
    const reportData = {
        labels: ['{{ __("erp.tasks_created") }}', '{{ __("erp.tasks_completed") }}', '{{ __("erp.tasks_overdue") }}'],
        datasets: [{
            label: '{{ __("erp.tasks") }}',
            data: [{{ $data['tasks_created'] ?? 0 }}, {{ $data['tasks_completed'] ?? 0 }}, {{ $data['tasks_overdue'] ?? 0 }}],
            backgroundColor: ['#3B82F6', '#10B981', '#EF4444'],
            borderWidth: 0
        }]
    };

    initChart('reportChart', 'bar', reportData);
});
@endif
</script>
@endpush
@endsection
