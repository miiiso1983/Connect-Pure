@extends('layouts.app')

@section('title', __('erp.add_contact'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('erp.add_contact') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('erp.create') }} {{ __('erp.contact') }}</p>
        </div>
        <a href="{{ route('modules.crm.contacts.index') }}" class="btn-secondary">
            {{ __('erp.back') }}
        </a>
    </div>

    <!-- Form -->
    <x-card>
        <form action="{{ route('modules.crm.contacts.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('erp.name') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Company -->
                <div>
                    <label for="company" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('erp.company') }}
                    </label>
                    <input type="text" id="company" name="company" value="{{ old('company') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('company') border-red-500 @enderror">
                    @error('company')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('erp.email') }}
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('erp.phone') }}
                    </label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('erp.type') }} <span class="text-red-500">*</span>
                    </label>
                    <select id="type" name="type" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('type') border-red-500 @enderror">
                        <option value="">{{ __('erp.select') }} {{ __('erp.type') }}</option>
                        <option value="lead" {{ old('type') === 'lead' ? 'selected' : '' }}>{{ __('erp.lead') }}</option>
                        <option value="client" {{ old('type') === 'client' ? 'selected' : '' }}>{{ __('erp.client') }}</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('erp.status') }} <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                        <option value="">{{ __('erp.select') }} {{ __('erp.status') }}</option>
                        <option value="new" {{ old('status') === 'new' ? 'selected' : '' }}>{{ __('erp.new') }}</option>
                        <option value="contacted" {{ old('status') === 'contacted' ? 'selected' : '' }}>{{ __('erp.contacted') }}</option>
                        <option value="qualified" {{ old('status') === 'qualified' ? 'selected' : '' }}>{{ __('erp.qualified') }}</option>
                        <option value="proposal" {{ old('status') === 'proposal' ? 'selected' : '' }}>{{ __('erp.proposal') }}</option>
                        <option value="negotiation" {{ old('status') === 'negotiation' ? 'selected' : '' }}>{{ __('erp.negotiation') }}</option>
                        <option value="closed_won" {{ old('status') === 'closed_won' ? 'selected' : '' }}>{{ __('erp.closed_won') }}</option>
                        <option value="closed_lost" {{ old('status') === 'closed_lost' ? 'selected' : '' }}>{{ __('erp.closed_lost') }}</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Potential Value -->
                <div>
                    <label for="potential_value" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('erp.potential_value') }}
                    </label>
                    <input type="number" id="potential_value" name="potential_value" value="{{ old('potential_value') }}" 
                           step="0.01" min="0"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('potential_value') border-red-500 @enderror">
                    @error('potential_value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Source -->
                <div>
                    <label for="source" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('erp.source') }}
                    </label>
                    <input type="text" id="source" name="source" value="{{ old('source') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('source') border-red-500 @enderror">
                    @error('source')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Assigned To -->
                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('erp.assigned_to') }}
                    </label>
                    <input type="text" id="assigned_to" name="assigned_to" value="{{ old('assigned_to') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('assigned_to') border-red-500 @enderror">
                    @error('assigned_to')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Next Follow-up -->
                <div>
                    <label for="next_follow_up" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('erp.next_follow_up') }}
                    </label>
                    <input type="datetime-local" id="next_follow_up" name="next_follow_up" value="{{ old('next_follow_up') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('next_follow_up') border-red-500 @enderror">
                    @error('next_follow_up')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('erp.notes') }}
                </label>
                <textarea id="notes" name="notes" rows="4"
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                <a href="{{ route('modules.crm.contacts.index') }}" class="btn-secondary">
                    {{ __('erp.cancel') }}
                </a>
                <button type="submit" class="btn-primary">
                    {{ __('erp.save') }} {{ __('erp.contact') }}
                </button>
            </div>
        </form>
    </x-card>
</div>
@endsection
