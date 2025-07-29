@props(['title' => null, 'subtitle' => null, 'icon' => null, 'gradient' => false, 'glass' => false, 'color' => 'white', 'padding' => null])

@php
    $cardClasses = 'modern-card overflow-hidden transition-all duration-300 hover:shadow-2xl';
    if ($glass) {
        $cardClasses = 'glass-card overflow-hidden transition-all duration-300 hover:shadow-2xl';
    }
    $paddingClass = $padding ?? 'p-0';
@endphp

<div {{ $attributes->merge(['class' => $cardClasses . ' ' . $paddingClass]) }}>
    @if($title || $subtitle || $icon)
        <div class="px-8 py-6 {{ $gradient ? 'bg-gradient-to-r from-blue-50 via-purple-50 to-pink-50' : 'bg-gradient-to-r from-gray-50 to-gray-100' }} border-b border-gray-200/50">
            <div class="flex items-center">
                @if($icon)
                    <div class="flex-shrink-0 mr-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            {!! $icon !!}
                        </div>
                    </div>
                @endif
                <div class="flex-1">
                    @if($title)
                        <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $title }}</h3>
                    @endif
                    @if($subtitle)
                        <p class="text-sm text-gray-600 font-medium">{{ $subtitle }}</p>
                    @endif
                </div>

                <!-- Card Action Button -->
                <div class="flex-shrink-0">
                    <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-white/50 rounded-lg transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="{{ $title || $subtitle || $icon ? 'px-8 py-8' : 'p-8' }}">
        {{ $slot }}
    </div>
</div>
