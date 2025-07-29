@extends('layouts.app')

@section('title', __('accounting.recurring_payments'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('accounting.recurring_payments') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('accounting.manage_automated_recurring_transactions') }}</p>
        </div>
        <div class="mt-4 lg:mt-0 flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <button onclick="processDueProfiles()" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                {{ __('accounting.process_due') }}
            </button>
            <a href="{{ route('modules.accounting.recurring.create') }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ __('accounting.create_recurring_profile') }}
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('accounting.total_profiles') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($summary['total_profiles']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('accounting.active_profiles') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($summary['active_profiles']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('accounting.due_for_processing') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($summary['due_for_processing']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __('accounting.monthly_revenue') }}</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($summary['monthly_revenue'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
        <form method="GET" action="{{ route('modules.accounting.recurring.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('accounting.type') }}</label>
                <select name="type" class="form-select">
                    <option value="">{{ __('accounting.all_types') }}</option>
                    @foreach($types as $key => $type)
                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('accounting.status') }}</label>
                <select name="status" class="form-select">
                    <option value="">{{ __('accounting.all_statuses') }}</option>
                    @foreach($statuses as $key => $status)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('accounting.frequency') }}</label>
                <select name="frequency" class="form-select">
                    <option value="">{{ __('accounting.all_frequencies') }}</option>
                    @foreach($frequencies as $key => $frequency)
                        <option value="{{ $key }}" {{ request('frequency') == $key ? 'selected' : '' }}>
                            {{ $frequency }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('accounting.search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('accounting.search_profiles') }}" class="form-input">
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="btn-primary">
                    {{ __('accounting.filter') }}
                </button>
                <a href="{{ route('modules.accounting.recurring.index') }}" class="btn-secondary">
                    {{ __('accounting.clear') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Recurring Profiles Table -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('accounting.profile_name') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('accounting.type') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('accounting.frequency') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('accounting.amount') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('accounting.next_run') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('accounting.status') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('accounting.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($profiles as $profile)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <a href="{{ route('modules.accounting.recurring.show', $profile) }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $profile->profile_name }}
                                    </a>
                                </div>
                                <div class="text-sm text-gray-500">
                                    @if($profile->customer)
                                        {{ $profile->customer->name }}
                                    @elseif($profile->vendor)
                                        {{ $profile->vendor->name }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $profile->type === 'invoice' ? 'bg-blue-100 text-blue-800' : 
                                       ($profile->type === 'expense' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800') }}">
                                    {{ $types[$profile->type] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $profile->frequency_text }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $profile->formatted_amount }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $profile->next_run_date_formatted }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    bg-{{ $profile->status_color }}-100 text-{{ $profile->status_color }}-800">
                                    {{ $statuses[$profile->status] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                    @if($profile->status === 'active')
                                        <button onclick="pauseProfile({{ $profile->id }})" 
                                                class="text-yellow-600 hover:text-yellow-900">
                                            {{ __('accounting.pause') }}
                                        </button>
                                    @elseif($profile->status === 'paused')
                                        <button onclick="resumeProfile({{ $profile->id }})" 
                                                class="text-green-600 hover:text-green-900">
                                            {{ __('accounting.resume') }}
                                        </button>
                                    @endif
                                    
                                    @if($profile->shouldProcess())
                                        <button onclick="processNow({{ $profile->id }})" 
                                                class="text-blue-600 hover:text-blue-900">
                                            {{ __('accounting.process_now') }}
                                        </button>
                                    @endif
                                    
                                    <a href="{{ route('modules.accounting.recurring.show', $profile) }}" 
                                       class="text-gray-600 hover:text-gray-900">
                                        {{ __('accounting.view') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                    <p class="text-lg font-medium">{{ __('accounting.no_recurring_profiles_found') }}</p>
                                    <p class="mt-1">{{ __('accounting.create_your_first_recurring_profile') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($profiles->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $profiles->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function pauseProfile(profileId) {
    if (confirm('{{ __("accounting.confirm_pause_profile") }}')) {
        fetch(`/modules/accounting/recurring/${profileId}/pause`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || '{{ __("accounting.error_occurred") }}');
            }
        });
    }
}

function resumeProfile(profileId) {
    fetch(`/modules/accounting/recurring/${profileId}/resume`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || '{{ __("accounting.error_occurred") }}');
        }
    });
}

function processNow(profileId) {
    if (confirm('{{ __("accounting.confirm_process_now") }}')) {
        fetch(`/modules/accounting/recurring/${profileId}/process-now`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message || '{{ __("accounting.error_occurred") }}');
            }
        });
    }
}

function processDueProfiles() {
    if (confirm('{{ __("accounting.confirm_process_due_profiles") }}')) {
        fetch('/modules/accounting/recurring/process-due', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message || '{{ __("accounting.error_occurred") }}');
            }
        });
    }
}
</script>
@endpush
@endsection
