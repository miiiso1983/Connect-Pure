@props(['contact'])

<div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-200">
    <div class="flex items-start justify-between mb-4">
        <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900">{{ $contact->name }}</h3>
            @if($contact->company)
                <p class="text-sm text-gray-600">{{ $contact->company }}</p>
            @endif
        </div>
        <div class="flex space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <span class="px-2 py-1 text-xs font-medium bg-{{ $contact->type_color }}-100 text-{{ $contact->type_color }}-800 rounded-full">
                {{ __('erp.' . $contact->type) }}
            </span>
            <span class="px-2 py-1 text-xs font-medium bg-{{ $contact->status_color }}-100 text-{{ $contact->status_color }}-800 rounded-full">
                {{ __('erp.' . $contact->status) }}
            </span>
        </div>
    </div>

    <div class="space-y-2 mb-4">
        @if($contact->email)
            <div class="flex items-center text-sm text-gray-600">
                <svg class="w-4 h-4 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                {{ $contact->email }}
            </div>
        @endif
        
        @if($contact->phone)
            <div class="flex items-center text-sm text-gray-600">
                <svg class="w-4 h-4 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
                {{ $contact->phone }}
            </div>
        @endif
    </div>

    @if($contact->potential_value)
        <div class="mb-4">
            <span class="text-sm text-gray-600">{{ __('erp.potential_value') }}:</span>
            <span class="text-lg font-semibold text-green-600">${{ number_format($contact->potential_value, 2) }}</span>
        </div>
    @endif

    @if($contact->next_follow_up)
        <div class="mb-4">
            <span class="text-sm text-gray-600">{{ __('erp.next_follow_up') }}:</span>
            <span class="text-sm font-medium {{ $contact->next_follow_up < now() ? 'text-red-600' : 'text-blue-600' }}">
                {{ $contact->next_follow_up->format('M j, Y') }}
            </span>
        </div>
    @endif

    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
        <div class="flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            @if($contact->communications_count ?? $contact->communications->count())
                <span class="text-xs text-gray-500">
                    {{ $contact->communications_count ?? $contact->communications->count() }} {{ __('erp.communications') }}
                </span>
            @endif
            
            @if($contact->pending_follow_ups_count ?? $contact->pendingFollowUps->count())
                <span class="text-xs text-yellow-600">
                    {{ $contact->pending_follow_ups_count ?? $contact->pendingFollowUps->count() }} {{ __('erp.pending_follow_ups') }}
                </span>
            @endif
        </div>
        
        <a href="{{ route('modules.crm.contacts.show', $contact) }}" 
           class="text-sm text-blue-600 hover:text-blue-800 font-medium">
            {{ __('erp.view') }} {{ __('erp.details') }}
        </a>
    </div>
</div>
