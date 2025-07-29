@extends('layouts.app')

@section('title', __('roles.create_role'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('roles.create_role') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('roles.create_new_role_description') }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.roles.roles.index') }}" class="btn-secondary">
                {{ __('roles.back') }}
            </a>
        </div>
    </div>

    <!-- Create Role Form -->
    <x-card title="{{ __('roles.role_information') }}">
        <form action="{{ route('modules.roles.roles.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('roles.role_name') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" 
                           placeholder="{{ __('roles.enter_role_name') }}" 
                           class="form-input @error('name') border-red-300 @enderror" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('roles.role_slug') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug') }}" 
                           placeholder="{{ __('roles.enter_role_slug') }}" 
                           class="form-input @error('slug') border-red-300 @enderror" required>
                    <p class="mt-1 text-xs text-gray-500">{{ __('roles.role_slug_help') }}</p>
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('roles.role_description') }}
                </label>
                <textarea id="description" name="description" rows="3" 
                          placeholder="{{ __('roles.enter_description') }}" 
                          class="form-textarea @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Hierarchy Settings -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('roles.parent_role') }}
                    </label>
                    <select id="parent_id" name="parent_id" class="form-select @error('parent_id') border-red-300 @enderror">
                        <option value="">{{ __('roles.no_parent') }}</option>
                        @foreach($parentRoles as $parentRole)
                            <option value="{{ $parentRole->id }}" {{ old('parent_id') == $parentRole->id ? 'selected' : '' }}>
                                {{ str_repeat('— ', $parentRole->level) }}{{ $parentRole->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">{{ __('roles.parent_role_help') }}</p>
                    @error('parent_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="inherit_permissions" class="flex items-center">
                        <input type="checkbox" id="inherit_permissions" name="inherit_permissions" value="1"
                               {{ old('inherit_permissions', true) ? 'checked' : '' }}
                               class="form-checkbox">
                        <span class="ml-2 text-sm text-gray-700">{{ __('roles.inherit_permissions') }}</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">{{ __('roles.inherit_permissions_help') }}</p>
                    @error('inherit_permissions')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Status and Sort Order -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="is_active" class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="form-checkbox">
                        <span class="ml-2 text-sm text-gray-700">{{ __('roles.is_active') }}</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">{{ __('roles.active_role_help') }}</p>
                    @error('is_active')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('roles.sort_order') }}
                    </label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}"
                           min="0" placeholder="0"
                           class="form-input @error('sort_order') border-red-300 @enderror">
                    <p class="mt-1 text-xs text-gray-500">{{ __('roles.sort_order_help') }}</p>
                    @error('sort_order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Permissions -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-4">
                    {{ __('roles.permissions') }}
                </label>
                <p class="text-sm text-gray-600 mb-4">{{ __('roles.permissions_help') }}</p>

                <div class="space-y-6">
                    @foreach($permissionGroups as $groupKey => $group)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-lg font-medium text-gray-900">{{ $group['label'] }}</h4>
                                <div class="flex space-x-2">
                                    <button type="button" class="text-sm text-blue-600 hover:text-blue-800" 
                                            onclick="selectAllInGroup('{{ $groupKey }}')">
                                        {{ __('roles.select_all') }}
                                    </button>
                                    <button type="button" class="text-sm text-gray-600 hover:text-gray-800" 
                                            onclick="deselectAllInGroup('{{ $groupKey }}')">
                                        {{ __('roles.deselect_all') }}
                                    </button>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($group['permissions'] as $permission => $label)
                                    <label class="flex items-center group-{{ $groupKey }}">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission }}" 
                                               {{ in_array($permission, old('permissions', [])) ? 'checked' : '' }}
                                               class="form-checkbox permission-{{ $groupKey }}">
                                        <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @error('permissions')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }} pt-6 border-t border-gray-200">
                <a href="{{ route('modules.roles.roles.index') }}" class="btn-secondary">
                    {{ __('roles.cancel') }}
                </a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ __('roles.create_role') }}
                </button>
            </div>
        </form>
    </x-card>
</div>

<script>
function selectAllInGroup(groupKey) {
    const checkboxes = document.querySelectorAll('.permission-' + groupKey);
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllInGroup(groupKey) {
    const checkboxes = document.querySelectorAll('.permission-' + groupKey);
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
}

// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const slug = name.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
        .replace(/\s+/g, '-') // Replace spaces with hyphens
        .replace(/-+/g, '-') // Replace multiple hyphens with single hyphen
        .trim('-'); // Remove leading/trailing hyphens
    
    document.getElementById('slug').value = slug;
});
</script>
@endsection
