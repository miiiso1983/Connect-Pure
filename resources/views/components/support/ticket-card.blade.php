@props(['ticket', 'collapsible' => true])

<div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
    <!-- Ticket Header -->
    <div class="p-4 border-b border-gray-200 {{ $collapsible ? 'cursor-pointer' : '' }}" 
         @if($collapsible) onclick="toggleTicket('ticket-{{ $ticket->id }}')" @endif>
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $ticket->ticket_number }}
                    </h3>
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ $ticket->status_color }}-100 text-{{ $ticket->status_color }}-800">
                        {{ __('erp.' . $ticket->status) }}
                    </span>
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ $ticket->priority_color }}-100 text-{{ $ticket->priority_color }}-800">
                        {{ __('erp.' . $ticket->priority) }}
                    </span>
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ $ticket->category_color }}-100 text-{{ $ticket->category_color }}-800">
                        {{ __('erp.' . $ticket->category) }}
                    </span>
                </div>
                <h4 class="text-md font-medium text-gray-800 mt-1">{{ $ticket->title }}</h4>
                <div class="flex items-center space-x-4 text-sm text-gray-600 mt-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <span>
                        <svg class="w-4 h-4 inline {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ $ticket->customer_name }}
                    </span>
                    <span>
                        <svg class="w-4 h-4 inline {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $ticket->created_at->diffForHumans() }}
                    </span>
                    @if($ticket->assigned_to)
                        <span>
                            <svg class="w-4 h-4 inline {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $ticket->assigned_to }}
                        </span>
                    @endif
                    @if($ticket->is_overdue)
                        <span class="text-red-600 font-medium">
                            <svg class="w-4 h-4 inline {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            {{ __('erp.overdue') }}
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                @if($ticket->comments_count > 0)
                    <span class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        {{ $ticket->comments_count }}
                    </span>
                @endif
                
                @if($ticket->attachments_count > 0)
                    <span class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        {{ $ticket->attachments_count }}
                    </span>
                @endif
                
                @if($collapsible)
                    <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" id="chevron-{{ $ticket->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                @endif
                
                <a href="{{ route('modules.support.tickets.show', $ticket) }}" class="text-blue-600 hover:text-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Collapsible Content -->
    @if($collapsible)
        <div id="ticket-{{ $ticket->id }}" class="hidden">
            <div class="p-4 border-b border-gray-100">
                <p class="text-gray-700 text-sm">{{ Str::limit($ticket->description, 200) }}</p>
                @if($ticket->tags && count($ticket->tags) > 0)
                    <div class="flex flex-wrap gap-1 mt-2">
                        @foreach($ticket->tags as $tag)
                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded">{{ $tag }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
            
            @if($ticket->comments && $ticket->comments->count() > 0)
                <div class="p-4">
                    <h5 class="text-sm font-medium text-gray-900 mb-2">{{ __('erp.recent_comments') }}</h5>
                    <div class="space-y-2">
                        @foreach($ticket->comments->take(2) as $comment)
                            <div class="text-sm">
                                <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                    <span class="font-medium text-{{ $comment->author_type_color }}-600">{{ $comment->author_name }}</span>
                                    <span class="text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                    @if($comment->is_internal)
                                        <span class="px-1 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded">{{ __('erp.internal') }}</span>
                                    @endif
                                </div>
                                <p class="text-gray-700 mt-1">{{ Str::limit($comment->comment, 100) }}</p>
                            </div>
                        @endforeach
                        @if($ticket->comments->count() > 2)
                            <p class="text-xs text-gray-500">{{ __('erp.and') }} {{ $ticket->comments->count() - 2 }} {{ __('erp.more_comments') }}</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>

@if($collapsible)
@push('scripts')
<script>
function toggleTicket(ticketId) {
    const content = document.getElementById(ticketId);
    const chevron = document.getElementById('chevron-' + ticketId.split('-')[1]);
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        chevron.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        chevron.style.transform = 'rotate(0deg)';
    }
}
</script>
@endpush
@endif
