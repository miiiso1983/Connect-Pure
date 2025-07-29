@extends('layouts.app')

@section('title', __('erp.team_performance'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('erp.team_performance') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('erp.manage_team_productivity') }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.performance.reports.index') }}" class="btn-secondary">
                {{ __('erp.view_reports') }}
            </a>
            <a href="{{ route('modules.performance.tasks.create') }}" class="btn-primary">
                {{ __('erp.create_task') }}
            </a>
        </div>
    </div>

    <!-- Team Overview KPIs -->
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
            title="{{ __('erp.team_efficiency') }}"
            :value="number_format($stats['avg_efficiency'], 1) . '%'"
            color="purple"
            :icon="'<svg class=\'w-6 h-6 text-purple-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 10V3L4 14h7v7l9-11h-7z\'></path></svg>'"
        />
    </div>

    <!-- Performance Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Team Performance Trend -->
        <x-performance.performance-chart 
            chartId="teamPerformanceChart"
            title="{{ __('erp.team_performance_trends') }}"
            type="line"
            height="300"
        />
        
        <!-- Task Distribution -->
        <x-performance.performance-chart 
            chartId="taskDistributionChart"
            title="{{ __('erp.task_distribution') }}"
            type="doughnut"
            height="300"
        />
    </div>

    <!-- Top Performers and Team Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Performers -->
        <x-card title="{{ __('erp.top_performers') }}">
            @if(count($topPerformers) > 0)
                <div class="space-y-4">
                    @foreach($topPerformers as $index => $performer)
                        <div class="flex items-center justify-between p-4 {{ $index === 0 ? 'bg-yellow-50 border border-yellow-200' : 'bg-gray-50' }} rounded-lg">
                            <div class="flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                <div class="flex-shrink-0">
                                    @if($index === 0)
                                        <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">{{ $index + 1 }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $performer['employee_name'] }}</h4>
                                    <p class="text-sm text-gray-600">{{ __('erp.overall_score') }}: {{ number_format($performer['overall_score'], 1) }}%</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ $performer['overall_score'] >= 85 ? 'green' : ($performer['overall_score'] >= 75 ? 'blue' : 'yellow') }}-100 text-{{ $performer['overall_score'] >= 85 ? 'green' : ($performer['overall_score'] >= 75 ? 'blue' : 'yellow') }}-800">
                                        {{ $performer['overall_score'] >= 85 ? __('erp.excellent') : ($performer['overall_score'] >= 75 ? __('erp.good') : __('erp.average')) }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ number_format($performer['completion_rate'], 1) }}% {{ __('erp.completion') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p class="text-gray-500">{{ __('erp.no_performance_data') }}</p>
                </div>
            @endif
        </x-card>

        <!-- Team Performance Overview -->
        <x-card title="{{ __('erp.team_overview') }}">
            @if(count($teamPerformance) > 0)
                <div class="space-y-4">
                    @foreach($teamPerformance as $member)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $member['name'] }}</h4>
                                    <div class="flex items-center space-x-4 mt-1 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                        <span class="text-sm text-gray-600">{{ $member['stats']['total_tasks'] }} {{ __('erp.tasks') }}</span>
                                        <span class="text-sm text-gray-600">{{ number_format($member['stats']['completion_rate'], 1) }}% {{ __('erp.completion') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                    <span class="text-sm font-medium text-gray-900">{{ number_format($member['score'], 1) }}%</span>
                                    <div class="w-16 bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full transition-all duration-300 bg-blue-600" style="width: {{ $member['score'] }}%"></div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 mt-1 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                    <span class="text-xs text-green-600">{{ $member['stats']['completed_tasks'] }} {{ __('erp.completed') }}</span>
                                    @if($member['stats']['overdue_tasks'] > 0)
                                        <span class="text-xs text-red-600">{{ $member['stats']['overdue_tasks'] }} {{ __('erp.overdue') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p class="text-gray-500">{{ __('erp.no_team_data') }}</p>
                </div>
            @endif
        </x-card>
    </div>

    <!-- Recent Activity and Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activity -->
        <x-card title="{{ __('erp.recent_activity') }}">
            <div class="space-y-4">
                <div class="flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ __('erp.task_completed') }}</p>
                        <p class="text-xs text-gray-500">{{ __('erp.ahmed_completed_auth_system') }}</p>
                        <p class="text-xs text-gray-400">{{ __('erp.2_hours_ago') }}</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ __('erp.new_task_assigned') }}</p>
                        <p class="text-xs text-gray-500">{{ __('erp.ui_design_assigned_to_sara') }}</p>
                        <p class="text-xs text-gray-400">{{ __('erp.4_hours_ago') }}</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ __('erp.deadline_approaching') }}</p>
                        <p class="text-xs text-gray-500">{{ __('erp.api_testing_due_tomorrow') }}</p>
                        <p class="text-xs text-gray-400">{{ __('erp.6_hours_ago') }}</p>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Quick Actions -->
        <x-card title="{{ __('erp.quick_actions') }}">
            <div class="grid grid-cols-1 gap-4">
                <a href="{{ route('modules.performance.tasks.create') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div class="{{ app()->getLocale() === 'ar' ? 'mr-4' : 'ml-4' }}">
                        <h4 class="text-lg font-medium text-gray-900">{{ __('erp.assign_new_task') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('erp.create_and_assign_task') }}</p>
                    </div>
                </a>
                
                <a href="{{ route('modules.performance.reports.index') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-200">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="{{ app()->getLocale() === 'ar' ? 'mr-4' : 'ml-4' }}">
                        <h4 class="text-lg font-medium text-gray-900">{{ __('erp.generate_reports') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('erp.view_team_performance_reports') }}</p>
                    </div>
                </a>
                
                <a href="{{ route('modules.performance.analytics') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors duration-200">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="{{ app()->getLocale() === 'ar' ? 'mr-4' : 'ml-4' }}">
                        <h4 class="text-lg font-medium text-gray-900">{{ __('erp.view_analytics') }}</h4>
                        <p class="text-sm text-gray-600">{{ __('erp.detailed_performance_analytics') }}</p>
                    </div>
                </a>
            </div>
        </x-card>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize team performance chart
    const teamChartData = {
        labels: ['Jan 2024', 'Feb 2024', 'Mar 2024', 'Apr 2024', 'May 2024', 'Jun 2024'],
        datasets: [
            {
                label: '{{ __("erp.tasks_completed") }}',
                data: [45, 52, 48, 61, 58, 67],
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            },
            {
                label: '{{ __("erp.efficiency") }}',
                data: [82, 85, 88, 90, 87, 92],
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4
            }
        ]
    };

    initChart('teamPerformanceChart', 'line', teamChartData);

    // Initialize task distribution chart
    const distributionData = {
        labels: ['{{ __("erp.development") }}', '{{ __("erp.design") }}', '{{ __("erp.testing") }}', '{{ __("erp.documentation") }}'],
        datasets: [{
            data: [40, 25, 20, 15],
            backgroundColor: ['#3B82F6', '#8B5CF6', '#10B981', '#F59E0B'],
            borderWidth: 0
        }]
    };

    initChart('taskDistributionChart', 'doughnut', distributionData);
});
</script>
@endpush
@endsection
