@props(['stage', 'count', 'value' => null, 'color' => 'blue', 'percentage' => null])

<div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">{{ __('erp.' . $stage) }}</h3>
        @if($percentage)
            <span class="text-sm text-gray-500">{{ $percentage }}%</span>
        @endif
    </div>
    
    <div class="mb-4">
        <div class="text-3xl font-bold text-{{ $color }}-600">{{ $count }}</div>
        @if($value)
            <div class="text-sm text-gray-600">${{ number_format($value, 2) }}</div>
        @endif
    </div>
    
    @if($percentage)
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-{{ $color }}-600 h-2 rounded-full transition-all duration-300 progress-bar" data-width="{{ $percentage }}"></div>
        </div>
    @endif
</div>

@once
@push('styles')
<style>
.progress-bar {
    width: 0%;
    transition: width 0.8s ease-in-out;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate progress bars
    setTimeout(function() {
        const progressBars = document.querySelectorAll('.progress-bar');
        progressBars.forEach(function(bar) {
            const width = bar.getAttribute('data-width');
            if (width) {
                bar.style.width = width + '%';
            }
        });
    }, 200);
});
</script>
@endpush
@endonce
