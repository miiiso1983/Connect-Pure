/**
 * Chart.js Utility Functions for Connect Pure ERP
 * Provides reusable chart configurations and helper functions
 */

// Global chart instances storage
window.chartInstances = window.chartInstances || {};

// Default color schemes
const colorSchemes = {
    primary: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4', '#84CC16'],
    success: ['#10B981', '#059669', '#047857', '#065F46', '#064E3B'],
    warning: ['#F59E0B', '#D97706', '#B45309', '#92400E', '#78350F'],
    danger: ['#EF4444', '#DC2626', '#B91C1C', '#991B1B', '#7F1D1D'],
    info: ['#3B82F6', '#2563EB', '#1D4ED8', '#1E40AF', '#1E3A8A'],
    purple: ['#8B5CF6', '#7C3AED', '#6D28D9', '#5B21B6', '#4C1D95']
};

// Chart.js default configuration
Chart.defaults.font.family = "'Inter', 'system-ui', 'sans-serif'";
Chart.defaults.font.size = 12;
Chart.defaults.color = '#6B7280';
Chart.defaults.borderColor = '#E5E7EB';
Chart.defaults.backgroundColor = '#F9FAFB';

// Default chart options
const defaultOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'top',
            labels: {
                usePointStyle: true,
                padding: 20,
                font: {
                    size: 12,
                    weight: '500'
                }
            }
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#FFFFFF',
            bodyColor: '#FFFFFF',
            borderColor: '#374151',
            borderWidth: 1,
            cornerRadius: 8,
            displayColors: true,
            mode: 'index',
            intersect: false
        }
    },
    interaction: {
        mode: 'nearest',
        axis: 'x',
        intersect: false
    }
};

// Line chart specific options
const lineChartOptions = {
    ...defaultOptions,
    scales: {
        x: {
            display: true,
            grid: {
                display: false
            },
            ticks: {
                font: {
                    size: 11
                }
            }
        },
        y: {
            display: true,
            beginAtZero: true,
            grid: {
                color: 'rgba(0, 0, 0, 0.05)'
            },
            ticks: {
                font: {
                    size: 11
                }
            }
        }
    },
    elements: {
        line: {
            tension: 0.4
        },
        point: {
            radius: 4,
            hoverRadius: 6
        }
    }
};

// Bar chart specific options
const barChartOptions = {
    ...defaultOptions,
    scales: {
        x: {
            display: true,
            grid: {
                display: false
            },
            ticks: {
                font: {
                    size: 11
                }
            }
        },
        y: {
            display: true,
            beginAtZero: true,
            grid: {
                color: 'rgba(0, 0, 0, 0.05)'
            },
            ticks: {
                font: {
                    size: 11
                }
            }
        }
    },
    elements: {
        bar: {
            borderRadius: 4,
            borderSkipped: false
        }
    }
};

// Doughnut chart specific options
const doughnutChartOptions = {
    ...defaultOptions,
    cutout: '60%',
    plugins: {
        ...defaultOptions.plugins,
        legend: {
            ...defaultOptions.plugins.legend,
            position: 'bottom'
        }
    }
};

/**
 * Initialize a chart with the given configuration
 * @param {string} canvasId - The ID of the canvas element
 * @param {string} type - Chart type (line, bar, doughnut, etc.)
 * @param {Object} data - Chart data
 * @param {Object} customOptions - Custom options to override defaults
 * @returns {Chart} Chart instance
 */
function initChart(canvasId, type, data, customOptions = {}) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) {
        console.error(`Canvas with ID '${canvasId}' not found`);
        return null;
    }

    // Destroy existing chart if it exists
    if (window.chartInstances[canvasId]) {
        window.chartInstances[canvasId].destroy();
    }

    // Get default options based on chart type
    let options;
    switch (type) {
        case 'line':
            options = { ...lineChartOptions };
            break;
        case 'bar':
            options = { ...barChartOptions };
            break;
        case 'doughnut':
        case 'pie':
            options = { ...doughnutChartOptions };
            break;
        default:
            options = { ...defaultOptions };
    }

    // Merge custom options
    options = deepMerge(options, customOptions);

    // Create chart
    const chart = new Chart(canvas, {
        type: type,
        data: data,
        options: options
    });

    // Store chart instance
    window.chartInstances[canvasId] = chart;

    return chart;
}

/**
 * Update chart data
 * @param {string} canvasId - The ID of the canvas element
 * @param {Object} newData - New chart data
 */
function updateChart(canvasId, newData) {
    const chart = window.chartInstances[canvasId];
    if (chart) {
        chart.data = newData;
        chart.update('active');
    }
}

/**
 * Get colors from a color scheme
 * @param {string} scheme - Color scheme name
 * @param {number} count - Number of colors needed
 * @returns {Array} Array of colors
 */
function getColors(scheme = 'primary', count = 5) {
    const colors = colorSchemes[scheme] || colorSchemes.primary;
    const result = [];
    
    for (let i = 0; i < count; i++) {
        result.push(colors[i % colors.length]);
    }
    
    return result;
}

/**
 * Generate gradient colors for charts
 * @param {CanvasRenderingContext2D} ctx - Canvas context
 * @param {string} color - Base color
 * @param {number} alpha - Alpha transparency
 * @returns {CanvasGradient} Gradient
 */
function createGradient(ctx, color, alpha = 0.2) {
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, color);
    gradient.addColorStop(1, `${color}${Math.round(alpha * 255).toString(16).padStart(2, '0')}`);
    return gradient;
}

/**
 * Format numbers for chart display
 * @param {number} value - Number to format
 * @param {string} type - Format type (currency, percentage, number)
 * @returns {string} Formatted string
 */
function formatChartValue(value, type = 'number') {
    switch (type) {
        case 'currency':
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            }).format(value);
        case 'percentage':
            return `${value}%`;
        case 'number':
        default:
            return new Intl.NumberFormat().format(value);
    }
}

/**
 * Deep merge objects
 * @param {Object} target - Target object
 * @param {Object} source - Source object
 * @returns {Object} Merged object
 */
function deepMerge(target, source) {
    const result = { ...target };
    
    for (const key in source) {
        if (source[key] && typeof source[key] === 'object' && !Array.isArray(source[key])) {
            result[key] = deepMerge(result[key] || {}, source[key]);
        } else {
            result[key] = source[key];
        }
    }
    
    return result;
}

/**
 * Show loading state for chart
 * @param {string} canvasId - Canvas ID
 */
function showChartLoading(canvasId) {
    const loadingElement = document.getElementById(`${canvasId}-loading`);
    if (loadingElement) {
        loadingElement.classList.remove('hidden');
    }
}

/**
 * Hide loading state for chart
 * @param {string} canvasId - Canvas ID
 */
function hideChartLoading(canvasId) {
    const loadingElement = document.getElementById(`${canvasId}-loading`);
    if (loadingElement) {
        loadingElement.classList.add('hidden');
    }
}

/**
 * Export chart as image
 * @param {string} canvasId - Canvas ID
 * @param {string} filename - Export filename
 */
function exportChart(canvasId, filename = 'chart') {
    const chart = window.chartInstances[canvasId];
    if (chart) {
        const url = chart.toBase64Image();
        const link = document.createElement('a');
        link.download = `${filename}.png`;
        link.href = url;
        link.click();
    }
}

// Export functions for global use
window.initChart = initChart;
window.updateChart = updateChart;
window.getColors = getColors;
window.createGradient = createGradient;
window.formatChartValue = formatChartValue;
window.showChartLoading = showChartLoading;
window.hideChartLoading = hideChartLoading;
window.exportChart = exportChart;
