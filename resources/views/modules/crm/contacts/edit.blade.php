@extends('layouts.app')

@section('title', __('erp.edit_contact'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="modern-card p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ __('erp.edit_contact') }}</h1>
                <p class="text-gray-600 mt-1">{{ __('erp.edit') }} {{ $contact->name }}</p>
            </div>
            <a href="{{ route('modules.crm.contacts.show', $contact) }}" class="btn-secondary">
                {{ __('erp.back') }}
            </a>
        </div>
    </div>

    <!-- Form -->
    <x-card>
        <form action="{{ route('modules.crm.contacts.update', $contact) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-ui.input name="name" label="{{ __('erp.name') }}" :value="old('name', $contact->name)" required />

                <x-ui.input name="company" label="{{ __('erp.company') }}" :value="old('company', $contact->company)" />

                <x-ui.input type="email" name="email" label="{{ __('erp.email') }}" :value="old('email', $contact->email)" />

                <x-ui.input name="phone" label="{{ __('erp.phone') }}" :value="old('phone', $contact->phone)" />

                <x-ui.select name="type" label="{{ __('erp.type') }}" required>
                    <option value="">{{ __('erp.select') }} {{ __('erp.type') }}</option>
                    <option value="lead" @selected(old('type', $contact->type)==='lead')>{{ __('erp.lead') }}</option>
                    <option value="client" @selected(old('type', $contact->type)==='client')>{{ __('erp.client') }}</option>
                </x-ui.select>

                <x-ui.select name="status" label="{{ __('erp.status') }}" required>
                    <option value="">{{ __('erp.select') }} {{ __('erp.status') }}</option>
                    <option value="new" @selected(old('status', $contact->status)==='new')>{{ __('erp.new') }}</option>
                    <option value="contacted" @selected(old('status', $contact->status)==='contacted')>{{ __('erp.contacted') }}</option>
                    <option value="qualified" @selected(old('status', $contact->status)==='qualified')>{{ __('erp.qualified') }}</option>
                    <option value="proposal" @selected(old('status', $contact->status)==='proposal')>{{ __('erp.proposal') }}</option>
                    <option value="negotiation" @selected(old('status', $contact->status)==='negotiation')>{{ __('erp.negotiation') }}</option>
                    <option value="closed_won" @selected(old('status', $contact->status)==='closed_won')>{{ __('erp.closed_won') }}</option>
                    <option value="closed_lost" @selected(old('status', $contact->status)==='closed_lost')>{{ __('erp.closed_lost') }}</option>
                </x-ui.select>

                <x-ui.input type="number" name="potential_value" label="{{ __('erp.potential_value') }}" :value="old('potential_value', $contact->potential_value)" step="0.01" min="0" />

                <x-ui.input name="source" label="{{ __('erp.source') }}" :value="old('source', $contact->source)" />

                <x-ui.input name="assigned_to" label="{{ __('erp.assigned_to') }}" :value="old('assigned_to', $contact->assigned_to)" />

                <x-ui.input type="datetime-local" name="next_follow_up" label="{{ __('erp.next_follow_up') }}" :value="old('next_follow_up', $contact->next_follow_up ? $contact->next_follow_up->format('Y-m-d\\TH:i') : '')" />
            </div>

            <!-- Notes -->
            <x-ui.textarea name="notes" label="{{ __('erp.notes') }}" rows="4" :value="old('notes', $contact->notes)" />

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                <a href="{{ route('modules.crm.contacts.show', $contact) }}" class="btn-secondary">
                    {{ __('erp.cancel') }}
                </a>
                <x-ui.button type="submit">{{ __('erp.save') }} {{ __('erp.contact') }}</x-ui.button>
            </div>
        </form>
    </x-card>
</div>
@endsection
