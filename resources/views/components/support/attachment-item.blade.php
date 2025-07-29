@props(['attachment'])

<div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
    <!-- File Icon -->
    <div class="flex-shrink-0">
        {!! $attachment->file_icon !!}
    </div>
    
    <!-- File Info -->
    <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-gray-900 truncate">
            {{ $attachment->original_name }}
        </p>
        <div class="flex items-center space-x-2 text-xs text-gray-500 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <span>{{ $attachment->file_size_human }}</span>
            <span>•</span>
            <span>{{ __('erp.uploaded_by') }}: {{ $attachment->uploaded_by }}</span>
            <span>•</span>
            <span>{{ $attachment->created_at->diffForHumans() }}</span>
        </div>
    </div>
    
    <!-- Actions -->
    <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
        <!-- Download Button -->
        <a href="{{ $attachment->download_url }}" 
           class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 bg-blue-100 rounded hover:bg-blue-200 transition-colors duration-200"
           title="{{ __('erp.download') }}">
            <svg class="w-3 h-3 {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            {{ __('erp.download') }}
        </a>
        
        <!-- Delete Button (if authorized) -->
        <form action="{{ route('modules.support.attachments.destroy', $attachment) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 bg-red-100 rounded hover:bg-red-200 transition-colors duration-200"
                    title="{{ __('erp.delete') }}"
                    onclick="return confirmDelete(this)"
                    data-confirm-message="{{ __('erp.confirm_delete') }}">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </form>
    </div>
</div>

@if($attachment->is_image)
    <!-- Image Preview Modal Trigger -->
    <div class="mt-2">
        <button onclick="showImagePreview('{{ Storage::url($attachment->file_path) }}', '{{ addslashes($attachment->original_name) }}')"
                class="text-xs text-blue-600 hover:text-blue-800">
            {{ __('erp.preview_image') }}
        </button>
    </div>
@endif

@once
@push('scripts')
<script>
function showImagePreview(imageUrl, imageName) {
    // Create modal
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50';
    modal.onclick = function() { document.body.removeChild(modal); };

    const modalContent = document.createElement('div');
    modalContent.className = 'max-w-4xl max-h-full p-4';
    modalContent.onclick = function(e) { e.stopPropagation(); };

    const img = document.createElement('img');
    img.src = imageUrl;
    img.alt = imageName;
    img.className = 'max-w-full max-h-full object-contain rounded-lg';

    const closeBtn = document.createElement('button');
    closeBtn.innerHTML = '×';
    closeBtn.className = 'absolute top-4 right-4 text-white text-3xl font-bold hover:text-gray-300';
    closeBtn.onclick = function() { document.body.removeChild(modal); };

    modalContent.appendChild(img);
    modal.appendChild(modalContent);
    modal.appendChild(closeBtn);
    document.body.appendChild(modal);
}

function confirmDelete(button) {
    const message = button.getAttribute('data-confirm-message');
    return confirm(message);
}
</script>
@endpush
@endonce
