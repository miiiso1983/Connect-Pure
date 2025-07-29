@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'image' => null,
    'badge' => null,
    'actions' => null,
    'footer' => null,
    'href' => null,
    'variant' => 'default',
    'size' => 'md',
    'hover' => true,
    'shadow' => 'md',
    'border' => true
])

@php
    $variantClasses = [
        'default' => 'bg-white',
        'primary' => 'bg-blue-50 border-blue-200',
        'success' => 'bg-green-50 border-green-200',
        'warning' => 'bg-yellow-50 border-yellow-200',
        'danger' => 'bg-red-50 border-red-200',
        'info' => 'bg-blue-50 border-blue-200',
        'dark' => 'bg-gray-800 text-white',
        'gradient' => 'bg-gradient-to-br from-blue-500 to-purple-600 text-white',
    ];
    
    $sizeClasses = [
        'xs' => 'p-3',
        'sm' => 'p-4',
        'md' => 'p-6',
        'lg' => 'p-8',
        'xl' => 'p-10',
    ];
    
    $shadowClasses = [
        'none' => '',
        'sm' => 'shadow-sm',
        'md' => 'shadow-md',
        'lg' => 'shadow-lg',
        'xl' => 'shadow-xl',
    ];
    
    $classes = [
        'rounded-lg',
        'overflow-hidden',
        'transition-all',
        'duration-200',
        $variantClasses[$variant] ?? 'bg-white',
        $sizeClasses[$size] ?? 'p-6',
        $shadowClasses[$shadow] ?? 'shadow-md',
    ];
    
    if ($border && $variant === 'default') {
        $classes[] = 'border border-gray-200';
    }
    
    if ($hover) {
        $classes[] = 'hover:shadow-lg';
        if ($href) {
            $classes[] = 'hover:scale-105 cursor-pointer';
        }
    }
    
    $component = $href ? 'a' : 'div';
@endphp

<{{ $component }} 
    @if($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => implode(' ', $classes)]) }}
>
    <!-- Header with Image/Icon -->
    @if($image || $icon || $badge)
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center">
                @if($image)
                    <div class="flex-shrink-0 mr-4">
                        <img class="h-12 w-12 rounded-lg object-cover" src="{{ $image }}" alt="">
                    </div>
                @elseif($icon)
                    <div class="flex-shrink-0 mr-4">
                        <div class="h-12 w-12 rounded-lg bg-blue-100 flex items-center justify-center">
                            {!! $icon !!}
                        </div>
                    </div>
                @endif
            </div>
            
            @if($badge)
                <div class="flex-shrink-0">
                    {!! $badge !!}
                </div>
            @endif
        </div>
    @endif
    
    <!-- Title and Subtitle -->
    @if($title || $subtitle)
        <div class="mb-4">
            @if($title)
                <h3 class="text-lg font-semibold {{ $variant === 'dark' || $variant === 'gradient' ? 'text-white' : 'text-gray-900' }} mb-1">
                    {{ $title }}
                </h3>
            @endif
            
            @if($subtitle)
                <p class="text-sm {{ $variant === 'dark' || $variant === 'gradient' ? 'text-gray-300' : 'text-gray-600' }}">
                    {{ $subtitle }}
                </p>
            @endif
        </div>
    @endif
    
    <!-- Main Content -->
    <div class="flex-1">
        {{ $slot }}
    </div>
    
    <!-- Actions -->
    @if($actions)
        <div class="mt-4 pt-4 border-t {{ $variant === 'dark' || $variant === 'gradient' ? 'border-gray-600' : 'border-gray-200' }}">
            <div class="flex items-center justify-end space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                {!! $actions !!}
            </div>
        </div>
    @endif
    
    <!-- Footer -->
    @if($footer)
        <div class="mt-4 pt-4 border-t {{ $variant === 'dark' || $variant === 'gradient' ? 'border-gray-600' : 'border-gray-200' }}">
            {!! $footer !!}
        </div>
    @endif
</{{ $component }}>

@push('styles')
<style>
.card-hover-effect {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.card-hover-effect:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.card-gradient-border {
    position: relative;
    background: linear-gradient(white, white) padding-box,
                linear-gradient(45deg, #3b82f6, #8b5cf6) border-box;
    border: 2px solid transparent;
}

.card-glass {
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
}

.card-neumorphism {
    background: #f0f0f0;
    box-shadow: 20px 20px 60px #bebebe, -20px -20px 60px #ffffff;
}

.card-floating {
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
    100% { transform: translateY(0px); }
}

.card-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .8; }
}

.card-slide-in {
    animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* RTL Support */
[dir="rtl"] .card-content {
    text-align: right;
}

[dir="rtl"] .card-actions {
    flex-direction: row-reverse;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .dark-mode .professional-card {
        background-color: #1f2937;
        border-color: #374151;
        color: #f9fafb;
    }
    
    .dark-mode .professional-card:hover {
        background-color: #111827;
    }
}

/* Mobile optimizations */
@media (max-width: 640px) {
    .professional-card {
        margin-bottom: 1rem;
    }
    
    .professional-card .card-actions {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .professional-card .card-actions > * {
        width: 100%;
        text-align: center;
    }
}

/* Print styles */
@media print {
    .professional-card {
        break-inside: avoid;
        box-shadow: none;
        border: 1px solid #000;
    }
    
    .professional-card .card-actions {
        display: none;
    }
}
</style>
@endpush
