@extends('layouts.app')

@section('title', __('erp.create_ticket'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('erp.create_ticket') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('erp.create_new_support_ticket') }}</p>
        </div>
        <a href="{{ route('modules.support.tickets.index') }}" class="btn-secondary">
            {{ __('erp.back_to_tickets') }}
        </a>
    </div>

    <!-- Create Ticket Form -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
        <form action="{{ route('modules.support.tickets.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('erp.title') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           value="{{ old('title') }}"
                           placeholder="{{ __('erp.enter_ticket_title') }}">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('erp.priority') }} <span class="text-red-500">*</span>
                    </label>
                    <select id="priority" name="priority" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">{{ __('erp.select_priority') }}</option>
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>{{ __('erp.low') }}</option>
                        <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>{{ __('erp.medium') }}</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>{{ __('erp.high') }}</option>
                        <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>{{ __('erp.urgent') }}</option>
                    </select>
                    @error('priority')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('erp.category') }} <span class="text-red-500">*</span>
                    </label>
                    <select id="category" name="category" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">{{ __('erp.select_category') }}</option>
                        <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>{{ __('erp.technical') }}</option>
                        <option value="billing" {{ old('category') === 'billing' ? 'selected' : '' }}>{{ __('erp.billing') }}</option>
                        <option value="general" {{ old('category', 'general') === 'general' ? 'selected' : '' }}>{{ __('erp.general') }}</option>
                        <option value="feature_request" {{ old('category') === 'feature_request' ? 'selected' : '' }}>{{ __('erp.feature_request') }}</option>
                        <option value="bug_report" {{ old('category') === 'bug_report' ? 'selected' : '' }}>{{ __('erp.bug_report') }}</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('erp.due_date') }}
                    </label>
                    <input type="datetime-local" id="due_date" name="due_date"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           value="{{ old('due_date') }}">
                    @error('due_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Customer Information -->
            <div class="border-t border-gray-200 pt-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('erp.customer_information') }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('erp.customer_name') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="customer_name" name="customer_name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               value="{{ old('customer_name') }}"
                               placeholder="{{ __('erp.enter_customer_name') }}">
                        @error('customer_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('erp.customer_email') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="customer_email" name="customer_email" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               value="{{ old('customer_email') }}"
                               placeholder="{{ __('erp.enter_customer_email') }}">
                        @error('customer_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('erp.customer_phone') }}
                        </label>
                        <input type="tel" id="customer_phone" name="customer_phone"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               value="{{ old('customer_phone') }}"
                               placeholder="{{ __('erp.enter_customer_phone') }}">
                        @error('customer_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('erp.description') }} <span class="text-red-500">*</span>
                </label>
                <textarea id="description" name="description" rows="6" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="{{ __('erp.describe_the_issue') }}">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Additional Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('erp.assign_to') }}
                    </label>
                    <input type="text" id="assigned_to" name="assigned_to"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           value="{{ old('assigned_to') }}"
                           placeholder="{{ __('erp.enter_assignee_name') }}">
                    @error('assigned_to')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="tags" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('erp.tags') }}
                    </label>
                    <input type="text" id="tags" name="tags"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           value="{{ old('tags') }}"
                           placeholder="{{ __('erp.enter_tags_comma_separated') }}">
                    <p class="text-xs text-gray-500 mt-1">{{ __('erp.separate_tags_with_commas') }}</p>
                    @error('tags')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- File Attachments -->
            <div class="mb-6">
                <label for="attachments" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('erp.attachments') }}
                </label>
                <input type="file" id="attachments" name="attachments[]" multiple
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.zip,.rar">
                <p class="text-xs text-gray-500 mt-1">{{ __('erp.max_file_size') }}: 10MB {{ __('erp.per_file') }}</p>
                @error('attachments.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                <a href="{{ route('modules.support.tickets.index') }}" class="btn-secondary">
                    {{ __('erp.cancel') }}
                </a>
                <button type="submit" class="btn-primary">
                    {{ __('erp.create_ticket') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
