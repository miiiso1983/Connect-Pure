@extends('layouts.app')

@section('title', __('erp.performance_reports'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('erp.performance_reports') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('erp.generate_detailed_performance_reports') }}</p>
        </div>
        <div class="mt-4 lg:mt-0 flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.performance.export', ['period' => $period, 'employee' => $employee, 'format' => 'csv']) }}" 
               class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                {{ __('erp.export_csv') }}
            </a>
            <a href="{{ route('modules.performance.export', ['period' => $period, 'employee' => $employee, 'format' => 'pdf']) }}" 
               class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                {{ __('erp.export_pdf') }}
            </a>
        </div>
    </div>

    <!-- Filters -->
    <x-card title="{{ __('erp.report_filters') }}">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="period" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('erp.time_period') }}
                </label>
                <select name="period" id="period" class="form-select">
                    <option value="week" {{ $period === 'week' ? 'selected' : '' }}>{{ __('erp.this_week') }}</option>
                    <option value="month" {{ $period === 'month' ? 'selected' : '' }}>{{ __('erp.this_month') }}</option>
                    <option value="quarter" {{ $period === 'quarter' ? 'selected' : '' }}>{{ __('erp.this_quarter') }}</option>
                    <option value="year" {{ $period === 'year' ? 'selected' : '' }}>{{ __('erp.this_year') }}</option>
                </select>
            </div>

            <div>
                <label for="employee" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('erp.employee') }}
                </label>
                <select name="employee" id="employee" class="form-select">
                    <option value="">{{ __('erp.all_employees') }}</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp }}" {{ $employee === $emp ? 'selected' : '' }}>{{ $emp }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('erp.report_type') }}
                </label>
                <select name="type" id="type" class="form-select">
                    <option value="summary" {{ $reportType === 'summary' ? 'selected' : '' }}>{{ __('erp.summary_report') }}</option>
                    <option value="detailed" {{ $reportType === 'detailed' ? 'selected' : '' }}>{{ __('erp.detailed_report') }}</option>
                    <option value="productivity" {{ $reportType === 'productivity' ? 'selected' : '' }}>{{ __('erp.productivity_report') }}</option>
                    <option value="efficiency" {{ $reportType === 'efficiency' ? 'selected' : '' }}>{{ __('erp.efficiency_report') }}</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="btn-primary w-full">
                    {{ __('erp.generate_report') }}
                </button>
            </div>
        </form>
    </x-card>

    <!-- Report Content -->
    @if(isset($reportData))
        @if($reportType === 'summary')
            <!-- Summary Report -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-performance.kpi-widget
                    title="{{ __('erp.total_tasks') }}"
                    :value="$reportData['stats']['total_tasks']"
                    color="blue"
                />
                <x-performance.kpi-widget
                    title="{{ __('erp.completed_tasks') }}"
                    :value="$reportData['stats']['completed_tasks']"
                    color="green"
                />
                <x-performance.kpi-widget
                    title="{{ __('erp.overdue_tasks') }}"
                    :value="$reportData['stats']['overdue_tasks']"
                    color="red"
                />
            </div>

            <!-- Distribution Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <x-card title="{{ __('erp.task_by_category') }}">
                    <div class="space-y-3">
                        @foreach($reportData['stats']['task_distribution'] ?? [] as $category => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700 capitalize">{{ $category }}</span>
                            <span class="text-sm text-gray-900">{{ $count }}</span>
                        </div>
                        @endforeach
                    </div>
                </x-card>

                <x-card title="{{ __('erp.task_by_priority') }}">
                    <div class="space-y-3">
                        @foreach($reportData['stats']['priority_distribution'] ?? [] as $priority => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700 capitalize">{{ $priority }}</span>
                            <span class="text-sm text-gray-900">{{ $count }}</span>
                        </div>
                        @endforeach
                    </div>
                </x-card>

                <x-card title="{{ __('erp.task_by_status') }}">
                    <div class="space-y-3">
                        @foreach($reportData['stats']['status_distribution'] ?? [] as $status => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700 capitalize">{{ $status }}</span>
                            <span class="text-sm text-gray-900">{{ $count }}</span>
                        </div>
                        @endforeach
                    </div>
                </x-card>
            </div>

        @elseif($reportType === 'detailed')
            <!-- Detailed Report -->
            <x-card title="{{ __('erp.detailed_task_report') }}">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('erp.task') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('erp.status') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('erp.priority') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('erp.assigned_to') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('erp.due_date') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('erp.completion') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($reportData['tasks'] as $task)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                        <div class="text-sm text-gray-500">{{ $task->category }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $task->status_color }}-100 text-{{ $task->status_color }}-800">
                                        {{ ucfirst($task->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $task->priority_color }}-100 text-{{ $task->priority_color }}-800">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $task->assignments->pluck('employee_name')->implode(', ') ?: '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $task->due_date ? $task->due_date->format('M d, Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $task->completion_percentage ?? 0 }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $task->completion_percentage ?? 0 }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>

        @elseif($reportType === 'productivity')
            <!-- Productivity Report -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-performance.kpi-widget
                    title="{{ __('erp.avg_productivity') }}"
                    :value="number_format($reportData['avg_productivity'], 1)"
                    color="blue"
                />
                <x-performance.kpi-widget
                    title="{{ __('erp.productivity_trend') }}"
                    :value="ucfirst($reportData['productivity_trend'])"
                    color="green"
                />
            </div>

            @if(isset($reportData['productivity_by_employee']))
            <x-card title="{{ __('erp.productivity_by_employee') }}">
                <div class="space-y-4">
                    @foreach($reportData['productivity_by_employee'] as $employee => $score)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">{{ $employee }}</span>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $score }}%"></div>
                            </div>
                            <span class="text-sm text-gray-900">{{ number_format($score, 1) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </x-card>
            @endif

        @elseif($reportType === 'efficiency')
            <!-- Efficiency Report -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-performance.kpi-widget
                    title="{{ __('erp.avg_efficiency') }}"
                    :value="number_format($reportData['avg_efficiency'], 1) . '%'"
                    color="green"
                />
                <x-performance.kpi-widget
                    title="{{ __('erp.efficiency_trend') }}"
                    :value="ucfirst($reportData['efficiency_trend'])"
                    color="blue"
                />
            </div>

            @if(isset($reportData['efficiency_by_employee']))
            <x-card title="{{ __('erp.efficiency_by_employee') }}">
                <div class="space-y-4">
                    @foreach($reportData['efficiency_by_employee'] as $employee => $rate)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">{{ $employee }}</span>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ min($rate, 100) }}%"></div>
                            </div>
                            <span class="text-sm text-gray-900">{{ number_format($rate, 1) }}%</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </x-card>
            @endif
        @endif
    @else
        <!-- No Report Generated -->
        <x-card>
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('erp.no_report_generated') }}</h3>
                <p class="text-gray-500 mb-4">{{ __('erp.select_filters_and_generate_report') }}</p>
            </div>
        </x-card>
    @endif
</div>
@endsection
