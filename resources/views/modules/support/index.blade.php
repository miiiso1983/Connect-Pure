@extends('layouts.app')

@section('title', __('erp.support'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('erp.support') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('erp.support_description') }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.support.tickets.index') }}" class="btn-secondary">
                {{ __('erp.view') }} {{ __('erp.tickets') }}
            </a>
            <a href="{{ route('modules.support.tickets.create') }}" class="btn-primary">
                {{ __('erp.create_ticket') }}
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <x-stat-card
            title="{{ __('erp.open_tickets') }}"
            :value="$stats['open_tickets']"
            color="blue"
            :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('erp.urgent_tickets') }}"
            :value="$stats['urgent_tickets']"
            color="red"
            :icon="'<svg class=\'w-6 h-6 text-red-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('erp.resolved') }}"
            :value="$stats['resolved_tickets']"
            color="green"
            :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('erp.overdue_tickets') }}"
            :value="$stats['overdue_tickets']"
            color="orange"
            :icon="'<svg class=\'w-6 h-6 text-orange-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />
    </div>

    <!-- Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <x-card title="{{ __('erp.recent_tickets') }}">
                @if($tickets->count() > 0)
                    <div class="space-y-4">
                        @foreach($tickets->take(5) as $ticket)
                            <x-support.ticket-card :ticket="$ticket" :collapsible="true" />
                        @endforeach
                    </div>

                    @if($tickets->count() > 5)
                        <div class="mt-4 text-center">
                            <a href="{{ route('modules.support.tickets.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                {{ __('erp.view_all_tickets') }}
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                        </svg>
                        <p class="text-gray-500">{{ __('erp.no_tickets_found') }}</p>
                        <a href="{{ route('modules.support.tickets.create') }}" class="mt-2 inline-block text-blue-600 hover:text-blue-800">
                            {{ __('erp.create_first_ticket') }}
                        </a>
                    </div>
                @endif
            </x-card>
        </div>

        <div>
            <x-card title="Support Categories">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Technical Issues</span>
                        <span class="text-sm font-bold text-gray-900">45%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-600 h-2 rounded-full" style="width: 45%"></div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Billing</span>
                        <span class="text-sm font-bold text-gray-900">25%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-600 h-2 rounded-full" style="width: 25%"></div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Feature Requests</span>
                        <span class="text-sm font-bold text-gray-900">20%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: 20%"></div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">General Inquiry</span>
                        <span class="text-sm font-bold text-gray-900">10%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: 10%"></div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
