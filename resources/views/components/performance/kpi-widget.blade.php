@props(['title', 'value', 'subtitle' => null, 'color' => 'blue', 'icon' => null, 'trend' => null, 'trendDirection' => null])

<div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                @if($icon)
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-{{ $color }}-100 rounded-lg flex items-center justify-center">
                            {!! $icon !!}
                        </div>
                    </div>
                @endif
                
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-600 truncate">{{ $title }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
                    @if($subtitle)
                        <p class="text-sm text-gray-500">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
        </div>
        
        @if($trend !== null)
            <div class="flex items-center space-x-1 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                @if($trendDirection === 'up')
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7m0 10h-10"></path>
                    </svg>
                    <span class="text-sm font-medium text-green-600">+{{ $trend }}%</span>
                @elseif($trendDirection === 'down')
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7l-9.2 9.2M7 7v10m0-10h10"></path>
                    </svg>
                    <span class="text-sm font-medium text-red-600">{{ $trend }}%</span>
                @else
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-600">{{ $trend }}%</span>
                @endif
            </div>
        @endif
    </div>
    
    <!-- Progress Bar (if applicable) -->
    @if(isset($percentage))
        <div class="mt-4">
            <div class="flex items-center justify-between text-sm text-gray-600 mb-1">
                <span>{{ __('erp.progress') }}</span>
                <span>{{ $percentage }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="h-2 rounded-full transition-all duration-300 bg-blue-600" style="width: {{ $percentage }}%"></div>
            </div>
        </div>
    @endif
    
    <!-- Additional Content Slot -->
    @if(isset($slot) && !empty(trim($slot)))
        <div class="mt-4 pt-4 border-t border-gray-100">
            {{ $slot }}
        </div>
    @endif
</div>
