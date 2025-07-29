@extends('layouts.app')

@section('title', __('erp.performance_analytics'))

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('erp.performance_analytics') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('erp.detailed_performance_insights') }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.performance.reports.index') }}" class="btn-secondary">
                {{ __('erp.view_reports') }}
            </a>
            <a href="{{ route('modules.performance.dashboard') }}" class="btn-secondary">
                {{ __('erp.dashboard') }}
            </a>
        </div>
    </div>

    <!-- Analytics Filters -->
    <x-card title="{{ __('erp.analytics_filters') }}">
        <form method="GET" action="{{ route('modules.performance.analytics') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="period" class="block text-sm font-medium text-gray-700 mb-1">{{ __('erp.time_period') }}</label>
                <select name="period" id="period" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="week" {{ request('period', 'month') === 'week' ? 'selected' : '' }}>{{ __('erp.last_week') }}</option>
                    <option value="month" {{ request('period', 'month') === 'month' ? 'selected' : '' }}>{{ __('erp.last_month') }}</option>
                    <option value="quarter" {{ request('period') === 'quarter' ? 'selected' : '' }}>{{ __('erp.last_quarter') }}</option>
                    <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>{{ __('erp.last_year') }}</option>
                </select>
            </div>
            
            <div>
                <label for="employee" class="block text-sm font-medium text-gray-700 mb-1">{{ __('erp.employee') }}</label>
                <input type="text" name="employee" id="employee" value="{{ request('employee') }}" 
                       placeholder="{{ __('erp.all_employees') }}" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="metric" class="block text-sm font-medium text-gray-700 mb-1">{{ __('erp.metric_type') }}</label>
                <select name="metric" id="metric" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all" {{ request('metric', 'all') === 'all' ? 'selected' : '' }}>{{ __('erp.all_metrics') }}</option>
                    <option value="productivity" {{ request('metric') === 'productivity' ? 'selected' : '' }}>{{ __('erp.productivity') }}</option>
                    <option value="efficiency" {{ request('metric') === 'efficiency' ? 'selected' : '' }}>{{ __('erp.efficiency') }}</option>
                    <option value="quality" {{ request('metric') === 'quality' ? 'selected' : '' }}>{{ __('erp.quality') }}</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="btn-primary w-full">
                    {{ __('erp.update_analytics') }}
                </button>
            </div>
        </form>
    </x-card>

    <!-- Key Performance Indicators -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <x-performance.kpi-widget 
            title="{{ __('erp.overall_productivity') }}"
            value="87.5%"
            subtitle="{{ __('erp.vs_last_period') }}: +5.2%"
            color="blue"
            trend="5.2"
            trendDirection="up"
            :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6\'></path></svg>'"
        />
        
        <x-performance.kpi-widget 
            title="{{ __('erp.team_efficiency') }}"
            value="92.1%"
            subtitle="{{ __('erp.vs_last_period') }}: +2.8%"
            color="green"
            trend="2.8"
            trendDirection="up"
            :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 10V3L4 14h7v7l9-11h-7z\'></path></svg>'"
        />
        
        <x-performance.kpi-widget 
            title="{{ __('erp.quality_score') }}"
            value="94.3%"
            subtitle="{{ __('erp.vs_last_period') }}: -1.2%"
            color="purple"
            trend="1.2"
            trendDirection="down"
            :icon="'<svg class=\'w-6 h-6 text-purple-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z\'></path></svg>'"
        />
        
        <x-performance.kpi-widget 
            title="{{ __('erp.on_time_delivery') }}"
            value="89.7%"
            subtitle="{{ __('erp.vs_last_period') }}: +3.1%"
            color="yellow"
            trend="3.1"
            trendDirection="up"
            :icon="'<svg class=\'w-6 h-6 text-yellow-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />
    </div>

    <!-- Performance Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Productivity Trends -->
        <x-performance.performance-chart
            chartId="productivityChart"
            title="{{ __('erp.productivity_trends') }}"
            type="line"
            height="350"
            :showLegend="true"
        />

        <!-- Task Distribution -->
        <x-performance.performance-chart
            chartId="taskDistributionChart"
            title="{{ __('erp.task_category_distribution') }}"
            type="doughnut"
            height="350"
            :showLegend="true"
        />
    </div>

    <!-- Additional Analytics Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Team Performance Comparison -->
        <x-performance.performance-chart
            chartId="teamComparisonChart"
            title="{{ __('erp.team_performance_comparison') }}"
            type="bar"
            height="300"
        />

        <!-- Weekly Progress -->
        <x-performance.performance-chart
            chartId="weeklyProgressChart"
            title="{{ __('erp.weekly_progress') }}"
            type="line"
            height="300"
        />

        <!-- Task Status Overview -->
        <x-performance.performance-chart
            chartId="taskStatusChart"
            title="{{ __('erp.task_status_overview') }}"
            type="doughnut"
            height="300"
        />
    </div>

    <!-- Detailed Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Employee Performance Ranking -->
        <x-card title="{{ __('erp.employee_performance_ranking') }}">
            <div class="space-y-4">
                @php
                    $employees = [
                        ['name' => 'Ahmed Al-Rashid', 'score' => 95.2, 'change' => '+2.1'],
                        ['name' => 'Sara Mohammed', 'score' => 92.8, 'change' => '+1.5'],
                        ['name' => 'Omar Hassan', 'score' => 89.4, 'change' => '-0.8'],
                        ['name' => 'Fatima Al-Zahra', 'score' => 87.6, 'change' => '+3.2'],
                        ['name' => 'Khalid Al-Mansoori', 'score' => 85.1, 'change' => '+0.9'],
                    ];
                @endphp
                
                @foreach($employees as $index => $employee)
                    <div class="flex items-center justify-between p-3 {{ $index === 0 ? 'bg-yellow-50 border border-yellow-200' : 'bg-gray-50' }} rounded-lg">
                        <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                            <div class="flex-shrink-0">
                                @if($index === 0)
                                    <div class="w-6 h-6 bg-yellow-500 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-medium text-gray-700">{{ $index + 1 }}</span>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $employee['name'] }}</p>
                                <p class="text-sm text-gray-600">{{ number_format($employee['score'], 1) }}% {{ __('erp.performance') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm {{ str_starts_with($employee['change'], '+') ? 'text-green-600' : 'text-red-600' }}">
                                {{ $employee['change'] }}%
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>

        <!-- Performance Insights -->
        <x-card title="{{ __('erp.performance_insights') }}">
            <div class="space-y-4">
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-start space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <svg class="w-5 h-5 text-green-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h4 class="font-medium text-green-900">{{ __('erp.positive_trend') }}</h4>
                            <p class="text-sm text-green-700">{{ __('erp.productivity_increased_insight') }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h4 class="font-medium text-blue-900">{{ __('erp.recommendation') }}</h4>
                            <p class="text-sm text-blue-700">{{ __('erp.focus_on_testing_tasks') }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h4 class="font-medium text-yellow-900">{{ __('erp.attention_needed') }}</h4>
                            <p class="text-sm text-yellow-700">{{ __('erp.deadline_management_insight') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Quick Actions -->
        <x-card title="{{ __('erp.quick_actions') }}">
            <div class="space-y-3">
                <a href="{{ route('modules.performance.tasks.create') }}" class="w-full btn-primary block text-center">
                    {{ __('erp.create_new_task') }}
                </a>
                
                <a href="{{ route('modules.performance.reports.index') }}" class="w-full btn-secondary block text-center">
                    {{ __('erp.generate_report') }}
                </a>
                
                <button onclick="exportAnalytics()" class="w-full btn-secondary">
                    {{ __('erp.export_analytics') }}
                </button>
                
                <button onclick="scheduleReport()" class="w-full btn-secondary">
                    {{ __('erp.schedule_report') }}
                </button>
            </div>
        </x-card>
    </div>

    <!-- Performance Heatmap -->
    <x-card title="{{ __('erp.performance_heatmap') }}">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h4 class="font-medium text-gray-900">{{ __('erp.weekly_activity_overview') }}</h4>
                <div class="flex items-center space-x-2 text-sm text-gray-600 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <span>{{ __('erp.less') }}</span>
                    <div class="flex space-x-1 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <div class="w-3 h-3 bg-gray-200 rounded-sm"></div>
                        <div class="w-3 h-3 bg-green-200 rounded-sm"></div>
                        <div class="w-3 h-3 bg-green-400 rounded-sm"></div>
                        <div class="w-3 h-3 bg-green-600 rounded-sm"></div>
                        <div class="w-3 h-3 bg-green-800 rounded-sm"></div>
                    </div>
                    <span>{{ __('erp.more') }}</span>
                </div>
            </div>
            
            <div class="grid grid-cols-7 gap-1">
                @for($week = 0; $week < 12; $week++)
                    @for($day = 0; $day < 7; $day++)
                        @php
                            $intensity = rand(0, 4);
                            $colors = ['bg-gray-200', 'bg-green-200', 'bg-green-400', 'bg-green-600', 'bg-green-800'];
                        @endphp
                        <div class="w-3 h-3 {{ $colors[$intensity] }} rounded-sm" title="{{ __('erp.activity_level') }}: {{ $intensity + 1 }}"></div>
                    @endfor
                @endfor
            </div>
        </div>
    </x-card>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize productivity chart
    const productivityData = {
        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
        datasets: [
            {
                label: '{{ __("erp.productivity") }}',
                data: [85, 87, 89, 92],
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            },
            {
                label: '{{ __("erp.efficiency") }}',
                data: [82, 85, 88, 90],
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4
            }
        ]
    };
    
    initChart('productivityChart', 'line', productivityData);
    
    // Initialize task distribution chart
    const distributionData = {
        labels: ['{{ __("erp.development") }}', '{{ __("erp.design") }}', '{{ __("erp.testing") }}', '{{ __("erp.documentation") }}', '{{ __("erp.meeting") }}'],
        datasets: [{
            data: [35, 20, 25, 10, 10],
            backgroundColor: ['#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#EF4444'],
            borderWidth: 0
        }]
    };
    
    initChart('taskDistributionChart', 'doughnut', distributionData);

    // Initialize team comparison chart
    const teamComparisonData = {
        labels: ['{{ __("erp.development_team") }}', '{{ __("erp.design_team") }}', '{{ __("erp.qa_team") }}', '{{ __("erp.marketing_team") }}'],
        datasets: [{
            label: '{{ __("erp.performance_score") }}',
            data: [92, 88, 85, 90],
            backgroundColor: ['#3B82F6', '#8B5CF6', '#10B981', '#F59E0B'],
            borderColor: ['#2563EB', '#7C3AED', '#059669', '#D97706'],
            borderWidth: 2
        }]
    };

    initChart('teamComparisonChart', 'bar', teamComparisonData);

    // Initialize weekly progress chart
    const weeklyProgressData = {
        labels: ['{{ __("erp.week") }} 1', '{{ __("erp.week") }} 2', '{{ __("erp.week") }} 3', '{{ __("erp.week") }} 4'],
        datasets: [{
            label: '{{ __("erp.completed_tasks") }}',
            data: [45, 52, 48, 61],
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true
        }]
    };

    initChart('weeklyProgressChart', 'line', weeklyProgressData);

    // Initialize task status chart
    const taskStatusData = {
        labels: ['{{ __("erp.completed") }}', '{{ __("erp.in_progress") }}', '{{ __("erp.pending") }}', '{{ __("erp.overdue") }}'],
        datasets: [{
            data: [65, 20, 10, 5],
            backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#EF4444'],
            borderWidth: 0
        }]
    };

    initChart('taskStatusChart', 'doughnut', taskStatusData);
});

// Enhanced functions with real-time updates
function updateTimeRange(range) {
    showChartLoading('productivityChart');
    showChartLoading('taskDistributionChart');
    showChartLoading('teamComparisonChart');
    showChartLoading('weeklyProgressChart');
    showChartLoading('taskStatusChart');

    // Simulate API call
    setTimeout(() => {
        hideChartLoading('productivityChart');
        hideChartLoading('taskDistributionChart');
        hideChartLoading('teamComparisonChart');
        hideChartLoading('weeklyProgressChart');
        hideChartLoading('taskStatusChart');

        console.log('Updated charts for time range:', range);
    }, 1500);
}

function refreshCharts() {
    const timeRange = document.getElementById('timeRangeSelect').value;
    updateTimeRange(timeRange);
}

function exportAnalytics() {
    const format = prompt('{{ __("erp.export_format") }} (csv/pdf/excel):', 'pdf');
    if (format) {
        alert('{{ __("erp.exporting_analytics") }} ' + format.toUpperCase() + '...');
        // In real implementation, this would trigger a download
    }
}

function scheduleReport() {
    alert('{{ __("erp.scheduling_report") }}...');
}
</script>
@endpush
@endsection
