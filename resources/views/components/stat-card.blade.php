@props(['title', 'value', 'icon', 'color' => 'blue', 'trend' => null, 'description' => null])

@php
    $gradientClasses = [
        'blue' => 'from-blue-500 to-blue-600',
        'green' => 'from-green-500 to-green-600',
        'yellow' => 'from-yellow-500 to-yellow-600',
        'red' => 'from-red-500 to-red-600',
        'purple' => 'from-purple-500 to-purple-600',
        'indigo' => 'from-indigo-500 to-indigo-600',
        'pink' => 'from-pink-500 to-pink-600',
        'cyan' => 'from-cyan-500 to-cyan-600',
        'orange' => 'from-orange-500 to-orange-600',
        'teal' => 'from-teal-500 to-teal-600',
        'gray' => 'from-gray-500 to-gray-600',
    ];

    $bgClasses = [
        'blue' => 'from-blue-50 to-blue-100',
        'green' => 'from-green-50 to-green-100',
        'yellow' => 'from-yellow-50 to-yellow-100',
        'red' => 'from-red-50 to-red-100',
        'purple' => 'from-purple-50 to-purple-100',
        'indigo' => 'from-indigo-50 to-indigo-100',
        'pink' => 'from-pink-50 to-pink-100',
        'cyan' => 'from-cyan-50 to-cyan-100',
        'orange' => 'from-orange-50 to-orange-100',
        'teal' => 'from-teal-50 to-teal-100',
        'gray' => 'from-gray-50 to-gray-100',
    ];
@endphp

<div class="modern-card group cursor-pointer relative overflow-hidden">
    <div class="p-8">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <!-- Icon -->
                <div class="w-16 h-16 bg-gradient-to-br {{ $gradientClasses[$color] }} rounded-2xl flex items-center justify-center shadow-lg mb-6 group-hover:scale-110 transition-transform duration-300">
                    <div class="text-white">
                        {!! $icon !!}
                    </div>
                </div>

                <!-- Value -->
                <div class="mb-3">
                    <div class="text-4xl font-bold text-gray-900 mb-1">{{ $value }}</div>
                    @if($trend)
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $trend > 0 ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100' }}">
                            @if($trend > 0)
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            @endif
                            {{ $trend > 0 ? '+' : '' }}{{ $trend }}%
                        </div>
                    @endif
                </div>

                <!-- Title and Description -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $title }}</h3>
                    @if($description)
                        <p class="text-sm text-gray-600">{{ $description }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Background Pattern -->
    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br {{ $bgClasses[$color] }} rounded-full opacity-20 -mr-16 -mt-16"></div>

    <!-- Hover Effect -->
    <div class="absolute inset-0 bg-gradient-to-br {{ $gradientClasses[$color] }} opacity-0 group-hover:opacity-5 transition-opacity duration-300 rounded-2xl"></div>
</div>
