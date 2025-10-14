@extends('layouts.app')

@section('title', __('erp.dashboard'))

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="modern-card overflow-hidden">
        <div class="p-8 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
            <h1 class="text-3xl font-bold mb-2">{{ __('erp.welcome') }}</h1>
            <p class="text-blue-100 text-lg">{{ __('erp.welcome_message') }}</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-stat-card
            title="{{ __('erp.active') }} {{ __('erp.crm_short') }}"
            value="1,234"
            color="blue"
            trend="12"
            :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('erp.support') }} {{ __('erp.pending') }}"
            value="56"
            color="green"
            trend="-8"
            :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('erp.accounting') }} {{ __('erp.status') }}"
            value="$45,678"
            color="yellow"
            trend="23"
            :icon="'<svg class=\'w-6 h-6 text-yellow-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('erp.hr') }} {{ __('erp.active') }}"
            value="89"
            color="pink"
            trend="5"
            :icon="'<svg class=\'w-6 h-6 text-pink-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z\'></path></svg>'"
        />
    </div>

    <!-- Performance Chart -->
    <x-interactive-chart
        type="line"
        :height="320"
        title="{{ __('erp.performance_overview') }}"
        subtitle="{{ __('erp.last_12_months') }}"
        :data="[
            'labels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            'datasets' => [
                [
                    'label' => __('erp.revenue'),
                    'data' => [1200, 1800, 1600, 2200, 2600, 2800, 3000, 3200, 3100, 3500, 3700, 4000],
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59,130,246,0.2)',
                    'tension' => 0.35,
                    'fill' => true,
                ],
                [
                    'label' => __('erp.expenses'),
                    'data' => [800, 900, 1100, 1200, 1400, 1500, 1600, 1700, 1650, 1800, 1900, 2000],
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239,68,68,0.15)',
                    'tension' => 0.35,
                    'fill' => true,
                ],
            ],
        ]"
        :options="[
            'scales' => [
                'y' => [ 'beginAtZero' => true ],
            ],
        ]"
    />


    <!-- Modules Grid -->
    <div>
        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('erp.modules') }}</h2>
        <div class="grid [grid-template-columns:repeat(auto-fit,minmax(260px,1fr))] gap-6">
            <x-module-card
                title="{{ __('erp.crm_short') }}"
                description="{{ __('erp.crm_description') }}"
                route="{{ route('modules.crm.index') }}"
                color="blue"
                :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z\'></path></svg>'"
            />

            <x-module-card
                title="{{ __('erp.support') }}"
                description="{{ __('erp.support_description') }}"
                route="{{ route('modules.support.index') }}"
                color="green"
                :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z\'></path></svg>'"
            />

            <x-module-card
                title="{{ __('erp.accounting') }}"
                description="{{ __('erp.accounting_description') }}"
                route="{{ route('modules.accounting.index') }}"
                color="yellow"
                :icon="'<svg class=\'w-6 h-6 text-yellow-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
            />

            <x-module-card
                title="{{ __('erp.performance') }}"
                description="{{ __('erp.performance_description') }}"
                route="{{ route('modules.performance.index') }}"
                color="purple"
                :icon="'<svg class=\'w-6 h-6 text-purple-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z\'></path></svg>'"
            />



            <x-module-card
                title="{{ __('erp.hr') }}"
                description="{{ __('erp.hr_description') }}"
                route="{{ route('modules.hr.dashboard') }}"
                color="pink"
                :icon="'<svg class=\'w-6 h-6 text-pink-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z\'></path></svg>'"
            />

            <x-module-card
                title="{{ __('erp.roles') }}"
                description="{{ __('erp.roles_description') }}"
                route="{{ route('modules.roles.index') }}"
                color="red"
                :icon="'<svg class=\'w-6 h-6 text-red-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z\'></path></svg>'"
            />
        </div>
    </div>
</div>
@endsection
