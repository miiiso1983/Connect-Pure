@extends('layouts.app')

@section('title', $task->title)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <li>
                        <a href="{{ route('modules.performance.index') }}" class="text-gray-500 hover:text-gray-700">
                            {{ __('erp.performance') }}
                        </a>
                    </li>
                    <li>
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-400 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </li>
                    <li>
                        <a href="{{ route('modules.performance.tasks.index') }}" class="text-gray-500 hover:text-gray-700">
                            {{ __('erp.tasks') }}
                        </a>
                    </li>
                    <li>
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-400 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </li>
                    <li>
                        <span class="text-gray-900 font-medium">{{ Str::limit($task->title, 30) }}</span>
                    </li>
                </ol>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900 mt-2">{{ $task->title }}</h1>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.performance.tasks.edit', $task) }}" class="btn-secondary">
                {{ __('erp.edit_task') }}
            </a>
            <form method="POST" action="{{ route('modules.performance.tasks.destroy', $task) }}" class="inline"
                  onsubmit="return confirm('{{ __('erp.confirm_delete') }}')"
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">
                    {{ __('erp.delete_task') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Task Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Task Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Task Information -->
            <x-card title="{{ __('erp.task_information') }}">
                <div class="space-y-4">
                    <div class="flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-{{ $task->status_color }}-100 text-{{ $task->status_color }}-800">
                            {{ __('erp.' . $task->status) }}
                        </span>
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-{{ $task->priority_color }}-100 text-{{ $task->priority_color }}-800">
                            {{ __('erp.' . $task->priority) }} {{ __('erp.priority') }}
                        </span>
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-{{ $task->category_color }}-100 text-{{ $task->category_color }}-800">
                            {{ __('erp.' . $task->category) }}
                        </span>
                    </div>
                    
                    @if($task->description)
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-2">{{ __('erp.description') }}</h4>
                            <p class="text-gray-700 whitespace-pre-wrap">{{ $task->description }}</p>
                        </div>
                    @endif
                    
                    @if($task->notes)
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-2">{{ __('erp.notes') }}</h4>
                            <p class="text-gray-700 whitespace-pre-wrap">{{ $task->notes }}</p>
                        </div>
                    @endif
                    
                    @if($task->tags && count($task->tags) > 0)
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-2">{{ __('erp.tags') }}</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($task->tags as $tag)
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded">{{ $tag }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </x-card>

            <!-- Progress and Time Tracking -->
            @if($task->completion_percentage > 0 || $task->estimated_hours || $task->actual_hours)
                <x-card title="{{ __('erp.progress_and_time') }}">
                    <div class="space-y-4">
                        @if($task->completion_percentage > 0)
                            <div>
                                <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                                    <span>{{ __('erp.completion_percentage') }}</span>
                                    <span>{{ $task->completion_percentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="h-3 rounded-full transition-all duration-300 bg-blue-600" style="width: {{ $task->completion_percentage }}%"></div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @if($task->estimated_hours)
                                <div class="text-center p-4 bg-blue-50 rounded-lg">
                                    <p class="text-2xl font-bold text-blue-600">{{ $task->estimated_hours }}h</p>
                                    <p class="text-sm text-gray-600">{{ __('erp.estimated_hours') }}</p>
                                </div>
                            @endif
                            
                            @if($task->actual_hours)
                                <div class="text-center p-4 bg-green-50 rounded-lg">
                                    <p class="text-2xl font-bold text-green-600">{{ $task->actual_hours }}h</p>
                                    <p class="text-sm text-gray-600">{{ __('erp.actual_hours') }}</p>
                                </div>
                            @endif
                            
                            @if($task->efficiency_rate)
                                <div class="text-center p-4 bg-{{ $task->efficiency_rate >= 100 ? 'green' : ($task->efficiency_rate >= 80 ? 'yellow' : 'red') }}-50 rounded-lg">
                                    <p class="text-2xl font-bold {{ $task->efficiency_rate >= 100 ? 'text-green-600' : ($task->efficiency_rate >= 80 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ number_format($task->efficiency_rate, 1) }}%
                                    </p>
                                    <p class="text-sm text-gray-600">{{ __('erp.efficiency_rate') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </x-card>
            @endif

            <!-- Task Assignments -->
            @if($task->assignments && $task->assignments->count() > 0)
                <x-card title="{{ __('erp.assignments') }}">
                    <div class="space-y-4">
                        @foreach($task->assignments as $assignment)
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
                                        <h4 class="font-medium text-gray-900">{{ $assignment->employee_name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $assignment->employee_role }}</p>
                                        <div class="flex items-center space-x-2 mt-1 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ $assignment->status_color }}-100 text-{{ $assignment->status_color }}-800">
                                                {{ __('erp.' . $assignment->assignment_status) }}
                                            </span>
                                            @if($assignment->assigned_at)
                                                <span class="text-xs text-gray-500">
                                                    {{ __('erp.assigned') }} {{ $assignment->assigned_at->diffForHumans() }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                @if($assignment->duration)
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">{{ $assignment->duration }}</p>
                                        <p class="text-xs text-gray-500">{{ __('erp.duration') }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Task Details -->
            <x-card title="{{ __('erp.task_details') }}">
                <div class="space-y-4">
                    <div>
                        <p class="text-sm font-medium text-gray-600">{{ __('erp.created_by') }}</p>
                        <p class="text-sm text-gray-900">{{ $task->created_by }}</p>
                    </div>
                    
                    @if($task->project_name)
                        <div>
                            <p class="text-sm font-medium text-gray-600">{{ __('erp.project_name') }}</p>
                            <p class="text-sm text-gray-900">{{ $task->project_name }}</p>
                        </div>
                    @endif
                    
                    <div>
                        <p class="text-sm font-medium text-gray-600">{{ __('erp.created_at') }}</p>
                        <p class="text-sm text-gray-900">{{ $task->created_at->format('M j, Y \a\t g:i A') }}</p>
                    </div>
                    
                    @if($task->start_date)
                        <div>
                            <p class="text-sm font-medium text-gray-600">{{ __('erp.start_date') }}</p>
                            <p class="text-sm text-gray-900">{{ $task->start_date->format('M j, Y') }}</p>
                        </div>
                    @endif
                    
                    @if($task->due_date)
                        <div>
                            <p class="text-sm font-medium text-gray-600">{{ __('erp.due_date') }}</p>
                            <p class="text-sm {{ $task->is_overdue ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $task->due_date->format('M j, Y') }}
                            </p>
                            @if($task->days_remaining !== null)
                                <p class="text-xs {{ $task->days_remaining < 0 ? 'text-red-500' : 'text-gray-500' }}">
                                    @if($task->days_remaining < 0)
                                        {{ __('erp.overdue_by') }} {{ abs($task->days_remaining) }} {{ __('erp.days') }}
                                    @else
                                        {{ $task->days_remaining }} {{ __('erp.days_remaining') }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    @endif
                    
                    @if($task->completed_at)
                        <div>
                            <p class="text-sm font-medium text-gray-600">{{ __('erp.completed_at') }}</p>
                            <p class="text-sm text-green-600">{{ $task->completed_at->format('M j, Y \a\t g:i A') }}</p>
                        </div>
                    @endif
                </div>
            </x-card>

            <!-- Quick Actions -->
            <x-card title="{{ __('erp.quick_actions') }}">
                <div class="space-y-3">
                    <a href="{{ route('modules.performance.tasks.edit', $task) }}" class="w-full btn-secondary">
                        {{ __('erp.edit_task') }}
                    </a>
                    
                    @if($task->status !== 'completed')
                        <form method="POST" action="{{ route('modules.performance.tasks.update', $task) }}" class="w-full">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="completed">
                            <input type="hidden" name="completion_percentage" value="100">
                            <input type="hidden" name="title" value="{{ $task->title }}">
                            <input type="hidden" name="priority" value="{{ $task->priority }}">
                            <input type="hidden" name="category" value="{{ $task->category }}">
                            <button type="submit" class="w-full btn-success">
                                {{ __('erp.mark_as_completed') }}
                            </button>
                        </form>
                    @endif
                    
                    <button onclick="window.print()" class="w-full btn-secondary">
                        {{ __('erp.print_task') }}
                    </button>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
