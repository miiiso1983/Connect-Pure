@props(['ticket', 'showInternal' => false])

<div class="space-y-4">
    @forelse($ticket->comments as $comment)
        @if(!$comment->is_internal || $showInternal)
            <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                <!-- Avatar -->
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-{{ $comment->author_type_color }}-100 rounded-full flex items-center justify-center">
                        {!! $comment->author_type_icon !!}
                    </div>
                </div>
                
                <!-- Comment Content -->
                <div class="flex-1 min-w-0">
                    <div class="bg-gray-50 rounded-lg p-4 {{ $comment->is_internal ? 'border-l-4 border-yellow-400' : '' }}">
                        <!-- Comment Header -->
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                <span class="text-sm font-medium text-{{ $comment->author_type_color }}-600">
                                    {{ $comment->author_name }}
                                </span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ $comment->author_type_color }}-100 text-{{ $comment->author_type_color }}-800">
                                    {{ __('erp.' . $comment->author_type) }}
                                </span>
                                @if($comment->is_internal)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                        {{ __('erp.internal_comment') }}
                                    </span>
                                @endif
                                @if($comment->is_solution)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                        {{ __('erp.solution') }}
                                    </span>
                                @endif
                            </div>
                            <span class="text-xs text-gray-500">
                                {{ $comment->created_at->format('M j, Y \a\t g:i A') }}
                            </span>
                        </div>
                        
                        <!-- Comment Text -->
                        <div class="text-gray-700 text-sm whitespace-pre-wrap">{{ $comment->comment }}</div>
                        
                        <!-- Comment Attachments -->
                        @if($comment->attachments && $comment->attachments->count() > 0)
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <h6 class="text-xs font-medium text-gray-900 mb-2">{{ __('erp.attachments') }}</h6>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($comment->attachments as $attachment)
                                        <x-support.attachment-item :attachment="$attachment" />
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Comment Actions -->
                    <div class="flex items-center space-x-2 mt-2 text-xs {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <span class="text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                        @if(!$comment->is_solution && $comment->author_type !== 'customer')
                            <button class="text-green-600 hover:text-green-800" onclick="markAsSolution(this)" data-comment-id="{{ $comment->id }}">
                                {{ __('erp.mark_as_solution') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @empty
        <div class="text-center py-8">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <p class="text-gray-500">{{ __('erp.no_comments_yet') }}</p>
        </div>
    @endforelse
</div>

<!-- Add Comment Form -->
<div class="mt-6 bg-white border border-gray-200 rounded-lg p-4">
    <h4 class="text-lg font-medium text-gray-900 mb-4">{{ __('erp.add_comment') }}</h4>
    
    <form action="{{ route('modules.support.tickets.comments.store', $ticket) }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="author_name" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('erp.name') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" id="author_name" name="author_name" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       value="{{ old('author_name', 'Support Agent') }}">
            </div>
            
            <div>
                <label for="author_email" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('erp.email') }} <span class="text-red-500">*</span>
                </label>
                <input type="email" id="author_email" name="author_email" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       value="{{ old('author_email', 'support@company.com') }}">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="author_type" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('erp.author_type') }} <span class="text-red-500">*</span>
                </label>
                <select id="author_type" name="author_type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="customer" {{ old('author_type') === 'customer' ? 'selected' : '' }}>{{ __('erp.customer') }}</option>
                    <option value="support" {{ old('author_type', 'support') === 'support' ? 'selected' : '' }}>{{ __('erp.support') }}</option>
                    <option value="technical" {{ old('author_type') === 'technical' ? 'selected' : '' }}>{{ __('erp.technical_team') }}</option>
                </select>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" id="is_internal" name="is_internal" value="1" {{ old('is_internal') ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_internal" class="{{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }} block text-sm text-gray-700">
                    {{ __('erp.internal_comment') }}
                </label>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" id="is_solution" name="is_solution" value="1" {{ old('is_solution') ? 'checked' : '' }}
                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                <label for="is_solution" class="{{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }} block text-sm text-gray-700">
                    {{ __('erp.mark_as_solution') }}
                </label>
            </div>
        </div>
        
        <div class="mb-4">
            <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('erp.comment') }} <span class="text-red-500">*</span>
            </label>
            <textarea id="comment" name="comment" rows="4" required
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="{{ __('erp.enter_your_comment') }}">{{ old('comment') }}</textarea>
        </div>
        
        <div class="mb-4">
            <label for="attachments" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('erp.attachments') }}
            </label>
            <input type="file" id="attachments" name="attachments[]" multiple
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.zip,.rar">
            <p class="text-xs text-gray-500 mt-1">{{ __('erp.max_file_size') }}: 10MB {{ __('erp.per_file') }}</p>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="btn-primary">
                {{ __('erp.add_comment') }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function markAsSolution(button) {
    const commentId = button.getAttribute('data-comment-id');
    if (confirm('{{ __("erp.confirm_mark_solution") }}')) {
        // In a real application, you would make an AJAX request here
        // For now, we'll just show an alert
        alert('{{ __("erp.feature_coming_soon") }}');
    }
}
</script>
@endpush
