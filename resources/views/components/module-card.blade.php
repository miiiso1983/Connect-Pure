@props(['title', 'description', 'route', 'icon', 'color' => 'blue'])

@php
    $gradients = [
        'blue' => 'from-blue-500 to-blue-600',
        'green' => 'from-green-500 to-green-600',
        'yellow' => 'from-amber-500 to-orange-600',
        'red' => 'from-rose-500 to-red-600',
        'purple' => 'from-violet-500 to-purple-600',
        'indigo' => 'from-indigo-500 to-indigo-600',
        'pink' => 'from-pink-500 to-rose-600',
        'cyan' => 'from-cyan-500 to-teal-600',
    ];

    $tints = [
        'blue' => 'bg-blue-100 text-blue-700',
        'green' => 'bg-green-100 text-green-700',
        'yellow' => 'bg-amber-100 text-amber-700',
        'red' => 'bg-rose-100 text-rose-700',
        'purple' => 'bg-violet-100 text-violet-700',
        'indigo' => 'bg-indigo-100 text-indigo-700',
        'pink' => 'bg-pink-100 text-pink-700',
        'cyan' => 'bg-cyan-100 text-cyan-700',
    ];

    $gradient = $gradients[$color] ?? $gradients['blue'];
    $tint = $tints[$color] ?? $tints['blue'];
@endphp

<a href="{{ $route }}" class="block group focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded-2xl h-full">
    <div class="modern-card p-6 relative overflow-hidden min-h-[200px] flex flex-col">
        <!-- Accent background circle -->
        <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full bg-gradient-to-br {{ $gradient }} opacity-10"></div>

        <div class="flex items-center mb-4 relative">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow {{ $tint }} group-hover:scale-105 transition-transform duration-200">
                {!! $icon !!}
            </div>
            <h3 class="{{ app()->getLocale() === 'ar' ? 'mr-4' : 'ml-4' }} text-lg font-semibold text-gray-900">
                {{ $title }}
            </h3>
        </div>

        <p class="text-gray-600 text-sm" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $description }}</p>
        <div class="mt-auto inline-flex items-center text-sm font-medium text-gray-700 group-hover:text-gray-900">
            {{ __('erp.view') }} {{ __('erp.details') }}
            <svg class="w-4 h-4 {{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }} group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ app()->getLocale() === 'ar' ? 'M15 19l-7-7 7-7' : 'M9 5l7 7-7 7' }}"></path>
            </svg>
        </div>

        <!-- Hover gradient veil -->
        <div class="absolute inset-0 rounded-2xl bg-gradient-to-r {{ $gradient }} opacity-0 group-hover:opacity-[0.06] transition-opacity duration-300 pointer-events-none"></div>
    </div>
</a>
