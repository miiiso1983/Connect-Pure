@extends('layouts.app')

@section('title', __('erp.follow_up_reminders'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('erp.follow_up_reminders') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('erp.follow_ups') }} {{ __('erp.dashboard') }}</p>
        </div>
        <a href="{{ route('modules.crm.index') }}" class="btn-secondary">
            {{ __('erp.back') }} {{ __('erp.crm_short') }}
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-stat-card 
            title="{{ __('erp.overdue_follow_ups') }}"
            value="{{ $overdueFollowUps->count() }}"
            color="red"
            :icon="'<svg class=\'w-6 h-6 text-red-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />
        
        <x-stat-card 
            title="{{ __('erp.today') }}"
            value="{{ $todayFollowUps->count() }}"
            color="yellow"
            :icon="'<svg class=\'w-6 h-6 text-yellow-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />
        
        <x-stat-card 
            title="{{ __('erp.upcoming') }}"
            value="{{ $upcomingFollowUps->count() }}"
            color="blue"
            :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg>'"
        />
    </div>

    <!-- Overdue Follow-ups -->
    @if($overdueFollowUps->count())
        <x-card title="{{ __('erp.overdue_follow_ups') }}" color="red-50">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($overdueFollowUps as $followUp)
                    <x-crm.follow-up-card :followUp="$followUp" />
                @endforeach
            </div>
        </x-card>
    @endif

    <!-- Today's Follow-ups -->
    @if($todayFollowUps->count())
        <x-card title="{{ __('erp.today') }} - {{ __('erp.follow_ups') }}" color="yellow-50">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($todayFollowUps as $followUp)
                    <x-crm.follow-up-card :followUp="$followUp" />
                @endforeach
            </div>
        </x-card>
    @endif

    <!-- Upcoming Follow-ups -->
    @if($upcomingFollowUps->count())
        <x-card title="{{ __('erp.upcoming') }} {{ __('erp.follow_ups') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($upcomingFollowUps as $followUp)
                    <x-crm.follow-up-card :followUp="$followUp" />
                @endforeach
            </div>
        </x-card>
    @endif

    <!-- No Follow-ups -->
    @if($overdueFollowUps->count() === 0 && $todayFollowUps->count() === 0 && $upcomingFollowUps->count() === 0)
        <x-card>
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('erp.no_data') }}</h3>
                <p class="text-gray-600 mb-4">{{ __('erp.no_follow_ups_scheduled') }}</p>
                <a href="{{ route('modules.crm.contacts.index') }}" class="btn-primary">
                    {{ __('erp.view') }} {{ __('erp.contacts') }}
                </a>
            </div>
        </x-card>
    @endif
</div>
@endsection
