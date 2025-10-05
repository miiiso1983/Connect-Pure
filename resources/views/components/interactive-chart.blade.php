@props([
    'type' => 'line',
    'data' => [],
    'options' => [],
    'height' => '400',
    'title' => null,
    'subtitle' => null,
    'loading' => false,
    'error' => null,
    'responsive' => true,
    // auto | light | dark
    'theme' => 'auto'
])

@php
    $chartId = 'chart-' . uniqid();
    $defaultOptions = [
        'responsive' => $responsive,
        'maintainAspectRatio' => false,
        'plugins' => [
            'legend' => [
                'position' => app()->getLocale() === 'ar' ? 'left' : 'right',
                'rtl' => app()->getLocale() === 'ar',
            ],
            'tooltip' => [
                'mode' => 'index',
                'intersect' => false,
                'rtl' => app()->getLocale() === 'ar',
            ]
        ],
        'scales' => [
            'x' => [
                'display' => true,
                'title' => [
                    'display' => false
                ]
            ],
            'y' => [
                'display' => true,
                'title' => [
                    'display' => false
                ]
            ]
        ]
    ];
    
    $mergedOptions = array_merge_recursive($defaultOptions, $options);
@endphp

<div {{ $attributes->merge(['class' => 'modern-card overflow-hidden']) }} style="--chart-height: {{ $height }}px">
    @if($title || $subtitle)
        <div class="px-6 py-4 border-b border-gray-100">
            @if($title)
                <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
            @endif
            @if($subtitle)
                <p class="text-sm text-gray-600 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    
    <div class="p-6">
        @if($loading)
            <div class="flex items-center justify-center" style="height: var(--chart-height)">
                <div class="text-center" style="height: var(--chart-height)">
                    <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-gray-600">{{ __('common.loading_chart') }}</p>
                </div>
            </div>
        @elseif($error)
            <div class="flex items-center justify-center" style="height: var(--chart-height)">
                <div class="text-center">
                    <svg class="h-12 w-12 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-red-600">{{ $error }}</p>
                </div>
            </div>
        @elseif(empty($data))
            <div class="flex items-center justify-center" style="height: var(--chart-height)">
                <div class="text-center">
                    <svg class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p class="text-gray-600">{{ __('common.no_chart_data') }}</p>
                </div>
            </div>
        @else
            <div class="relative">
                <canvas
                    id="{{ $chartId }}"
                    style="height: var(--chart-height)"
                    class="w-full"
                    data-chart-data="{{ base64_encode(json_encode($data)) }}"
                    data-chart-options="{{ base64_encode(json_encode($mergedOptions)) }}"
                    data-chart-type="{{ $type }}"
                    data-chart-theme="{{ $theme }}"
                ></canvas>
                
                <!-- Chart Actions -->
                <div class="absolute top-2 {{ app()->getLocale() === 'ar' ? 'left-2' : 'right-2' }}">
                    <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <button
                            onclick="downloadChart('{{ $chartId }}', 'png')"
                            class="chart-action p-2 rounded-md shadow-sm border"
                            title="{{ __('common.download_chart') }}"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </button>

                        <button
                            onclick="refreshChart('{{ $chartId }}')"
                            class="chart-action p-2 rounded-md shadow-sm border"
                            title="{{ __('common.refresh_chart') }}"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@if(!$loading && !$error && !empty($data))
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('{{ $chartId }}');
    const ctx = canvas.getContext('2d');

    // Get data from canvas data attributes
    const chartData = JSON.parse(atob(canvas.dataset.chartData));
    const chartOptions = JSON.parse(atob(canvas.dataset.chartOptions));
    const theme = canvas.dataset.chartTheme;

    // Apply theme colors
    const docTheme = document.documentElement.getAttribute('data-theme') || 'light';
    const isDark = (theme === 'dark') || (theme !== 'light' && docTheme === 'dark');

    // Apply theme to options
    if (isDark) {
        chartOptions.plugins.legend.labels = { color: '#E5E7EB' };
        chartOptions.scales.x.ticks = { color: '#E5E7EB' };
        chartOptions.scales.y.ticks = { color: '#E5E7EB' };
        chartOptions.scales.x.grid = { color: '#374151' };
        chartOptions.scales.y.grid = { color: '#374151' };
    }

    // Create chart
    window['chart_{{ $chartId }}'] = new Chart(ctx, {
        type: canvas.dataset.chartType,
        data: chartData,
        options: chartOptions
    });
});
</script>
@endif
<script>
// Chart utility functions
function downloadChart(chartId, format = 'png') {
    const chart = window['chart_' + chartId];
    if (chart) {
        const url = chart.toBase64Image();
        const link = document.createElement('a');
        link.download = 'chart.' + format;
        link.href = url;
        link.click();
    }
}

function refreshChart(chartId) {
    const chart = window['chart_' + chartId];
    if (chart) {
        // Add refresh logic here - typically would reload data
        chart.update();
    }
}

function updateChartData(chartId, newData) {
    const chart = window['chart_' + chartId];
    if (chart) {
        chart.data = newData;
        chart.update();
    }
}

function toggleChartType(chartId, newType) {
    const chart = window['chart_' + chartId];
    if (chart) {
        chart.config.type = newType;
        chart.update();
    }
}
</script>
@endpush
