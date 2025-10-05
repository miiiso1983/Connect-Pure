@extends('layouts.app')

@section('title', __('erp.contacts'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="modern-card p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ __('erp.contacts') }}</h1>
                <p class="text-gray-600 mt-1">{{ __('erp.crm_description') }}</p>
            </div>
            <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                <a href="{{ route('modules.crm.contacts.bulk-upload') }}" class="btn-secondary">
                    <svg class="w-4 h-4 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    {{ __('erp.bulk_upload') }}
                </a>
                <a href="{{ route('modules.crm.contacts.create') }}" class="btn-primary">
                    {{ __('erp.add_contact') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <x-card>
        <form method="GET" action="{{ route('modules.crm.contacts.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-ui.input
                name="search"
                label="{{ __('erp.search') }}"
                placeholder="{{ __('erp.search') }}..."
                value="{{ request('search') }}"/>

            <x-ui.select name="type" label="{{ __('erp.type') }}">
                <option value="">{{ __('erp.all') }}</option>
                <option value="lead" @selected(request('type') === 'lead')>{{ __('erp.leads') }}</option>
                <option value="client" @selected(request('type') === 'client')>{{ __('erp.clients') }}</option>
            </x-ui.select>

            <x-ui.select name="status" label="{{ __('erp.status') }}">
                <option value="">{{ __('erp.all') }}</option>
                <option value="new" @selected(request('status') === 'new')>{{ __('erp.new') }}</option>
                <option value="contacted" @selected(request('status') === 'contacted')>{{ __('erp.contacted') }}</option>
                <option value="qualified" @selected(request('status') === 'qualified')>{{ __('erp.qualified') }}</option>
                <option value="proposal" @selected(request('status') === 'proposal')>{{ __('erp.proposal') }}</option>
                <option value="negotiation" @selected(request('status') === 'negotiation')>{{ __('erp.negotiation') }}</option>
                <option value="closed_won" @selected(request('status') === 'closed_won')>{{ __('erp.closed_won') }}</option>
                <option value="closed_lost" @selected(request('status') === 'closed_lost')>{{ __('erp.closed_lost') }}</option>
            </x-ui.select>

            <div class="flex items-end">
                <x-ui.button type="submit" class="w-full">{{ __('erp.filter') }}</x-ui.button>
            </div>
        </form>
    </x-card>

    <!-- Contacts Grid -->
    @if($contacts->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($contacts as $contact)
                <x-crm.contact-card :contact="$contact" />
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $contacts->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('erp.no_data') }}</h3>
                <p class="text-gray-600 mb-4">{{ __('erp.no_contacts_found') }}</p>
                <a href="{{ route('modules.crm.contacts.create') }}" class="btn-primary">
                    {{ __('erp.add_contact') }}
                </a>
            </div>
        </x-card>
    @endif
</div>
@endsection
