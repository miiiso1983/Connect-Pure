<div class="role-node" style="margin-left: {{ $level * 2 }}rem;">
    <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center space-x-4">
            <!-- Level Indicator -->
            <div class="flex items-center space-x-2">
                @if($level > 0)
                    @for($i = 0; $i < $level; $i++)
                        <div class="w-4 h-px bg-gray-300"></div>
                    @endfor
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                @endif
                
                <!-- Role Icon -->
                <div class="flex-shrink-0 h-10 w-10">
                    <div class="h-10 w-10 rounded-full flex items-center justify-center {{ $level === 0 ? 'bg-blue-100' : 'bg-green-100' }}">
                        <svg class="w-5 h-5 {{ $level === 0 ? 'text-blue-600' : 'text-green-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Role Information -->
            <div class="flex-1">
                <div class="flex items-center space-x-2">
                    <h4 class="text-lg font-medium text-gray-900">{{ $role['name'] }}</h4>
                    
                    <!-- Level Badge -->
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ __('roles.level') }} {{ $role['level'] }}
                    </span>
                    
                    <!-- Root Role Badge -->
                    @if($role['level'] === 0)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ __('roles.root_role') }}
                        </span>
                    @endif
                </div>
                
                <div class="text-sm text-gray-500 mt-1">
                    {{ $role['slug'] }}
                </div>
                
                <!-- Statistics -->
                <div class="flex items-center space-x-4 mt-2 text-sm text-gray-600">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        {{ $role['permissions_count'] }} {{ __('roles.permissions') }}
                    </div>
                    
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ $role['users_count'] }} {{ __('roles.users') }}
                    </div>
                    
                    @if(count($role['children']) > 0)
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            {{ count($role['children']) }} {{ __('roles.children_roles') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center space-x-2">
            <a href="{{ route('modules.roles.roles.show', $role['id']) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                {{ __('roles.view') }}
            </a>
            <a href="{{ route('modules.roles.roles.edit', $role['id']) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                {{ __('roles.edit') }}
            </a>
        </div>
    </div>

    <!-- Children -->
    @if(count($role['children']) > 0)
        <div class="mt-4 space-y-4">
            @foreach($role['children'] as $childRole)
                @include('modules.roles.hierarchy.tree-node', ['role' => $childRole, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
