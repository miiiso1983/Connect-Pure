@props([
    'name',
    'title' => null,
    'subtitle' => null,
    'size' => 'md',
    'closeable' => true,
    'persistent' => false,
    'maxWidth' => null
])

@php
    $sizeClasses = [
        'sm' => 'max-w-md',
        'md' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
        '2xl' => 'max-w-6xl',
        'full' => 'max-w-full mx-4'
    ];
    
    $maxWidthClass = $maxWidth ? $maxWidth : ($sizeClasses[$size] ?? 'max-w-md');
@endphp

<div 
    x-data="{ 
        show: false,
        open() { 
            this.show = true;
            document.body.style.overflow = 'hidden';
        },
        close() { 
            this.show = false;
            document.body.style.overflow = '';
        }
    }"
    x-on:open-modal.window="$event.detail === '{{ $name }}' ? open() : null"
    x-on:close-modal.window="$event.detail === '{{ $name }}' ? close() : null"
    x-on:keydown.escape.window="show && {{ $closeable ? 'close()' : 'null' }}"
    x-show="show"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    <!-- Background overlay -->
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div 
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
            @if($closeable && !$persistent)
                @click="close()"
            @endif
        ></div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div 
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block w-full {{ $maxWidthClass }} my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg"
            @click.stop
        >
            @if($title || $subtitle || $closeable)
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            @if($title)
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $title }}
                                </h3>
                            @endif
                            @if($subtitle)
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $subtitle }}
                                </p>
                            @endif
                        </div>
                        
                        @if($closeable)
                            <button 
                                @click="close()"
                                class="text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition ease-in-out duration-150"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            <div class="px-6 py-4">
                {{ $slot }}
            </div>

            @if(isset($footer))
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Global modal functions
window.openModal = function(name) {
    window.dispatchEvent(new CustomEvent('open-modal', { detail: name }));
};

window.closeModal = function(name) {
    window.dispatchEvent(new CustomEvent('close-modal', { detail: name }));
};

// Close modal on successful form submission
document.addEventListener('DOMContentLoaded', function() {
    // Listen for successful form submissions
    document.addEventListener('form-success', function(e) {
        if (e.detail && e.detail.closeModal) {
            closeModal(e.detail.closeModal);
        }
    });
    
    // Listen for Livewire events
    if (window.Livewire) {
        Livewire.on('closeModal', (modalName) => {
            closeModal(modalName);
        });
        
        Livewire.on('openModal', (modalName) => {
            openModal(modalName);
        });
    }
});
</script>
@endpush
