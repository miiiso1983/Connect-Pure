@props(['task', 'showAssignments' => true])

<div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-200">
    <!-- Task Header -->
    <div class="p-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $task->title }}
                    </h3>
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ $task->status_color }}-100 text-{{ $task->status_color }}-800">
                        {{ __('erp.' . $task->status) }}
                    </span>
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ $task->priority_color }}-100 text-{{ $task->priority_color }}-800">
                        {{ __('erp.' . $task->priority) }}
                    </span>
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ $task->category_color }}-100 text-{{ $task->category_color }}-800">
                        {{ __('erp.' . $task->category) }}
                    </span>
                </div>
                
                @if($task->project_name)
                    <p class="text-sm text-gray-600 mt-1">
                        <svg class="w-4 h-4 inline {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        {{ $task->project_name }}
                    </p>
                @endif
                
                @if($task->description)
                    <p class="text-sm text-gray-700 mt-2">{{ Str::limit($task->description, 100) }}</p>
                @endif
            </div>
            
            <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                @if($task->completion_percentage > 0)
                    <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <div class="w-16 bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-300 bg-blue-600" style="width: {{ $task->completion_percentage }}%"></div>
                        </div>
                        <span class="text-xs text-gray-600">{{ $task->completion_percentage }}%</span>
                    </div>
                @endif
                
                <a href="{{ route('modules.performance.tasks.show', $task) }}" class="text-blue-600 hover:text-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Task Details -->
    <div class="p-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            @if($task->due_date)
                <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <div>
                        <p class="text-gray-600">{{ __('erp.due_date') }}</p>
                        <p class="font-medium {{ $task->is_overdue ? 'text-red-600' : 'text-gray-900' }}">
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
                </div>
            @endif
            
            @if($task->estimated_hours)
                <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-gray-600">{{ __('erp.estimated_hours') }}</p>
                        <p class="font-medium text-gray-900">{{ $task->estimated_hours }}h</p>
                        @if($task->actual_hours)
                            <p class="text-xs text-gray-500">{{ __('erp.actual') }}: {{ $task->actual_hours }}h</p>
                        @endif
                    </div>
                </div>
            @endif
            
            @if($task->efficiency_rate)
                <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <div>
                        <p class="text-gray-600">{{ __('erp.efficiency') }}</p>
                        <p class="font-medium {{ $task->efficiency_rate >= 100 ? 'text-green-600' : ($task->efficiency_rate >= 80 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ number_format($task->efficiency_rate, 1) }}%
                        </p>
                    </div>
                </div>
            @endif
            
            <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <div>
                    <p class="text-gray-600">{{ __('erp.created_by') }}</p>
                    <p class="font-medium text-gray-900">{{ $task->created_by }}</p>
                    <p class="text-xs text-gray-500">{{ $task->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
        
        @if($task->tags && count($task->tags) > 0)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex flex-wrap gap-1">
                    @foreach($task->tags as $tag)
                        <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
        @endif
        
        @if($showAssignments && $task->assignments && $task->assignments->count() > 0)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <h5 class="text-sm font-medium text-gray-900 mb-2">{{ __('erp.assignments') }}</h5>
                <div class="space-y-2">
                    @foreach($task->assignments->take(3) as $assignment)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                <span class="font-medium text-gray-900">{{ $assignment->employee_name }}</span>
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $assignment->status_color }}-100 text-{{ $assignment->status_color }}-800">
                                    {{ __('erp.' . $assignment->assignment_status) }}
                                </span>
                            </div>
                            @if($assignment->assigned_at)
                                <span class="text-gray-500">{{ $assignment->assigned_at->diffForHumans() }}</span>
                            @endif
                        </div>
                    @endforeach
                    @if($task->assignments->count() > 3)
                        <p class="text-xs text-gray-500">{{ __('erp.and') }} {{ $task->assignments->count() - 3 }} {{ __('erp.more') }}</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
