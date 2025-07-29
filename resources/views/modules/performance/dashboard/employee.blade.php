@extends('layouts.app')

@section('title', __('erp.my_performance'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('erp.my_performance') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('erp.welcome') }}, {{ $employeeName }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.performance.tasks.create') }}" class="btn-primary">
                {{ __('erp.create_task') }}
            </a>
        </div>
    </div>

    <!-- Personal KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <x-performance.kpi-widget 
            title="{{ __('erp.my_tasks') }}"
            :value="$myStats['total_tasks']"
            color="blue"
            :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01\'></path></svg>'"
        />
        
        <x-performance.kpi-widget 
            title="{{ __('erp.active_tasks') }}"
            :value="$myStats['active_tasks']"
            color="yellow"
            :icon="'<svg class=\'w-6 h-6 text-yellow-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />
        
        <x-performance.kpi-widget 
            title="{{ __('erp.completed_tasks') }}"
            :value="$myStats['completed_tasks']"
            color="green"
            :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />
        
        <x-performance.kpi-widget 
            title="{{ __('erp.overdue_tasks') }}"
            :value="$myStats['overdue_tasks']"
            color="red"
            :icon="'<svg class=\'w-6 h-6 text-red-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z\'></path></svg>'"
        />
    </div>

    <!-- Performance Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-performance.kpi-widget 
            title="{{ __('erp.completion_rate') }}"
            :value="number_format($myStats['completion_rate'], 1) . '%'"
            subtitle="{{ __('erp.personal_completion_rate') }}"
            color="blue"
            :percentage="$myStats['completion_rate']"
            :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6\'></path></svg>'"
        />
        
        <x-performance.kpi-widget 
            title="{{ __('erp.efficiency_rate') }}"
            :value="number_format($myStats['efficiency_rate'], 1) . '%'"
            subtitle="{{ __('erp.time_management_efficiency') }}"
            color="green"
            :percentage="$myStats['efficiency_rate']"
            :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 10V3L4 14h7v7l9-11h-7z\'></path></svg>'"
        />
        
        <x-performance.kpi-widget 
            title="{{ __('erp.avg_task_duration') }}"
            :value="$myStats['avg_task_duration'] ?? __('erp.no_data')"
            subtitle="{{ __('erp.average_time_per_task') }}"
            color="purple"
            :icon="'<svg class=\'w-6 h-6 text-purple-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />
    </div>

    <!-- Performance Chart and Tasks -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Performance Trend Chart -->
        <x-performance.performance-chart 
            chartId="myPerformanceChart"
            title="{{ __('erp.my_performance_trend') }}"
            type="line"
            height="300"
        />
        
        <!-- My Active Tasks -->
        <x-card title="{{ __('erp.my_active_tasks') }}">
            @if($myTasks->where('status', '!=', 'completed')->count() > 0)
                <div class="space-y-4">
                    @foreach($myTasks->where('status', '!=', 'completed')->take(5) as $task)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">{{ $task->title }}</h4>
                                <div class="flex items-center space-x-4 mt-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ $task->status_color }}-100 text-{{ $task->status_color }}-800">
                                        {{ __('erp.' . $task->status) }}
                                    </span>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ $task->priority_color }}-100 text-{{ $task->priority_color }}-800">
                                        {{ __('erp.' . $task->priority) }}
                                    </span>
                                    @if($task->due_date)
                                        <span class="text-xs text-gray-500">
                                            {{ __('erp.due') }}: {{ $task->due_date->format('M j') }}
                                        </span>
                                    @endif
                                </div>
                                @if($task->completion_percentage > 0)
                                    <div class="mt-2">
                                        <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                                            <span>{{ __('erp.progress') }}</span>
                                            <span>{{ $task->completion_percentage }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full transition-all duration-300 bg-blue-600" style="width: {{ $task->completion_percentage }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                <a href="{{ route('modules.performance.tasks.show', $task) }}" class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($myTasks->where('status', '!=', 'completed')->count() > 5)
                    <div class="mt-4 text-center">
                        <a href="{{ route('modules.performance.tasks.index', ['employee' => $employeeName]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            {{ __('erp.view_all_my_tasks') }}
                        </a>
                    </div>
                @endif
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-500">{{ __('erp.no_active_tasks') }}</p>
                    <p class="text-sm text-gray-400 mt-1">{{ __('erp.all_tasks_completed') }}</p>
                </div>
            @endif
        </x-card>
    </div>

    <!-- Recent Completed Tasks -->
    <x-card title="{{ __('erp.recently_completed_tasks') }}">
        @if($myTasks->where('status', 'completed')->count() > 0)
            <div class="space-y-4">
                @foreach($myTasks->where('status', 'completed')->take(3) as $task)
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $task->title }}</h4>
                                <div class="flex items-center space-x-4 mt-1 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                    <span class="text-sm text-gray-600">{{ $task->project_name }}</span>
                                    @if($task->completed_at)
                                        <span class="text-sm text-gray-500">
                                            {{ __('erp.completed') }} {{ $task->completed_at->diffForHumans() }}
                                        </span>
                                    @endif
                                    @if($task->efficiency_rate)
                                        <span class="text-sm {{ $task->efficiency_rate >= 100 ? 'text-green-600' : 'text-yellow-600' }}">
                                            {{ number_format($task->efficiency_rate, 1) }}% {{ __('erp.efficiency') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('modules.performance.tasks.show', $task) }}" class="text-blue-600 hover:text-blue-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <p class="text-gray-500">{{ __('erp.no_completed_tasks_yet') }}</p>
            </div>
        @endif
    </x-card>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize performance chart
    const chartData = {
        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
        datasets: [
            {
                label: '{{ __("erp.tasks_completed") }}',
                data: [8, 12, 10, 15],
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            },
            {
                label: '{{ __("erp.hours_worked") }}',
                data: [32, 38, 35, 42],
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                yAxisID: 'y1'
            }
        ]
    };
    
    initChart('myPerformanceChart', 'line', chartData);
});
</script>
@endpush
@endsection
