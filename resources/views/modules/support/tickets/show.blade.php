@extends('layouts.app')

@section('title', $ticket->ticket_number)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $ticket->ticket_number }}</h1>
            <p class="text-gray-600 mt-1">{{ $ticket->title }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.support.tickets.edit', $ticket) }}" class="btn-secondary">
                {{ __('erp.edit_ticket') }}
            </a>
            <a href="{{ route('modules.support.tickets.index') }}" class="btn-secondary">
                {{ __('erp.back_to_tickets') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Ticket Details -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">{{ __('erp.ticket_details') }}</h2>
                    <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-{{ $ticket->status_color }}-100 text-{{ $ticket->status_color }}-800">
                            {{ __('erp.' . $ticket->status) }}
                        </span>
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-{{ $ticket->priority_color }}-100 text-{{ $ticket->priority_color }}-800">
                            {{ __('erp.' . $ticket->priority) }}
                        </span>
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-{{ $ticket->category_color }}-100 text-{{ $ticket->category_color }}-800">
                            {{ __('erp.' . $ticket->category) }}
                        </span>
                    </div>
                </div>
                
                <div class="prose max-w-none">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $ticket->description }}</p>
                </div>
                
                @if($ticket->tags && count($ticket->tags) > 0)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">{{ __('erp.tags') }}</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($ticket->tags as $tag)
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded">{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                @if($ticket->resolution_notes)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">{{ __('erp.resolution_notes') }}</h4>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $ticket->resolution_notes }}</p>
                    </div>
                @endif
            </div>
            
            <!-- Ticket Attachments -->
            @if($ticket->attachments && $ticket->attachments->count() > 0)
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('erp.attachments') }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($ticket->attachments as $attachment)
                            <x-support.attachment-item :attachment="$attachment" />
                        @endforeach
                    </div>
                </div>
            @endif
            
            <!-- Comments Thread -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('erp.comments') }}</h3>
                    <button onclick="toggleInternalComments()" id="toggleBtn" class="text-sm text-blue-600 hover:text-blue-800">
                        {{ __('erp.show_internal_comments') }}
                    </button>
                </div>
                
                <x-support.comment-thread :ticket="$ticket" :show-internal="false" />
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('erp.quick_actions') }}</h3>
                <div class="space-y-2">
                    @if($ticket->status !== 'resolved')
                        <button onclick="showResolveModal()" class="w-full btn-success text-sm">
                            {{ __('erp.resolve_ticket') }}
                        </button>
                    @endif
                    
                    @if($ticket->status === 'resolved' || $ticket->status === 'closed')
                        <form action="{{ route('modules.support.tickets.reopen', $ticket) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full btn-warning text-sm">
                                {{ __('erp.reopen_ticket') }}
                            </button>
                        </form>
                    @endif
                    
                    @if($ticket->status !== 'closed')
                        <form action="{{ route('modules.support.tickets.close', $ticket) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full btn-secondary text-sm" onclick="return confirmClose(this)" data-confirm-message="{{ __('erp.confirm_close_ticket') }}">
                                {{ __('erp.close_ticket') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            
            <!-- Ticket Information -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('erp.ticket_information') }}</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('erp.created') }}:</span>
                        <span class="text-gray-900">{{ $ticket->created_at->format('M j, Y g:i A') }}</span>
                    </div>
                    
                    @if($ticket->due_date)
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('erp.due_date') }}:</span>
                            <span class="text-gray-900 {{ $ticket->is_overdue ? 'text-red-600 font-medium' : '' }}">
                                {{ $ticket->due_date->format('M j, Y g:i A') }}
                            </span>
                        </div>
                    @endif
                    
                    @if($ticket->resolved_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('erp.resolved_at') }}:</span>
                            <span class="text-gray-900">{{ $ticket->resolved_at->format('M j, Y g:i A') }}</span>
                        </div>
                    @endif
                    
                    @if($ticket->response_time)
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('erp.response_time') }}:</span>
                            <span class="text-gray-900">{{ $ticket->response_time }}</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Customer Information -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('erp.customer_information') }}</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-600">{{ __('erp.name') }}:</span>
                        <p class="text-gray-900 font-medium">{{ $ticket->customer_name }}</p>
                    </div>
                    
                    <div>
                        <span class="text-gray-600">{{ __('erp.email') }}:</span>
                        <p class="text-gray-900">
                            <a href="mailto:{{ $ticket->customer_email }}" class="text-blue-600 hover:text-blue-800">
                                {{ $ticket->customer_email }}
                            </a>
                        </p>
                    </div>
                    
                    @if($ticket->customer_phone)
                        <div>
                            <span class="text-gray-600">{{ __('erp.phone') }}:</span>
                            <p class="text-gray-900">
                                <a href="tel:{{ $ticket->customer_phone }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $ticket->customer_phone }}
                                </a>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Assignment -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('erp.assignment') }}</h3>
                <form action="{{ route('modules.support.tickets.assign', $ticket) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-3">
                        <input type="text" name="assigned_to" 
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               value="{{ $ticket->assigned_to }}"
                               placeholder="{{ __('erp.assign_to_someone') }}">
                        <button type="submit" class="w-full btn-primary text-sm">
                            {{ __('erp.assign_ticket') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Resolve Ticket Modal -->
<div id="resolveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('erp.resolve_ticket') }}</h3>
        <form action="{{ route('modules.support.tickets.resolve', $ticket) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-4">
                <label for="resolution_notes" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('erp.resolution_notes') }} <span class="text-red-500">*</span>
                </label>
                <textarea id="resolution_notes" name="resolution_notes" rows="4" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="{{ __('erp.describe_resolution') }}"></textarea>
            </div>
            <div class="flex justify-end space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                <button type="button" onclick="hideResolveModal()" class="btn-secondary">
                    {{ __('erp.cancel') }}
                </button>
                <button type="submit" class="btn-success">
                    {{ __('erp.resolve_ticket') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let showingInternal = false;

function toggleInternalComments() {
    showingInternal = !showingInternal;
    const btn = document.getElementById('toggleBtn');
    
    if (showingInternal) {
        btn.textContent = '{{ __("erp.hide_internal_comments") }}';
        // In a real app, you would reload the comment thread with internal comments
        // For now, we'll just change the button text
    } else {
        btn.textContent = '{{ __("erp.show_internal_comments") }}';
    }
}

function showResolveModal() {
    document.getElementById('resolveModal').classList.remove('hidden');
}

function hideResolveModal() {
    document.getElementById('resolveModal').classList.add('hidden');
}

function confirmClose(button) {
    const message = button.getAttribute('data-confirm-message');
    return confirm(message);
}
</script>
@endpush
@endsection
