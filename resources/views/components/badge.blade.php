@props([
    'variant' => 'default',
    'size' => 'md',
    'rounded' => true,
    'dot' => false,
    'icon' => null,
    'href' => null,
    'dismissible' => false
])

@php
    $variantClasses = [
        'default' => 'bg-gray-100 text-gray-800',
        'primary' => 'bg-blue-100 text-blue-800',
        'secondary' => 'bg-gray-100 text-gray-800',
        'success' => 'bg-green-100 text-green-800',
        'danger' => 'bg-red-100 text-red-800',
        'warning' => 'bg-yellow-100 text-yellow-800',
        'info' => 'bg-blue-100 text-blue-800',
        'light' => 'bg-gray-50 text-gray-600',
        'dark' => 'bg-gray-800 text-gray-100',
        
        // Solid variants
        'primary-solid' => 'bg-blue-600 text-white',
        'secondary-solid' => 'bg-gray-600 text-white',
        'success-solid' => 'bg-green-600 text-white',
        'danger-solid' => 'bg-red-600 text-white',
        'warning-solid' => 'bg-yellow-600 text-white',
        'info-solid' => 'bg-blue-600 text-white',
        
        // Outline variants
        'primary-outline' => 'border border-blue-200 text-blue-800 bg-transparent',
        'secondary-outline' => 'border border-gray-200 text-gray-800 bg-transparent',
        'success-outline' => 'border border-green-200 text-green-800 bg-transparent',
        'danger-outline' => 'border border-red-200 text-red-800 bg-transparent',
        'warning-outline' => 'border border-yellow-200 text-yellow-800 bg-transparent',
        'info-outline' => 'border border-blue-200 text-blue-800 bg-transparent',
    ];
    
    $sizeClasses = [
        'xs' => 'px-2 py-0.5 text-xs',
        'sm' => 'px-2.5 py-0.5 text-xs',
        'md' => 'px-2.5 py-0.5 text-sm',
        'lg' => 'px-3 py-1 text-sm',
        'xl' => 'px-3 py-1 text-base',
    ];
    
    $dotColors = [
        'default' => 'bg-gray-400',
        'primary' => 'bg-blue-400',
        'secondary' => 'bg-gray-400',
        'success' => 'bg-green-400',
        'danger' => 'bg-red-400',
        'warning' => 'bg-yellow-400',
        'info' => 'bg-blue-400',
        'light' => 'bg-gray-300',
        'dark' => 'bg-gray-600',
    ];
    
    $baseVariant = explode('-', $variant)[0];
    
    $classes = [
        'inline-flex',
        'items-center',
        'font-medium',
        $variantClasses[$variant] ?? 'bg-gray-100 text-gray-800',
        $sizeClasses[$size] ?? 'px-2.5 py-0.5 text-xs'
    ];
    
    if ($rounded) {
        $classes[] = 'rounded-full';
    } else {
        $classes[] = 'rounded';
    }
    
    if ($href) {
        $classes[] = 'hover:opacity-80 transition-opacity duration-200';
    }
    
    $component = $href ? 'a' : 'span';
@endphp

<{{ $component }} 
    @if($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => implode(' ', $classes)]) }}
    @if($dismissible) x-data="{ show: true }" x-show="show" @endif
>
    @if($dot)
        <span class="w-2 h-2 {{ $dotColors[$baseVariant] ?? $dotColors['default'] }} rounded-full mr-1.5"></span>
    @endif
    
    @if($icon)
        <span class="mr-1.5">
            {!! $icon !!}
        </span>
    @endif
    
    {{ $slot }}
    
    @if($dismissible)
        <button 
            @click="show = false"
            class="ml-1.5 inline-flex items-center justify-center w-4 h-4 text-current hover:bg-black hover:bg-opacity-10 rounded-full focus:outline-none focus:bg-black focus:bg-opacity-10"
        >
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    @endif
</{{ $component }}>

@if($dismissible)
    @once
        @push('scripts')
        <script>
        // Auto-dismiss badges after a certain time
        document.addEventListener('DOMContentLoaded', function() {
            const autoDismissBadges = document.querySelectorAll('[data-auto-dismiss]');
            autoDismissBadges.forEach(badge => {
                const delay = parseInt(badge.dataset.autoDismiss) || 5000;
                setTimeout(() => {
                    if (badge.querySelector('[x-data]')) {
                        badge.querySelector('[x-data]').__x.$data.show = false;
                    }
                }, delay);
            });
        });
        </script>
        @endpush
    @endonce
@endif
