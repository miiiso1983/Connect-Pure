@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => false,
    'icon' => true,
    'border' => false,
    'compact' => false
])

@php
    $typeConfig = [
        'success' => [
            'bg' => 'bg-green-50',
            'border' => 'border-green-200',
            'text' => 'text-green-800',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'iconBg' => 'bg-green-100',
            'iconColor' => 'text-green-600'
        ],
        'error' => [
            'bg' => 'bg-red-50',
            'border' => 'border-red-200',
            'text' => 'text-red-800',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'iconBg' => 'bg-red-100',
            'iconColor' => 'text-red-600'
        ],
        'warning' => [
            'bg' => 'bg-yellow-50',
            'border' => 'border-yellow-200',
            'text' => 'text-yellow-800',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>',
            'iconBg' => 'bg-yellow-100',
            'iconColor' => 'text-yellow-600'
        ],
        'info' => [
            'bg' => 'bg-blue-50',
            'border' => 'border-blue-200',
            'text' => 'text-blue-800',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'iconBg' => 'bg-blue-100',
            'iconColor' => 'text-blue-600'
        ]
    ];
    
    $config = $typeConfig[$type] ?? [
        'bg' => 'bg-blue-50',
        'border' => 'border-blue-200',
        'text' => 'text-blue-800',
        'icon' => 'text-blue-400',
        'button' => 'text-blue-500 hover:bg-blue-100'
    ];
    
    $classes = [
        'rounded-lg',
        'p-4',
        $config['bg'],
        $config['text']
    ];
    
    if ($border) {
        $classes[] = 'border';
        $classes[] = $config['border'];
    }
    
    if ($compact) {
        $classes = array_diff($classes, ['p-4']);
        $classes[] = 'p-3';
    }
@endphp

<div {{ $attributes->merge(['class' => implode(' ', $classes)]) }} @if($dismissible) x-data="{ show: true }" x-show="show" x-transition @endif>
    <div class="flex">
        @if($icon)
            <div class="flex-shrink-0">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $config['iconBg'] }} {{ $config['iconColor'] }}">
                    {!! $config['icon'] !!}
                </div>
            </div>
        @endif
        
        <div class="{{ $icon ? 'ml-3' : '' }} flex-1">
            @if($title)
                <h3 class="text-sm font-medium {{ $config['text'] }} mb-1">
                    {{ $title }}
                </h3>
            @endif
            
            <div class="text-sm {{ $config['text'] }} {{ $title ? '' : 'font-medium' }}">
                {{ $slot }}
            </div>
        </div>
        
        @if($dismissible)
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button 
                        @click="show = false"
                        class="inline-flex rounded-md p-1.5 {{ $config['text'] }} hover:bg-opacity-20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-{{ $type }}-50 focus:ring-{{ $type }}-600"
                    >
                        <span class="sr-only">{{ __('common.dismiss') }}</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
