@props(['communications'])

<div class="space-y-4">
    @forelse($communications as $communication)
        <div class="bg-gray-50 rounded-lg p-4 border-{{ app()->getLocale() === 'ar' ? 'r' : 'l' }}-4 border-{{ $communication->type_color }}-500">
            <div class="flex items-start justify-between mb-2">
                <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                    <div class="text-{{ $communication->type_color }}-600">
                        {!! $communication->type_icon !!}
                    </div>
                    <span class="text-sm font-medium text-gray-900">{{ __('erp.' . $communication->type) }}</span>
                    @if($communication->subject)
                        <span class="text-sm text-gray-600">- {{ $communication->subject }}</span>
                    @endif
                </div>
                <span class="text-xs text-gray-500">
                    {{ $communication->communication_date->format('M j, Y H:i') }}
                </span>
            </div>
            
            <div class="text-sm text-gray-700 mb-2">
                {{ $communication->content }}
            </div>
            
            @if($communication->created_by)
                <div class="text-xs text-gray-500">
                    {{ __('erp.created') }} {{ __('erp.by') }} {{ $communication->created_by }}
                </div>
            @endif
        </div>
    @empty
        <div class="text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <p>{{ __('erp.no_data') }}</p>
        </div>
    @endforelse
</div>
