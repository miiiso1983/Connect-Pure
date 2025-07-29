@props(['followUp'])

<div class="bg-white rounded-lg shadow-md border border-gray-200 p-4 {{ $followUp->is_overdue ? 'border-red-300 bg-red-50' : '' }}">
    <div class="flex items-start justify-between mb-3">
        <div class="flex-1">
            <h4 class="text-sm font-semibold text-gray-900">{{ $followUp->title }}</h4>
            @if($followUp->contact)
                <p class="text-xs text-gray-600">{{ $followUp->contact->name }}</p>
            @endif
        </div>
        <div class="flex space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <span class="px-2 py-1 text-xs font-medium bg-{{ $followUp->priority_color }}-100 text-{{ $followUp->priority_color }}-800 rounded-full">
                {{ __('erp.' . $followUp->priority) }}
            </span>
            <span class="px-2 py-1 text-xs font-medium bg-{{ $followUp->status_color }}-100 text-{{ $followUp->status_color }}-800 rounded-full">
                {{ __('erp.' . $followUp->status) }}
            </span>
        </div>
    </div>

    @if($followUp->description)
        <p class="text-sm text-gray-700 mb-3">{{ $followUp->description }}</p>
    @endif

    <div class="flex items-center justify-between text-xs text-gray-500">
        <span class="{{ $followUp->is_overdue ? 'text-red-600 font-medium' : '' }}">
            {{ $followUp->scheduled_date->format('M j, Y H:i') }}
            @if($followUp->is_overdue)
                ({{ __('erp.overdue') }})
            @endif
        </span>
        
        @if($followUp->assigned_to)
            <span>{{ $followUp->assigned_to }}</span>
        @endif
    </div>

    @if($followUp->status === 'pending')
        <div class="mt-3 pt-3 border-t border-gray-200">
            <div class="flex space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                <form action="{{ route('modules.crm.follow-ups.complete', $followUp) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">
                        {{ __('erp.mark_completed') }}
                    </button>
                </form>
                
                <button class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded">
                    {{ __('erp.reschedule') }}
                </button>
            </div>
        </div>
    @endif
</div>
