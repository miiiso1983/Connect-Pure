@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'method' => 'POST',
    'action' => null,
    'enctype' => null,
    'submitText' => 'Save',
    'cancelUrl' => null,
    'cancelText' => 'Cancel',
    'loading' => false,
    'compact' => false
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden']) }}>
    @if($title || $subtitle || $icon)
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center">
                @if($icon)
                    <div class="flex-shrink-0 mr-4">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            {!! $icon !!}
                        </div>
                    </div>
                @endif
                
                <div>
                    @if($title)
                        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                    @endif
                    @if($subtitle)
                        <p class="text-sm text-gray-600 mt-1">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
    
    <form 
        @if($action) action="{{ $action }}" @endif
        method="{{ $method === 'GET' ? 'GET' : 'POST' }}"
        @if($enctype) enctype="{{ $enctype }}" @endif
        class="form-card"
    >
        @if($method !== 'GET' && $method !== 'POST')
            @method($method)
        @endif
        
        @if($method !== 'GET')
            @csrf
        @endif
        
        <div class="p-6 {{ $compact ? 'p-4' : '' }}">
            {{ $slot }}
        </div>
        
        @if(isset($actions) || $submitText || $cancelUrl)
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }} {{ $compact ? 'px-4 py-3' : '' }}">
                @if(isset($actions))
                    {{ $actions }}
                @else
                    @if($cancelUrl)
                        <a href="{{ $cancelUrl }}" class="btn-secondary">
                            {{ $cancelText }}
                        </a>
                    @endif
                    
                    <button type="submit" class="btn-primary" @if($loading) disabled @endif>
                        @if($loading)
                            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('common.processing') }}...
                        @else
                            {{ $submitText }}
                        @endif
                    </button>
                @endif
            </div>
        @endif
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation and submission handling
    const form = document.querySelector('.form-card');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton && !submitButton.disabled) {
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('common.processing') }}...
                `;
                
                // Re-enable button after 5 seconds to prevent permanent disable
                setTimeout(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = '{{ $submitText }}';
                }, 5000);
            }
        });
    }
});
</script>
@endpush
