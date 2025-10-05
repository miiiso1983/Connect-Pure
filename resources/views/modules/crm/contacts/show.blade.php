@extends('layouts.app')

@section('title', $contact->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $contact->name }}</h1>
            @if($contact->company)
                <p class="text-gray-600 mt-1">{{ $contact->company }}</p>
            @endif
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.crm.contacts.edit', $contact) }}" class="btn-primary">
                {{ __('erp.edit') }} {{ __('erp.contact') }}
            </a>
            <a href="{{ route('modules.crm.contacts.index') }}" class="btn-secondary">
                {{ __('erp.back') }}
            </a>
        </div>
    </div>

    <!-- Contact Info Card -->
    <x-card>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('erp.contact') }} {{ __('erp.details') }}</h3>
                <div class="space-y-3">
                    @if($contact->email)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-gray-900">{{ $contact->email }}</span>
                        </div>
                    @endif
                    
                    @if($contact->phone)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span class="text-gray-900">{{ $contact->phone }}</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('erp.status') }}</h3>
                @php
                    $typeMap = [
                        'lead' => 'info',
                        'client' => 'primary',
                    ];
                    $statusMap = [
                        'new' => 'secondary',
                        'contacted' => 'info',
                        'qualified' => 'warning',
                        'proposal' => 'info',
                        'negotiation' => 'warning',
                        'closed_won' => 'success',
                        'closed_lost' => 'danger',
                    ];
                    $typeVariant = $typeMap[$contact->type] ?? 'default';
                    $statusVariant = $statusMap[$contact->status] ?? 'default';
                @endphp
                <div class="space-y-3">
                    <div>
                        <x-badge :variant="$typeVariant" size="sm">{{ __('erp.' . $contact->type) }}</x-badge>
                    </div>
                    <div>
                        <x-badge :variant="$statusVariant" size="sm">{{ __('erp.' . $contact->status) }}</x-badge>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('erp.additional_info') }}</h3>
                <div class="space-y-3">
                    @if($contact->potential_value)
                        <div>
                            <span class="text-sm text-gray-600">{{ __('erp.potential_value') }}:</span>
                            <span class="text-lg font-semibold text-green-600">${{ number_format($contact->potential_value, 2) }}</span>
                        </div>
                    @endif
                    
                    @if($contact->source)
                        <div>
                            <span class="text-sm text-gray-600">{{ __('erp.source') }}:</span>
                            <span class="text-gray-900">{{ $contact->source }}</span>
                        </div>
                    @endif
                    
                    @if($contact->assigned_to)
                        <div>
                            <span class="text-sm text-gray-600">{{ __('erp.assigned_to') }}:</span>
                            <span class="text-gray-900">{{ $contact->assigned_to }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        @if($contact->notes)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('erp.notes') }}</h3>
                <p class="text-gray-700">{{ $contact->notes }}</p>
            </div>
        @endif
    </x-card>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}" aria-label="Tabs">
                <button onclick="showTab('communications')" id="tab-communications" 
                        class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    {{ __('erp.communications') }}
                    <span class="bg-gray-100 text-gray-900 {{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }} py-0.5 px-2.5 rounded-full text-xs font-medium">
                        {{ $contact->communications->count() }}
                    </span>
                </button>
                
                <button onclick="showTab('follow-ups')" id="tab-follow-ups"
                        class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    {{ __('erp.follow_ups') }}
                    <span class="bg-gray-100 text-gray-900 {{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }} py-0.5 px-2.5 rounded-full text-xs font-medium">
                        {{ $contact->followUps->count() }}
                    </span>
                </button>
            </nav>
        </div>

        <!-- Communications Tab -->
        <div id="communications-tab" class="tab-content p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('erp.communication_history') }}</h3>
                <button onclick="showAddCommunicationModal()" class="btn-primary">
                    {{ __('erp.add_communication') }}
                </button>
            </div>
            
            <x-crm.communication-log :communications="$contact->communications" />
        </div>

        <!-- Follow-ups Tab -->
        <div id="follow-ups-tab" class="tab-content p-6 hidden">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('erp.follow_ups') }}</h3>
                <button onclick="showAddFollowUpModal()" class="btn-primary">
                    {{ __('erp.add_follow_up') }}
                </button>
            </div>
            
            @if($contact->followUps->count())
                <div class="space-y-4">
                    @foreach($contact->followUps as $followUp)
                        <x-crm.follow-up-card :followUp="$followUp" />
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>{{ __('erp.no_data') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    // Add active class to selected tab button
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.remove('border-transparent', 'text-gray-500');
    activeButton.classList.add('border-blue-500', 'text-blue-600');
}

// Initialize first tab as active
document.addEventListener('DOMContentLoaded', function() {
    showTab('communications');
});

function showAddCommunicationModal() {
    // TODO: Implement modal for adding communication
    alert('Add Communication Modal - To be implemented');
}

function showAddFollowUpModal() {
    // TODO: Implement modal for adding follow-up
    alert('Add Follow-up Modal - To be implemented');
}
</script>
@endpush
@endsection
