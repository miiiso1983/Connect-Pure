@extends('layouts.app')

@section('title', __('roles.user_roles'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('roles.user_roles') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('roles.manage_user_role_assignments') }}</p>
        </div>
        <div class="mt-4 lg:mt-0 flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('admin.user-roles.export') }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                {{ __('roles.export_data') }}
            </a>
            <a href="{{ route('admin.roles.index') }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                {{ __('roles.manage_roles') }}
            </a>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('roles.users_and_roles') }}</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('roles.user') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('roles.current_roles') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('roles.permissions_count') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('roles.last_login') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('roles.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                            <span class="text-white font-medium text-sm">
                                                {{ substr($user->name, 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($user->roles as $role)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $role->localized_name }}
                                        </span>
                                    @empty
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            {{ __('roles.no_roles_assigned') }}
                                        </span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ count($user->getAllPermissions()) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : __('roles.never') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                    <a href="{{ route('admin.user-roles.show', $user) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        {{ __('roles.manage') }}
                                    </a>
                                    <button onclick="viewUserPermissions({{ $user->id }})" 
                                            class="text-green-600 hover:text-green-900">
                                        {{ __('roles.permissions') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Role Distribution Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Role Distribution -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('roles.role_distribution') }}</h3>
            <div class="space-y-3">
                @foreach($roles as $role)
                    @php
                        $userCount = $role->users()->count();
                        $percentage = $users->total() > 0 ? ($userCount / $users->total()) * 100 : 0;
                    @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-700">{{ $role->localized_name }}</span>
                        </div>
                        <div class="flex items-center space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <span class="text-sm text-gray-600 w-8 text-right">{{ $userCount }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('roles.quick_actions') }}</h3>
            <div class="space-y-3">
                <button onclick="showBulkAssignModal()" 
                        class="w-full text-left px-4 py-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors duration-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ __('roles.bulk_assign_roles') }}</div>
                            <div class="text-xs text-gray-500">{{ __('roles.assign_roles_to_multiple_users') }}</div>
                        </div>
                    </div>
                </button>
                
                <button onclick="showRoleHierarchy()" 
                        class="w-full text-left px-4 py-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors duration-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ __('roles.view_hierarchy') }}</div>
                            <div class="text-xs text-gray-500">{{ __('roles.view_role_hierarchy_and_structure') }}</div>
                        </div>
                    </div>
                </button>
                
                <a href="{{ route('admin.roles.permission-matrix') }}" 
                   class="block w-full text-left px-4 py-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors duration-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ __('roles.permission_matrix') }}</div>
                            <div class="text-xs text-gray-500">{{ __('roles.view_all_role_permissions') }}</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- User Permissions Modal -->
<div id="userPermissionsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-96 overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900" id="modalUserName">{{ __('roles.user_permissions') }}</h3>
            </div>
            <div class="p-6" id="modalContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="px-6 py-4 border-t border-gray-200 text-right">
                <button onclick="closeUserPermissionsModal()" class="btn-secondary">
                    {{ __('roles.close') }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewUserPermissions(userId) {
    document.getElementById('userPermissionsModal').classList.remove('hidden');
    document.getElementById('modalContent').innerHTML = '<div class="text-center py-4">{{ __("roles.loading") }}...</div>';
    
    fetch(`/admin/user-roles/${userId}/permissions`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('modalUserName').textContent = `${data.user.name} - {{ __('roles.permissions') }}`;
            
            let content = '<div class="space-y-4">';
            
            // Roles
            content += '<div><h4 class="font-medium text-gray-900 mb-2">{{ __("roles.assigned_roles") }}:</h4>';
            content += '<div class="flex flex-wrap gap-2">';
            data.roles.forEach(role => {
                content += `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${role.name}</span>`;
            });
            content += '</div></div>';
            
            // Permissions by module
            content += '<div><h4 class="font-medium text-gray-900 mb-2">{{ __("roles.permissions_by_module") }}:</h4>';
            for (const [module, permissions] of Object.entries(data.grouped_permissions)) {
                content += `<div class="mb-3">`;
                content += `<h5 class="text-sm font-medium text-gray-700 mb-1">${module.charAt(0).toUpperCase() + module.slice(1)}:</h5>`;
                content += `<div class="flex flex-wrap gap-1">`;
                permissions.forEach(permission => {
                    content += `<span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-700">${permission}</span>`;
                });
                content += `</div></div>`;
            }
            content += '</div>';
            
            content += `<div class="text-sm text-gray-500">{{ __('roles.total_permissions') }}: ${data.permissions_count}</div>`;
            content += '</div>';
            
            document.getElementById('modalContent').innerHTML = content;
        })
        .catch(error => {
            document.getElementById('modalContent').innerHTML = '<div class="text-red-600">{{ __("roles.error_loading_permissions") }}</div>';
        });
}

function closeUserPermissionsModal() {
    document.getElementById('userPermissionsModal').classList.add('hidden');
}

function showBulkAssignModal() {
    alert('{{ __("roles.bulk_assign_feature_coming_soon") }}');
}

function showRoleHierarchy() {
    alert('{{ __("roles.hierarchy_feature_coming_soon") }}');
}
</script>
@endpush
@endsection
