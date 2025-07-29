@extends('layouts.app')

@section('title', __('erp.edit_task'))

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
                        <a href="{{ route('modules.performance.tasks.show', $task) }}" class="text-gray-500 hover:text-gray-700">
                            {{ Str::limit($task->title, 20) }}
                        </a>
                    </li>
                    <li>
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-400 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </li>
                    <li>
                        <span class="text-gray-900 font-medium">{{ __('erp.edit') }}</span>
                    </li>
                </ol>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900 mt-2">{{ __('erp.edit_task') }}</h1>
            <p class="text-gray-600 mt-1">{{ $task->title }}</p>
        </div>
        <a href="{{ route('modules.performance.tasks.show', $task) }}" class="btn-secondary">
            {{ __('erp.back_to_task') }}
        </a>
    </div>

    <!-- Task Edit Form -->
    <form method="POST" action="{{ route('modules.performance.tasks.update', $task) }}" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Task Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <x-card title="{{ __('erp.basic_information') }}">
                    <div class="space-y-4">
                        <!-- Task Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('erp.task_title') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title', $task->title) }}" required
                                   placeholder="{{ __('erp.enter_task_title') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('title') border-red-300 @enderror">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Task Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('erp.task_description') }}
                            </label>
                            <textarea name="description" id="description" rows="4" 
                                      placeholder="{{ __('erp.enter_task_description') }}"
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-300 @enderror">{{ old('description', $task->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status, Priority and Category -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('erp.status') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="status" id="status" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-300 @enderror">
                                    <option value="pending" {{ old('status', $task->status) === 'pending' ? 'selected' : '' }}>{{ __('erp.pending') }}</option>
                                    <option value="in_progress" {{ old('status', $task->status) === 'in_progress' ? 'selected' : '' }}>{{ __('erp.in_progress') }}</option>
                                    <option value="completed" {{ old('status', $task->status) === 'completed' ? 'selected' : '' }}>{{ __('erp.completed') }}</option>
                                    <option value="cancelled" {{ old('status', $task->status) === 'cancelled' ? 'selected' : '' }}>{{ __('erp.cancelled') }}</option>
                                    <option value="on_hold" {{ old('status', $task->status) === 'on_hold' ? 'selected' : '' }}>{{ __('erp.on_hold') }}</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('erp.priority') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="priority" id="priority" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('priority') border-red-300 @enderror">
                                    <option value="low" {{ old('priority', $task->priority) === 'low' ? 'selected' : '' }}>{{ __('erp.low') }}</option>
                                    <option value="medium" {{ old('priority', $task->priority) === 'medium' ? 'selected' : '' }}>{{ __('erp.medium') }}</option>
                                    <option value="high" {{ old('priority', $task->priority) === 'high' ? 'selected' : '' }}>{{ __('erp.high') }}</option>
                                    <option value="urgent" {{ old('priority', $task->priority) === 'urgent' ? 'selected' : '' }}>{{ __('erp.urgent') }}</option>
                                </select>
                                @error('priority')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('erp.category') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="category" id="category" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('category') border-red-300 @enderror">
                                    <option value="development" {{ old('category', $task->category) === 'development' ? 'selected' : '' }}>{{ __('erp.development') }}</option>
                                    <option value="design" {{ old('category', $task->category) === 'design' ? 'selected' : '' }}>{{ __('erp.design') }}</option>
                                    <option value="testing" {{ old('category', $task->category) === 'testing' ? 'selected' : '' }}>{{ __('erp.testing') }}</option>
                                    <option value="documentation" {{ old('category', $task->category) === 'documentation' ? 'selected' : '' }}>{{ __('erp.documentation') }}</option>
                                    <option value="meeting" {{ old('category', $task->category) === 'meeting' ? 'selected' : '' }}>{{ __('erp.meeting') }}</option>
                                    <option value="research" {{ old('category', $task->category) === 'research' ? 'selected' : '' }}>{{ __('erp.research') }}</option>
                                    <option value="other" {{ old('category', $task->category) === 'other' ? 'selected' : '' }}>{{ __('erp.other') }}</option>
                                </select>
                                @error('category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Project Name -->
                        <div>
                            <label for="project_name" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('erp.project_name') }}
                            </label>
                            <input type="text" name="project_name" id="project_name" value="{{ old('project_name', $task->project_name) }}"
                                   placeholder="{{ __('erp.enter_project_name') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('project_name') border-red-300 @enderror">
                            @error('project_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-card>

                <!-- Timeline and Progress -->
                <x-card title="{{ __('erp.timeline_and_progress') }}">
                    <div class="space-y-4">
                        <!-- Start and Due Dates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('erp.start_date') }}
                                </label>
                                <input type="date" name="start_date" id="start_date" 
                                       value="{{ old('start_date', $task->start_date ? $task->start_date->format('Y-m-d') : '') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('start_date') border-red-300 @enderror">
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('erp.due_date') }}
                                </label>
                                <input type="date" name="due_date" id="due_date" 
                                       value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('due_date') border-red-300 @enderror">
                                @error('due_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Hours and Progress -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="estimated_hours" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('erp.estimated_hours') }}
                                </label>
                                <input type="number" name="estimated_hours" id="estimated_hours" 
                                       value="{{ old('estimated_hours', $task->estimated_hours) }}" 
                                       min="1" step="1" placeholder="{{ __('erp.enter_estimated_hours') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('estimated_hours') border-red-300 @enderror">
                                @error('estimated_hours')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="actual_hours" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('erp.actual_hours') }}
                                </label>
                                <input type="number" name="actual_hours" id="actual_hours" 
                                       value="{{ old('actual_hours', $task->actual_hours) }}" 
                                       min="0" step="1" placeholder="{{ __('erp.enter_actual_hours') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('actual_hours') border-red-300 @enderror">
                                @error('actual_hours')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="completion_percentage" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('erp.completion_percentage') }}
                                </label>
                                <input type="number" name="completion_percentage" id="completion_percentage" 
                                       value="{{ old('completion_percentage', $task->completion_percentage) }}" 
                                       min="0" max="100" step="1" placeholder="0-100"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('completion_percentage') border-red-300 @enderror">
                                @error('completion_percentage')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </x-card>

                <!-- Additional Information -->
                <x-card title="{{ __('erp.additional_information') }}">
                    <div class="space-y-4">
                        <!-- Tags -->
                        <div>
                            <label for="tags" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('erp.tags') }}
                            </label>
                            <input type="text" name="tags" id="tags" 
                                   value="{{ old('tags', $task->tags ? implode(', ', $task->tags) : '') }}"
                                   placeholder="{{ __('erp.enter_tags_comma_separated') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('tags') border-red-300 @enderror">
                            <p class="mt-1 text-sm text-gray-500">{{ __('erp.separate_tags_with_commas') }}</p>
                            @error('tags')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('erp.notes') }}
                            </label>
                            <textarea name="notes" id="notes" rows="3" 
                                      placeholder="{{ __('erp.enter_additional_notes') }}"
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-300 @enderror">{{ old('notes', $task->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Current Status -->
                <x-card title="{{ __('erp.current_status') }}">
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-{{ $task->status_color }}-100 text-{{ $task->status_color }}-800">
                                {{ __('erp.' . $task->status) }}
                            </span>
                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-{{ $task->priority_color }}-100 text-{{ $task->priority_color }}-800">
                                {{ __('erp.' . $task->priority) }}
                            </span>
                        </div>
                        
                        @if($task->completion_percentage > 0)
                            <div>
                                <div class="flex items-center justify-between text-sm text-gray-600 mb-1">
                                    <span>{{ __('erp.progress') }}</span>
                                    <span>{{ $task->completion_percentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full bg-blue-600" style="width: {{ $task->completion_percentage }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </x-card>

                <!-- Quick Actions -->
                <x-card title="{{ __('erp.actions') }}">
                    <div class="space-y-3">
                        <button type="submit" class="w-full btn-primary">
                            {{ __('erp.update_task') }}
                        </button>
                        
                        <a href="{{ route('modules.performance.tasks.show', $task) }}" class="w-full btn-secondary block text-center">
                            {{ __('erp.cancel') }}
                        </a>
                        
                        <form method="POST" action="{{ route('modules.performance.tasks.destroy', $task) }}" class="w-full" 
                              onsubmit="return confirm('{{ __('erp.confirm_delete') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full btn-danger">
                                {{ __('erp.delete_task') }}
                            </button>
                        </form>
                    </div>
                </x-card>

                <!-- Task History -->
                <x-card title="{{ __('erp.task_history') }}">
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="font-medium text-gray-900">{{ __('erp.created') }}</p>
                            <p class="text-gray-600">{{ $task->created_at->format('M j, Y \a\t g:i A') }}</p>
                            <p class="text-gray-500">{{ __('erp.by') }} {{ $task->created_by }}</p>
                        </div>
                        
                        @if($task->updated_at != $task->created_at)
                            <div>
                                <p class="font-medium text-gray-900">{{ __('erp.last_updated') }}</p>
                                <p class="text-gray-600">{{ $task->updated_at->format('M j, Y \a\t g:i A') }}</p>
                                <p class="text-gray-500">{{ $task->updated_at->diffForHumans() }}</p>
                            </div>
                        @endif
                        
                        @if($task->completed_at)
                            <div>
                                <p class="font-medium text-green-900">{{ __('erp.completed') }}</p>
                                <p class="text-green-600">{{ $task->completed_at->format('M j, Y \a\t g:i A') }}</p>
                                <p class="text-green-500">{{ $task->completed_at->diffForHumans() }}</p>
                            </div>
                        @endif
                    </div>
                </x-card>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Auto-complete task when status is set to completed
document.getElementById('status').addEventListener('change', function() {
    const completionField = document.getElementById('completion_percentage');
    if (this.value === 'completed' && completionField.value < 100) {
        completionField.value = 100;
    }
});

// Auto-set completion percentage when status changes
document.getElementById('completion_percentage').addEventListener('change', function() {
    const statusField = document.getElementById('status');
    if (this.value == 100 && statusField.value !== 'completed') {
        if (confirm('{{ __("erp.mark_task_completed_question") }}')) {
            statusField.value = 'completed';
        }
    }
});

// Update due date minimum when start date changes
document.getElementById('start_date').addEventListener('change', function() {
    document.getElementById('due_date').setAttribute('min', this.value);
});
</script>
@endpush
@endsection
