@extends('layouts.app')

@section('title', __('erp.performance'))

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('erp.performance') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('erp.track_employee_productivity') }}</p>
        </div>
        <div class="mt-4 lg:mt-0 flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 {{ app()->getLocale() === 'ar' ? 'sm:space-x-reverse' : '' }}">
            <!-- Filters -->
            <div class="flex space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                <select id="periodFilter" class="form-select text-sm">
                    <option value="week" {{ $period === 'week' ? 'selected' : '' }}>{{ __('erp.this_week') }}</option>
                    <option value="month" {{ $period === 'month' ? 'selected' : '' }}>{{ __('erp.this_month') }}</option>
                    <option value="quarter" {{ $period === 'quarter' ? 'selected' : '' }}>{{ __('erp.this_quarter') }}</option>
                    <option value="year" {{ $period === 'year' ? 'selected' : '' }}>{{ __('erp.this_year') }}</option>
                </select>

                <select id="employeeFilter" class="form-select text-sm">
                    <option value="">{{ __('erp.all_employees') }}</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp }}" {{ $employee === $emp ? 'selected' : '' }}>{{ $emp }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                <a href="{{ route('modules.performance.dashboard') }}" class="btn-secondary">
                    {{ __('erp.performance_dashboard') }}
                </a>
                <a href="{{ route('modules.performance.tasks.create') }}" class="btn-primary">
                    {{ __('erp.create_task') }}
                </a>
            </div>
        </div>
    </div>

    <!-- KPI Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <x-performance.kpi-widget
            title="{{ __('erp.total_tasks') }}"
            :value="$stats['total_tasks']"
            color="blue"
            :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01\'></path></svg>'"
        />

        <x-performance.kpi-widget
            title="{{ __('erp.active_tasks') }}"
            :value="$stats['active_tasks']"
            color="yellow"
            :icon="'<svg class=\'w-6 h-6 text-yellow-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />

        <x-performance.kpi-widget
            title="{{ __('erp.completed_tasks') }}"
            :value="$stats['completed_tasks']"
            color="green"
            :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />

        <x-performance.kpi-widget
            title="{{ __('erp.overdue_tasks') }}"
            :value="$stats['overdue_tasks']"
            color="red"
            :icon="'<svg class=\'w-6 h-6 text-red-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z\'></path></svg>'"
        />
    </div>

    <!-- Performance Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <x-performance.kpi-widget
            title="{{ __('erp.completion_rate') }}"
            :value="number_format($stats['completion_rate'], 1) . '%'"
            subtitle="{{ __('erp.task_completion_rate') }}"
            color="blue"
            :percentage="$stats['completion_rate']"
            :trend="$stats['productivity_trend'] ?? 'neutral'"
            :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6\'></path></svg>'"
        />

        <x-performance.kpi-widget
            title="{{ __('erp.efficiency_rate') }}"
            :value="number_format($stats['avg_efficiency'], 1) . '%'"
            subtitle="{{ __('erp.estimated_vs_actual_time') }}"
            color="green"
            :percentage="$stats['avg_efficiency']"
            :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 10V3L4 14h7v7l9-11h-7z\'></path></svg>'"
        />

        <x-performance.kpi-widget
            title="{{ __('erp.avg_completion_time') }}"
            :value="$stats['avg_completion_time']"
            subtitle="{{ __('erp.average_task_duration') }}"
            color="purple"
            :icon="'<svg class=\'w-6 h-6 text-purple-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />

        <x-performance.kpi-widget
            title="{{ __('erp.total_employees') }}"
            :value="$stats['total_employees']"
            subtitle="{{ __('erp.active_team_members') }}"
            color="indigo"
            :icon="'<svg class=\'w-6 h-6 text-indigo-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z\'></path></svg>'"
        />
    </div>

    <!-- KPI Summary -->
    @if(isset($kpis))
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <x-performance.kpi-widget
            title="{{ __('erp.productivity_score') }}"
            :value="number_format($kpis['avg_productivity'], 1)"
            subtitle="{{ __('erp.out_of_100') }}"
            color="blue"
            :percentage="$kpis['avg_productivity']"
            :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z\'></path></svg>'"
        />

        <x-performance.kpi-widget
            title="{{ __('erp.efficiency_score') }}"
            :value="number_format($kpis['avg_efficiency'], 1) . '%'"
            subtitle="{{ __('erp.time_efficiency') }}"
            color="green"
            :percentage="$kpis['avg_efficiency']"
            :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 10V3L4 14h7v7l9-11h-7z\'></path></svg>'"
        />

        <x-performance.kpi-widget
            title="{{ __('erp.quality_score') }}"
            :value="number_format($kpis['avg_quality'], 1)"
            subtitle="{{ __('erp.work_quality') }}"
            color="yellow"
            :percentage="$kpis['avg_quality']"
            :icon="'<svg class=\'w-6 h-6 text-yellow-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z\'></path></svg>'"
        />

        <x-performance.kpi-widget
            title="{{ __('erp.overall_score') }}"
            :value="number_format($kpis['avg_overall'], 1)"
            subtitle="{{ __('erp.combined_performance') }}"
            color="purple"
            :percentage="$kpis['avg_overall']"
            :icon="'<svg class=\'w-6 h-6 text-purple-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />
    </div>
    @endif

    <!-- Charts Section -->
    @if(isset($chartData))
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Productivity Chart -->
        <x-card title="{{ __('erp.productivity_trend') }}">
            <div class="h-64">
                <canvas id="productivityChart"></canvas>
            </div>
        </x-card>

        <!-- Task Distribution Chart -->
        <x-card title="{{ __('erp.task_distribution') }}">
            <div class="h-64">
                <canvas id="taskDistributionChart"></canvas>
            </div>
        </x-card>
    </div>
    @endif

    <!-- Top Performers -->
    @if(isset($topPerformers) && $topPerformers->count() > 0)
    <x-card title="{{ __('erp.top_performers') }}">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('erp.employee') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('erp.productivity') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('erp.efficiency') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('erp.overall_score') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('erp.grade') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($topPerformers->take(5) as $performer)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ substr($performer->employee_name, 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $performer->employee_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $performer->employee_email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $performer->productivity_score }}%"></div>
                                </div>
                                <span class="text-sm text-gray-900">{{ number_format($performer->productivity_score, 1) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $performer->efficiency_rate }}%"></div>
                                </div>
                                <span class="text-sm text-gray-900">{{ number_format($performer->efficiency_rate, 1) }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">{{ number_format($performer->overall_score, 1) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $performer->performance_color }}-100 text-{{ $performer->performance_color }}-800">
                                {{ $performer->performance_grade }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
    @endif

    <!-- Recent Tasks and Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-card title="{{ __('erp.recent_tasks') }}">
            @if($recentTasks->count() > 0)
                <div class="space-y-4">
                    @foreach($recentTasks as $task)
                        <x-performance.task-card :task="$task" :show-assignments="false" />
                    @endforeach
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('modules.performance.tasks.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        {{ __('erp.view_all_tasks') }}
                    </a>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <p class="text-gray-500">{{ __('erp.no_tasks_found') }}</p>
                    <a href="{{ route('modules.performance.tasks.create') }}" class="mt-2 inline-block text-blue-600 hover:text-blue-800">
                        {{ __('erp.create_first_task') }}
                    </a>
                </div>
            @endif
        </x-card>

        <x-card title="{{ __('erp.quick_actions') }}">
            <div class="grid grid-cols-1 gap-4">
                <a href="{{ route('modules.performance.tasks.create') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div class="{{ app()->getLocale() === 'ar' ? 'mr-4' : 'ml-4' }}">
                        <h4 class="text-lg font-medium text-gray-900">{{ __('erp.create_task') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('erp.assign_new_task_to_team') }}</p>
                    </div>
                </a>

                <a href="{{ route('modules.performance.reports.index') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-200">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="{{ app()->getLocale() === 'ar' ? 'mr-4' : 'ml-4' }}">
                        <h4 class="text-lg font-medium text-gray-900">{{ __('erp.view_reports') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('erp.generate_performance_reports') }}</p>
                    </div>
                </a>

                <a href="{{ route('modules.performance.analytics') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors duration-200">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="{{ app()->getLocale() === 'ar' ? 'mr-4' : 'ml-4' }}">
                        <h4 class="text-lg font-medium text-gray-900">{{ __('erp.performance_analytics') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('erp.view_detailed_analytics') }}</p>
                    </div>
                </a>
            </div>
        </x-card>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const periodFilter = document.getElementById('periodFilter');
    const employeeFilter = document.getElementById('employeeFilter');

    function updateFilters() {
        const period = periodFilter.value;
        const employee = employeeFilter.value;
        const url = new URL(window.location);

        url.searchParams.set('period', period);
        if (employee) {
            url.searchParams.set('employee', employee);
        } else {
            url.searchParams.delete('employee');
        }

        window.location.href = url.toString();
    }

    periodFilter.addEventListener('change', updateFilters);
    employeeFilter.addEventListener('change', updateFilters);

    @if(isset($chartData))
    // Productivity Chart
    const productivityCtx = document.getElementById('productivityChart');
    if (productivityCtx) {
        new Chart(productivityCtx, {
            type: 'line',
            data: @json($chartData['productivity_chart']),
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    }

    // Task Distribution Chart
    const taskDistributionCtx = document.getElementById('taskDistributionChart');
    if (taskDistributionCtx && @json($chartData['task_distribution'])) {
        const distributionData = @json($chartData['task_distribution']);
        const labels = Object.keys(distributionData);
        const data = Object.values(distributionData);

        new Chart(taskDistributionCtx, {
            type: 'doughnut',
            data: {
                labels: labels.map(label => label.charAt(0).toUpperCase() + label.slice(1)),
                datasets: [{
                    data: data,
                    backgroundColor: [
                        '#3B82F6', // blue
                        '#10B981', // green
                        '#F59E0B', // yellow
                        '#EF4444', // red
                        '#8B5CF6', // purple
                        '#06B6D4', // cyan
                        '#84CC16'  // lime
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    }
    @endif
});
</script>
@endpush
