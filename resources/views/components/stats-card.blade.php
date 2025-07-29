@props([
    'title',
    'value',
    'subtitle' => null,
    'icon' => null,
    'color' => 'blue',
    'trend' => null,
    'trendDirection' => null,
    'href' => null,
    'loading' => false
])

@php
    $colorClasses = [
        'blue' => 'bg-blue-100 text-blue-600',
        'green' => 'bg-green-100 text-green-600',
        'red' => 'bg-red-100 text-red-600',
        'yellow' => 'bg-yellow-100 text-yellow-600',
        'purple' => 'bg-purple-100 text-purple-600',
        'indigo' => 'bg-indigo-100 text-indigo-600',
        'pink' => 'bg-pink-100 text-pink-600',
        'gray' => 'bg-gray-100 text-gray-600',
    ];
    
    $iconColorClass = $colorClasses[$color] ?? 'bg-blue-100 text-blue-600';
    
    $trendColorClass = match($trendDirection) {
        'up' => 'text-green-600',
        'down' => 'text-red-600',
        default => 'text-gray-500'
    };
    
    $component = $href ? 'a' : 'div';
@endphp

<{{ $component }} 
    @if($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-md border border-gray-200 p-6 transition-all duration-200 hover:shadow-lg' . ($href ? ' hover:bg-gray-50' : '')]) }}
>
    <div class="flex items-center">
        @if($icon)
            <div class="flex-shrink-0">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $iconColorClass }}">
                    @if(is_string($icon))
                        {!! $icon !!}
                    @else
                        {{ $icon }}
                    @endif
                </div>
            </div>
        @endif
        
        <div class="{{ $icon ? 'ml-4' : '' }} flex-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ $title }}</p>
                    
                    @if($loading)
                        <div class="animate-pulse">
                            <div class="h-8 bg-gray-200 rounded w-20 mt-1"></div>
                        </div>
                    @else
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $value }}</p>
                    @endif
                    
                    @if($subtitle)
                        <p class="text-sm text-gray-500 mt-1">{{ $subtitle }}</p>
                    @endif
                </div>
                
                @if($trend && !$loading)
                    <div class="text-right">
                        <div class="flex items-center {{ $trendColorClass }}">
                            @if($trendDirection === 'up')
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            @elseif($trendDirection === 'down')
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                </svg>
                            @endif
                            <span class="text-sm font-medium">{{ $trend }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</{{ $component }}>
