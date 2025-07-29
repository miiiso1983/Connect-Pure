@props(['chartId', 'title', 'type' => 'line', 'data' => [], 'height' => '400'])

<div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
        
        <!-- Chart Controls -->
        <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <select class="text-sm border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    onchange="updateChartPeriod('{{ $chartId }}', this.value)">
                <option value="week">{{ __('erp.this_week') }}</option>
                <option value="month" selected>{{ __('erp.this_month') }}</option>
                <option value="quarter">{{ __('erp.this_quarter') }}</option>
                <option value="year">{{ __('erp.this_year') }}</option>
            </select>
            
            <button onclick="refreshChart('{{ $chartId }}')" 
                    class="text-gray-500 hover:text-gray-700 p-1 rounded">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </button>
        </div>
    </div>
    
    <!-- Chart Container -->
    <div class="relative">
        <canvas id="{{ $chartId }}" style="height: {{ $height }}px"></canvas>
        
        <!-- Loading Overlay -->
        <div id="{{ $chartId }}-loading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center hidden">
            <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-blue-600">{{ __('erp.loading') }}...</span>
            </div>
        </div>
        
        <!-- No Data Message -->
        <div id="{{ $chartId }}-no-data" class="absolute inset-0 flex items-center justify-center hidden">
            <div class="text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <p class="text-gray-500">{{ __('erp.no_data_available') }}</p>
            </div>
        </div>
    </div>
    
    <!-- Chart Legend (if needed) -->
    @if(isset($showLegend) && $showLegend)
        <div id="{{ $chartId }}-legend" class="mt-4 flex flex-wrap justify-center gap-4"></div>
    @endif
</div>

@once
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart instances storage
window.performanceCharts = window.performanceCharts || {};

// Chart configuration
const chartConfigs = {
    line: {
        type: 'line',
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                x: {
                    display: true,
                    grid: {
                        display: false
                    }
                },
                y: {
                    display: true,
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    },
    bar: {
        type: 'bar',
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            },
            scales: {
                x: {
                    display: true,
                    grid: {
                        display: false
                    }
                },
                y: {
                    display: true,
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            }
        }
    },
    doughnut: {
        type: 'doughnut',
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                }
            }
        }
    }
};

// Initialize chart
function initChart(chartId, type, data) {
    const ctx = document.getElementById(chartId);
    if (!ctx) return;
    
    const config = { ...chartConfigs[type] };
    config.data = data;
    
    // Destroy existing chart if it exists
    if (window.performanceCharts[chartId]) {
        window.performanceCharts[chartId].destroy();
    }
    
    window.performanceCharts[chartId] = new Chart(ctx, config);
}

// Update chart data
function updateChart(chartId, newData) {
    const chart = window.performanceCharts[chartId];
    if (chart) {
        chart.data = newData;
        chart.update();
    }
}

// Show loading state
function showChartLoading(chartId) {
    document.getElementById(chartId + '-loading').classList.remove('hidden');
    document.getElementById(chartId + '-no-data').classList.add('hidden');
}

// Hide loading state
function hideChartLoading(chartId) {
    document.getElementById(chartId + '-loading').classList.add('hidden');
}

// Show no data message
function showNoData(chartId) {
    document.getElementById(chartId + '-no-data').classList.remove('hidden');
    hideChartLoading(chartId);
}

// Update chart period
function updateChartPeriod(chartId, period) {
    showChartLoading(chartId);
    
    // In a real application, you would make an AJAX request here
    // For now, we'll just simulate a delay
    setTimeout(() => {
        hideChartLoading(chartId);
        console.log('Updated chart period for', chartId, 'to', period);
    }, 1000);
}

// Refresh chart
function refreshChart(chartId) {
    showChartLoading(chartId);
    
    // In a real application, you would fetch fresh data here
    setTimeout(() => {
        hideChartLoading(chartId);
        console.log('Refreshed chart', chartId);
    }, 500);
}

// Color schemes
const colorSchemes = {
    primary: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
    success: ['#10B981', '#059669', '#047857', '#065F46'],
    warning: ['#F59E0B', '#D97706', '#B45309', '#92400E'],
    danger: ['#EF4444', '#DC2626', '#B91C1C', '#991B1B']
};

// Get colors for chart
function getChartColors(scheme = 'primary', count = 5) {
    const colors = colorSchemes[scheme] || colorSchemes.primary;
    return colors.slice(0, count);
}
</script>
@endpush
@endonce
