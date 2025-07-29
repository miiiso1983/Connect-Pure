@props([
    'columns' => 'auto',
    'gap' => '6',
    'responsive' => true
])

@php
    $columnClasses = [
        '1' => 'grid-cols-1',
        '2' => 'grid-cols-1 md:grid-cols-2',
        '3' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
        '4' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
        '5' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5',
        '6' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6',
        'auto' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4',
    ];
    
    $gapClasses = [
        '1' => 'gap-1',
        '2' => 'gap-2',
        '3' => 'gap-3',
        '4' => 'gap-4',
        '5' => 'gap-5',
        '6' => 'gap-6',
        '8' => 'gap-8',
    ];
    
    $classes = [
        'grid',
        $columnClasses[$columns] ?? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4',
        $gapClasses[$gap] ?? 'gap-6',
    ];
    
    if ($responsive) {
        $classes[] = 'auto-rows-fr';
    }
@endphp

<div {{ $attributes->merge(['class' => implode(' ', $classes)]) }}>
    {{ $slot }}
</div>

@push('styles')
<style>
.dashboard-grid {
    display: grid;
    gap: 1.5rem;
}

/* Auto-fit responsive grid */
.dashboard-grid.auto-fit {
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
}

/* Auto-fill responsive grid */
.dashboard-grid.auto-fill {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
}

/* Masonry-like layout */
.dashboard-grid.masonry {
    column-count: 1;
    column-gap: 1.5rem;
}

@media (min-width: 640px) {
    .dashboard-grid.masonry {
        column-count: 2;
    }
}

@media (min-width: 768px) {
    .dashboard-grid.masonry {
        column-count: 3;
    }
}

@media (min-width: 1024px) {
    .dashboard-grid.masonry {
        column-count: 4;
    }
}

.dashboard-grid.masonry > * {
    break-inside: avoid;
    margin-bottom: 1.5rem;
}

/* Grid item animations */
.dashboard-grid-item {
    animation: fadeInUp 0.5s ease-out;
}

.dashboard-grid-item:nth-child(1) { animation-delay: 0.1s; }
.dashboard-grid-item:nth-child(2) { animation-delay: 0.2s; }
.dashboard-grid-item:nth-child(3) { animation-delay: 0.3s; }
.dashboard-grid-item:nth-child(4) { animation-delay: 0.4s; }
.dashboard-grid-item:nth-child(5) { animation-delay: 0.5s; }
.dashboard-grid-item:nth-child(6) { animation-delay: 0.6s; }

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .dashboard-grid {
        gap: 1rem;
    }
    
    .dashboard-grid > * {
        min-height: auto;
    }
}

/* Print optimizations */
@media print {
    .dashboard-grid {
        display: block;
    }
    
    .dashboard-grid > * {
        break-inside: avoid;
        margin-bottom: 1rem;
    }
}
</style>
@endpush
