@extends('layouts.app')

@section('title', __('erp.tickets'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('erp.tickets') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('erp.manage_support_tickets') }}</p>
        </div>
        <a href="{{ route('modules.support.tickets.create') }}" class="btn-primary">
            {{ __('erp.create_ticket') }}
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4">
        <form method="GET" action="{{ route('modules.support.tickets.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">{{ __('erp.search') }}</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="{{ __('erp.search_tickets') }}">
            </div>
            
            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">{{ __('erp.status') }}</label>
                <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">{{ __('erp.all_statuses') }}</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>{{ __('erp.open') }}</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>{{ __('erp.in_progress') }}</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('erp.pending') }}</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>{{ __('erp.resolved') }}</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>{{ __('erp.closed') }}</option>
                </select>
            </div>
            
            <!-- Priority Filter -->
            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">{{ __('erp.priority') }}</label>
                <select id="priority" name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">{{ __('erp.all_priorities') }}</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>{{ __('erp.low') }}</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>{{ __('erp.medium') }}</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>{{ __('erp.high') }}</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>{{ __('erp.urgent') }}</option>
                </select>
            </div>
            
            <!-- Category Filter -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">{{ __('erp.category') }}</label>
                <select id="category" name="category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">{{ __('erp.all_categories') }}</option>
                    <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>{{ __('erp.technical') }}</option>
                    <option value="billing" {{ request('category') === 'billing' ? 'selected' : '' }}>{{ __('erp.billing') }}</option>
                    <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>{{ __('erp.general') }}</option>
                    <option value="feature_request" {{ request('category') === 'feature_request' ? 'selected' : '' }}>{{ __('erp.feature_request') }}</option>
                    <option value="bug_report" {{ request('category') === 'bug_report' ? 'selected' : '' }}>{{ __('erp.bug_report') }}</option>
                </select>
            </div>
            
            <!-- Actions -->
            <div class="flex items-end space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                <button type="submit" class="btn-primary">
                    {{ __('erp.filter') }}
                </button>
                <a href="{{ route('modules.support.tickets.index') }}" class="btn-secondary">
                    {{ __('erp.clear') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Tickets List -->
    @if($tickets->count() > 0)
        <div class="space-y-4">
            @foreach($tickets as $ticket)
                <x-support.ticket-card :ticket="$ticket" :collapsible="true" />
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-6">
            {{ $tickets->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-8">
            <div class="text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('erp.no_tickets_found') }}</h3>
                <p class="text-gray-600 mb-4">
                    @if(request()->hasAny(['search', 'status', 'priority', 'category']))
                        {{ __('erp.no_tickets_match_filters') }}
                    @else
                        {{ __('erp.no_tickets_created_yet') }}
                    @endif
                </p>
                <div class="flex justify-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    @if(request()->hasAny(['search', 'status', 'priority', 'category']))
                        <a href="{{ route('modules.support.tickets.index') }}" class="btn-secondary">
                            {{ __('erp.clear_filters') }}
                        </a>
                    @endif
                    <a href="{{ route('modules.support.tickets.create') }}" class="btn-primary">
                        {{ __('erp.create_first_ticket') }}
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
