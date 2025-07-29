@props(['title', 'description', 'route', 'icon', 'color' => 'blue'])

<a href="{{ $route }}" class="block group">
    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 transition-all duration-200 hover:shadow-lg hover:border-{{ $color }}-300 group-hover:scale-105">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-{{ $color }}-100 rounded-lg flex items-center justify-center group-hover:bg-{{ $color }}-200 transition-colors duration-200">
                {!! $icon !!}
            </div>
            <h3 class="{{ app()->getLocale() === 'ar' ? 'mr-4' : 'ml-4' }} text-lg font-semibold text-gray-900 group-hover:text-{{ $color }}-700 transition-colors duration-200">
                {{ $title }}
            </h3>
        </div>
        <p class="text-gray-600 text-sm">{{ $description }}</p>
        <div class="mt-4 flex items-center text-{{ $color }}-600 text-sm font-medium">
            {{ __('erp.view') }} {{ __('erp.details') }}
            <svg class="w-4 h-4 {{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }} group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ app()->getLocale() === 'ar' ? 'M15 19l-7-7 7-7' : 'M9 5l7 7-7 7' }}"></path>
            </svg>
        </div>
    </div>
</a>
