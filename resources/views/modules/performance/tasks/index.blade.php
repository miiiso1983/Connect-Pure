@extends('layouts.app')

@section('title', __('erp.tasks'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('erp.task_management') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('erp.manage_and_track_tasks') }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.performance.dashboard') }}" class="btn-secondary">
                {{ __('erp.dashboard') }}
            </a>
            <a href="{{ route('modules.performance.tasks.create') }}" class="btn-primary">
                {{ __('erp.create_task') }}
            </a>
        </div>
    </div>

    <!-- Filters -->
    <x-card title="{{ __('erp.filters') }}">
        <form method="GET" action="{{ route('modules.performance.tasks.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">{{ __('erp.search') }}</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="{{ __('erp.search_tasks') }}" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">{{ __('erp.status') }}</label>
                <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">{{ __('erp.all_statuses') }}</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('erp.pending') }}</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>{{ __('erp.in_progress') }}</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('erp.completed') }}</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('erp.cancelled') }}</option>
                    <option value="on_hold" {{ request('status') === 'on_hold' ? 'selected' : '' }}>{{ __('erp.on_hold') }}</option>
                </select>
            </div>
            
            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">{{ __('erp.priority') }}</label>
                <select name="priority" id="priority" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">{{ __('erp.all_priorities') }}</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>{{ __('erp.low') }}</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>{{ __('erp.medium') }}</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>{{ __('erp.high') }}</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>{{ __('erp.urgent') }}</option>
                </select>
            </div>
            
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">{{ __('erp.category') }}</label>
                <select name="category" id="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">{{ __('erp.all_categories') }}</option>
                    <option value="development" {{ request('category') === 'development' ? 'selected' : '' }}>{{ __('erp.development') }}</option>
                    <option value="design" {{ request('category') === 'design' ? 'selected' : '' }}>{{ __('erp.design') }}</option>
                    <option value="testing" {{ request('category') === 'testing' ? 'selected' : '' }}>{{ __('erp.testing') }}</option>
                    <option value="documentation" {{ request('category') === 'documentation' ? 'selected' : '' }}>{{ __('erp.documentation') }}</option>
                    <option value="meeting" {{ request('category') === 'meeting' ? 'selected' : '' }}>{{ __('erp.meeting') }}</option>
                    <option value="research" {{ request('category') === 'research' ? 'selected' : '' }}>{{ __('erp.research') }}</option>
                    <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>{{ __('erp.other') }}</option>
                </select>
            </div>
            
            <div>
                <label for="employee" class="block text-sm font-medium text-gray-700 mb-1">{{ __('erp.employee') }}</label>
                <input type="text" name="employee" id="employee" value="{{ request('employee') }}" 
                       placeholder="{{ __('erp.employee_name') }}" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="btn-primary w-full">
                    {{ __('erp.filter') }}
                </button>
            </div>
        </form>
    </x-card>

    <!-- Tasks List -->
    @if($tasks->count() > 0)
        <div class="space-y-6">
            @foreach($tasks as $task)
                <x-performance.task-card :task="$task" />
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-6">
            {{ $tasks->withQueryString()->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('erp.no_tasks_found') }}</h3>
                <p class="text-gray-600 mb-4">{{ __('erp.no_tasks_match_criteria') }}</p>
                <div class="flex justify-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <a href="{{ route('modules.performance.tasks.index') }}" class="btn-secondary">
                        {{ __('erp.clear_filters') }}
                    </a>
                    <a href="{{ route('modules.performance.tasks.create') }}" class="btn-primary">
                        {{ __('erp.create_first_task') }}
                    </a>
                </div>
            </div>
        </x-card>
    @endif
</div>
@endsection
