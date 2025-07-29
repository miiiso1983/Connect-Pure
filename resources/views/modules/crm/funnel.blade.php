@extends('layouts.app')

@section('title', __('erp.sales_funnel'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('erp.sales_funnel') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('erp.funnel_overview') }}</p>
        </div>
        <a href="{{ route('modules.crm.index') }}" class="btn-secondary">
            {{ __('erp.back') }} {{ __('erp.crm_short') }}
        </a>
    </div>

    <!-- Funnel Visualization -->
    <x-card title="{{ __('erp.sales_funnel') }}">
        <div class="space-y-6">
            <!-- Funnel Stages -->
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
                <x-crm.funnel-stage 
                    stage="new" 
                    :count="$funnelData['new']" 
                    color="gray"
                    :percentage="$funnelData['new'] > 0 ? 100 : 0"
                />
                <x-crm.funnel-stage 
                    stage="contacted" 
                    :count="$funnelData['contacted']" 
                    color="blue"
                    :percentage="$conversionRates['contacted_rate'] ?? 0"
                />
                <x-crm.funnel-stage 
                    stage="qualified" 
                    :count="$funnelData['qualified']" 
                    color="yellow"
                    :percentage="$conversionRates['qualified_rate'] ?? 0"
                />
                <x-crm.funnel-stage 
                    stage="proposal" 
                    :count="$funnelData['proposal']" 
                    color="purple"
                    :percentage="$conversionRates['proposal_rate'] ?? 0"
                />
                <x-crm.funnel-stage 
                    stage="negotiation" 
                    :count="$funnelData['negotiation']" 
                    color="orange"
                    :percentage="$conversionRates['negotiation_rate'] ?? 0"
                />
                <x-crm.funnel-stage 
                    stage="closed_won" 
                    :count="$funnelData['closed_won']" 
                    color="green"
                    :percentage="$conversionRates['won_rate'] ?? 0"
                />
                <x-crm.funnel-stage 
                    stage="closed_lost" 
                    :count="$funnelData['closed_lost']" 
                    color="red"
                />
            </div>

            <!-- Conversion Flow Visualization -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('erp.conversion_rate') }} {{ __('erp.flow') }}</h3>
                <div class="space-y-4">
                    @php
                        $stages = [
                            ['from' => 'new', 'to' => 'contacted', 'rate' => $conversionRates['contacted_rate'] ?? 0],
                            ['from' => 'contacted', 'to' => 'qualified', 'rate' => $conversionRates['qualified_rate'] ?? 0],
                            ['from' => 'qualified', 'to' => 'proposal', 'rate' => $conversionRates['proposal_rate'] ?? 0],
                            ['from' => 'proposal', 'to' => 'negotiation', 'rate' => $conversionRates['negotiation_rate'] ?? 0],
                            ['from' => 'negotiation', 'to' => 'closed_won', 'rate' => $conversionRates['won_rate'] ?? 0],
                        ];
                    @endphp

                    @foreach($stages as $stage)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                <span class="text-sm font-medium text-gray-700">{{ __('erp.' . $stage['from']) }}</span>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ app()->getLocale() === 'ar' ? 'M15 19l-7-7 7-7' : 'M9 5l7 7-7 7' }}"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">{{ __('erp.' . $stage['to']) }}</span>
                            </div>
                            <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                <div class="w-32 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300 progress-bar" data-width="{{ $stage['rate'] }}"></div>
                                </div>
                                <span class="text-sm font-bold text-gray-900">{{ $stage['rate'] }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </x-card>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-card title="{{ __('erp.total_value') }}">
            <div class="text-center">
                @php
                    $totalPipelineValue = collect($funnelData)->except(['closed_won', 'closed_lost'])->sum() * 10000; // Estimated
                    $totalClosedValue = $funnelData['closed_won'] * 15000; // Estimated
                @endphp
                <div class="text-3xl font-bold text-green-600">${{ number_format($totalClosedValue, 0) }}</div>
                <div class="text-sm text-gray-600">{{ __('erp.closed_won') }}</div>
                <div class="mt-2 text-lg font-semibold text-blue-600">${{ number_format($totalPipelineValue, 0) }}</div>
                <div class="text-sm text-gray-600">{{ __('erp.pipeline_value') }}</div>
            </div>
        </x-card>

        <x-card title="{{ __('erp.average_deal_size') }}">
            <div class="text-center">
                @php
                    $avgDealSize = $funnelData['closed_won'] > 0 ? $totalClosedValue / $funnelData['closed_won'] : 0;
                @endphp
                <div class="text-3xl font-bold text-purple-600">${{ number_format($avgDealSize, 0) }}</div>
                <div class="text-sm text-gray-600">{{ __('erp.per_deal') }}</div>
            </div>
        </x-card>

        <x-card title="{{ __('erp.overall_conversion') }}">
            <div class="text-center">
                @php
                    $totalLeads = array_sum($funnelData);
                    $overallConversion = $totalLeads > 0 ? round(($funnelData['closed_won'] / $totalLeads) * 100, 1) : 0;
                @endphp
                <div class="text-3xl font-bold text-indigo-600">{{ $overallConversion }}%</div>
                <div class="text-sm text-gray-600">{{ __('erp.lead_to_client') }}</div>
            </div>
        </x-card>
    </div>
</div>

@push('styles')
<style>
.progress-bar {
    width: 0%;
    transition: width 0.8s ease-in-out;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate progress bars
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(function(bar) {
        const width = bar.getAttribute('data-width');
        setTimeout(function() {
            bar.style.width = width + '%';
        }, 100);
    });
});
</script>
@endpush
@endsection
