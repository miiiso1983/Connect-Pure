@props(['size' => 'md', 'variant' => 'button'])

@php
    $sizeClasses = [
        'sm' => 'w-8 h-8',
        'md' => 'w-10 h-10',
        'lg' => 'w-12 h-12'
    ];
    
    $iconSizes = [
        'sm' => 'w-4 h-4',
        'md' => 'w-5 h-5',
        'lg' => 'w-6 h-6'
    ];
    
    $buttonClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $iconClass = $iconSizes[$size] ?? $iconSizes['md'];
@endphp

@if($variant === 'button')
    <!-- Modern Theme Toggle Button -->
    <button type="button" 
            data-theme-toggle
            class="{{ $buttonClass }} modern-card hover:scale-110 transition-all duration-300 flex items-center justify-center group relative overflow-hidden"
            aria-label="Toggle theme"
            title="Toggle between light and dark mode">
        
        <!-- Background Animation -->
        <div class="absolute inset-0 bg-gradient-to-r from-yellow-400 to-orange-500 opacity-0 group-hover:opacity-20 transition-opacity duration-300 rounded-xl"></div>
        
        <!-- Light Mode Icon (Sun) -->
        <svg class="theme-icon-light {{ $iconClass }} text-yellow-500 transition-all duration-300 absolute" 
             fill="none" 
             stroke="currentColor" 
             viewBox="0 0 24 24">
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
            </path>
        </svg>
        
        <!-- Dark Mode Icon (Moon) -->
        <svg class="theme-icon-dark {{ $iconClass }} text-blue-600 transition-all duration-300 absolute hidden" 
             fill="none" 
             stroke="currentColor" 
             viewBox="0 0 24 24">
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
            </path>
        </svg>
    </button>

@elseif($variant === 'switch')
    <!-- Toggle Switch Style -->
    <div class="flex items-center space-x-3">
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Light</span>
        <button type="button" 
                data-theme-toggle
                class="relative inline-flex h-6 w-11 items-center rounded-full bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-gray-700"
                role="switch"
                aria-checked="false"
                aria-label="Toggle theme">
            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform dark:translate-x-6 dark:bg-gray-300"></span>
        </button>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Dark</span>
    </div>

@elseif($variant === 'dropdown-item')
    <!-- Dropdown Menu Item -->
    <button type="button" 
            data-theme-toggle
            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors duration-200">
        
        <!-- Light Mode Icon -->
        <svg class="theme-icon-light {{ $iconClass }} mr-3 text-yellow-500" 
             fill="none" 
             stroke="currentColor" 
             viewBox="0 0 24 24">
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
            </path>
        </svg>
        
        <!-- Dark Mode Icon -->
        <svg class="theme-icon-dark {{ $iconClass }} mr-3 text-blue-600 hidden" 
             fill="none" 
             stroke="currentColor" 
             viewBox="0 0 24 24">
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
            </path>
        </svg>
        
        <div>
            <div class="font-medium">Switch Theme</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Toggle between light and dark mode</div>
        </div>
    </button>

@elseif($variant === 'floating')
    <!-- Floating Action Button -->
    <div class="fixed bottom-6 right-6 z-50">
        <button type="button" 
                data-theme-toggle
                class="w-14 h-14 modern-card hover:scale-110 transition-all duration-300 flex items-center justify-center group shadow-2xl"
                aria-label="Toggle theme"
                title="Toggle between light and dark mode">
            
            <!-- Animated Background -->
            <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-purple-600 opacity-0 group-hover:opacity-20 transition-opacity duration-300 rounded-2xl"></div>
            
            <!-- Light Mode Icon -->
            <svg class="theme-icon-light w-6 h-6 text-yellow-500 transition-all duration-300 absolute" 
                 fill="none" 
                 stroke="currentColor" 
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" 
                      stroke-linejoin="round" 
                      stroke-width="2" 
                      d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                </path>
            </svg>
            
            <!-- Dark Mode Icon -->
            <svg class="theme-icon-dark w-6 h-6 text-blue-600 transition-all duration-300 absolute hidden" 
                 fill="none" 
                 stroke="currentColor" 
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" 
                      stroke-linejoin="round" 
                      stroke-width="2" 
                      d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                </path>
            </svg>
        </button>
    </div>
@endif

<style>
/* Theme-specific styles */
[data-theme="dark"] .theme-icon-light {
    display: none;
}

[data-theme="dark"] .theme-icon-dark {
    display: block !important;
}

[data-theme="light"] .theme-icon-light {
    display: block;
}

[data-theme="light"] .theme-icon-dark {
    display: none !important;
}

/* Smooth transitions for theme changes */
* {
    transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
}

/* Keyboard focus styles */
[data-theme-toggle]:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* Animation for icon rotation */
[data-theme-toggle]:hover .theme-icon-light,
[data-theme-toggle]:hover .theme-icon-dark {
    transform: rotate(180deg);
}
</style>
