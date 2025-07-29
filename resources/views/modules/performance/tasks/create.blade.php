@extends('layouts.app')

@section('title', __('erp.create_task'))

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
                        <span class="text-gray-900 font-medium">{{ __('erp.create_task') }}</span>
                    </li>
                </ol>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900 mt-2">{{ __('erp.create_task') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('erp.create_new_task_description') }}</p>
        </div>
        <a href="{{ route('modules.performance.tasks.index') }}" class="btn-secondary">
            {{ __('erp.back_to_tasks') }}
        </a>
    </div>

    <!-- Task Creation Form -->
    <form method="POST" action="{{ route('modules.performance.tasks.store') }}" class="space-y-6">
        @csrf
        
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
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
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
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Priority and Category -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('erp.priority') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="priority" id="priority" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('priority') border-red-300 @enderror">
                                    <option value="">{{ __('erp.select_priority') }}</option>
                                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>{{ __('erp.low') }}</option>
                                    <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>{{ __('erp.medium') }}</option>
                                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>{{ __('erp.high') }}</option>
                                    <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>{{ __('erp.urgent') }}</option>
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
                                    <option value="">{{ __('erp.select_category') }}</option>
                                    <option value="development" {{ old('category') === 'development' ? 'selected' : '' }}>{{ __('erp.development') }}</option>
                                    <option value="design" {{ old('category') === 'design' ? 'selected' : '' }}>{{ __('erp.design') }}</option>
                                    <option value="testing" {{ old('category') === 'testing' ? 'selected' : '' }}>{{ __('erp.testing') }}</option>
                                    <option value="documentation" {{ old('category') === 'documentation' ? 'selected' : '' }}>{{ __('erp.documentation') }}</option>
                                    <option value="meeting" {{ old('category') === 'meeting' ? 'selected' : '' }}>{{ __('erp.meeting') }}</option>
                                    <option value="research" {{ old('category') === 'research' ? 'selected' : '' }}>{{ __('erp.research') }}</option>
                                    <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>{{ __('erp.other') }}</option>
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
                            <input type="text" name="project_name" id="project_name" value="{{ old('project_name') }}"
                                   placeholder="{{ __('erp.enter_project_name') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('project_name') border-red-300 @enderror">
                            @error('project_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-card>

                <!-- Timeline and Estimation -->
                <x-card title="{{ __('erp.timeline_and_estimation') }}">
                    <div class="space-y-4">
                        <!-- Start and Due Dates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('erp.start_date') }}
                                </label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('start_date') border-red-300 @enderror">
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('erp.due_date') }}
                                </label>
                                <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('due_date') border-red-300 @enderror">
                                @error('due_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Estimated Hours -->
                        <div>
                            <label for="estimated_hours" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('erp.estimated_hours') }}
                            </label>
                            <input type="number" name="estimated_hours" id="estimated_hours" value="{{ old('estimated_hours') }}" 
                                   min="1" step="1" placeholder="{{ __('erp.enter_estimated_hours') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('estimated_hours') border-red-300 @enderror">
                            @error('estimated_hours')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                            <input type="text" name="tags" id="tags" value="{{ old('tags') }}"
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
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-300 @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Task Assignment -->
                <x-card title="{{ __('erp.task_assignment') }}">
                    <div class="space-y-4">
                        <div id="employee-assignments">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('erp.assign_to_employees') }}
                            </label>
                            <div class="space-y-2" id="assignment-list">
                                <div class="assignment-item">
                                    <input type="text" name="assigned_employees[]" 
                                           placeholder="{{ __('erp.employee_name') }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                            <button type="button" onclick="addAssignment()" class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                                + {{ __('erp.add_another_employee') }}
                            </button>
                        </div>
                    </div>
                </x-card>

                <!-- Quick Actions -->
                <x-card title="{{ __('erp.quick_actions') }}">
                    <div class="space-y-3">
                        <button type="submit" class="w-full btn-primary">
                            {{ __('erp.create_task') }}
                        </button>
                        
                        <button type="button" onclick="saveDraft()" class="w-full btn-secondary">
                            {{ __('erp.save_as_draft') }}
                        </button>
                        
                        <a href="{{ route('modules.performance.tasks.index') }}" class="w-full btn-secondary block text-center">
                            {{ __('erp.cancel') }}
                        </a>
                    </div>
                </x-card>

                <!-- Tips -->
                <x-card title="{{ __('erp.tips') }}">
                    <div class="space-y-3 text-sm text-gray-600">
                        <div class="flex items-start space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                            <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <p>{{ __('erp.task_title_tip') }}</p>
                        </div>
                        <div class="flex items-start space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                            <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <p>{{ __('erp.estimation_tip') }}</p>
                        </div>
                        <div class="flex items-start space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                            <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <p>{{ __('erp.assignment_tip') }}</p>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function addAssignment() {
    const assignmentList = document.getElementById('assignment-list');
    const newAssignment = document.createElement('div');
    newAssignment.className = 'assignment-item flex items-center space-x-2';
    newAssignment.innerHTML = `
        <input type="text" name="assigned_employees[]" 
               placeholder="{{ __('erp.employee_name') }}"
               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <button type="button" onclick="removeAssignment(this)" class="text-red-600 hover:text-red-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    assignmentList.appendChild(newAssignment);
}

function removeAssignment(button) {
    button.closest('.assignment-item').remove();
}

function saveDraft() {
    // In a real application, you would save the form data as a draft
    alert('{{ __("erp.draft_saved") }}');
}

// Set minimum date to today for start and due dates
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('start_date').setAttribute('min', today);
    document.getElementById('due_date').setAttribute('min', today);
    
    // Update due date minimum when start date changes
    document.getElementById('start_date').addEventListener('change', function() {
        document.getElementById('due_date').setAttribute('min', this.value);
    });
});
</script>
@endpush
@endsection
