@extends('layouts.app')

@section('title', __('erp.crm'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('erp.crm') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('erp.crm_description') }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.crm.contacts.create') }}" class="btn-primary">
                {{ __('erp.add_contact') }}
            </a>
            <a href="{{ route('modules.crm.contacts.index') }}" class="btn-secondary">
                {{ __('erp.view') }} {{ __('erp.contacts') }}
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <x-stat-card
            title="{{ __('erp.total_contacts') }}"
            value="{{ $stats['total_contacts'] }}"
            color="blue"
            :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('erp.total_leads') }}"
            value="{{ $stats['total_leads'] }}"
            color="yellow"
            :icon="'<svg class=\'w-6 h-6 text-yellow-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('erp.total_clients') }}"
            value="{{ $stats['total_clients'] }}"
            color="green"
            :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('erp.deals_closed') }}"
            value="{{ $stats['deals_closed'] }}"
            color="purple"
            :icon="'<svg class=\'w-6 h-6 text-purple-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />
    </div>

    <!-- Sales Funnel -->
    <x-card title="{{ __('erp.sales_funnel') }}">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
            <x-crm.funnel-stage
                stage="new"
                :count="$funnelData['new']"
                color="gray"
            />
            <x-crm.funnel-stage
                stage="contacted"
                :count="$funnelData['contacted']"
                color="blue"
            />
            <x-crm.funnel-stage
                stage="qualified"
                :count="$funnelData['qualified']"
                color="yellow"
            />
            <x-crm.funnel-stage
                stage="proposal"
                :count="$funnelData['proposal']"
                color="purple"
            />
            <x-crm.funnel-stage
                stage="negotiation"
                :count="$funnelData['negotiation']"
                color="orange"
            />
            <x-crm.funnel-stage
                stage="closed_won"
                :count="$funnelData['closed_won']"
                color="green"
            />
            <x-crm.funnel-stage
                stage="closed_lost"
                :count="$funnelData['closed_lost']"
                color="red"
            />
        </div>
    </x-card>

    <!-- Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-card title="{{ __('erp.recent_activities') }}">
            @if($recentContacts->count())
                <div class="space-y-4">
                    @foreach($recentContacts as $contact)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $contact->name }}</h4>
                                @if($contact->company)
                                    <p class="text-sm text-gray-600">{{ $contact->company }}</p>
                                @endif
                                @if($contact->email)
                                    <p class="text-sm text-gray-600">{{ $contact->email }}</p>
                                @endif
                            </div>
                            <div class="text-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}">
                                <span class="px-2 py-1 text-xs font-medium bg-{{ $contact->type_color }}-100 text-{{ $contact->type_color }}-800 rounded-full">
                                    {{ __('erp.' . $contact->type) }}
                                </span>
                                <div class="mt-1">
                                    <span class="px-2 py-1 text-xs font-medium bg-{{ $contact->status_color }}-100 text-{{ $contact->status_color }}-800 rounded-full">
                                        {{ __('erp.' . $contact->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <p>{{ __('erp.no_data') }}</p>
                </div>
            @endif
        </x-card>

        <x-card title="{{ __('erp.follow_up_reminders') }}">
            @if($stats['pending_follow_ups'] > 0)
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <div>
                            <h4 class="font-medium text-yellow-800">{{ __('erp.pending_follow_ups') }}</h4>
                            <p class="text-sm text-yellow-600">{{ $stats['pending_follow_ups'] }} {{ __('erp.follow_ups') }}</p>
                        </div>
                        <a href="{{ route('modules.crm.follow-ups.index') }}" class="text-yellow-600 hover:text-yellow-800">
                            {{ __('erp.view') }} {{ __('erp.details') }}
                        </a>
                    </div>

                    @if($stats['overdue_follow_ups'] > 0)
                        <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border border-red-200">
                            <div>
                                <h4 class="font-medium text-red-800">{{ __('erp.overdue_follow_ups') }}</h4>
                                <p class="text-sm text-red-600">{{ $stats['overdue_follow_ups'] }} {{ __('erp.overdue') }}</p>
                            </div>
                            <a href="{{ route('modules.crm.follow-ups.index') }}" class="text-red-600 hover:text-red-800">
                                {{ __('erp.view') }} {{ __('erp.details') }}
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>{{ __('erp.no_data') }}</p>
                </div>
            @endif
        </x-card>
    </div>
</div>
@endsection
