<!-- Main Navigation Section -->
<div class="space-y-2">
    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}"
       class="group flex items-center px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg shadow-blue-500/25' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-all duration-300 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
            </svg>
        </div>
        <span class="font-semibold">Dashboard</span>
        @if(request()->routeIs('dashboard'))
            <div class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></div>
        @endif
    </a>

    <!-- CRM Module -->
    <a href="{{ route('modules.crm.index') }}"
       class="group flex items-center px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('modules.crm.*') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-500/25' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ request()->routeIs('modules.crm.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-all duration-300 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </div>
        <span class="font-semibold">CRM</span>
        @if(request()->routeIs('modules.crm.*'))
            <div class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></div>
        @endif
    </a>

    <!-- Support Module -->
    <a href="{{ route('modules.support.index') }}"
       class="group flex items-center px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('modules.support.*') ? 'bg-gradient-to-r from-emerald-600 to-green-600 text-white shadow-lg shadow-emerald-500/25' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ request()->routeIs('modules.support.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-all duration-300 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
        </div>
        <span class="font-semibold">Support</span>
        @if(request()->routeIs('modules.support.*'))
            <div class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></div>
        @endif
    </a>

    <!-- Accounting Module -->
    <a href="{{ route('modules.accounting.index') }}"
       class="group flex items-center px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('modules.accounting.*') ? 'bg-gradient-to-r from-amber-600 to-orange-600 text-white shadow-lg shadow-amber-500/25' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ request()->routeIs('modules.accounting.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-all duration-300 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <span class="font-semibold">Accounting</span>
        @if(request()->routeIs('modules.accounting.*'))
            <div class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></div>
        @endif
    </a>

    <!-- Performance Module -->
    <a href="{{ route('modules.performance.index') }}"
       class="group flex items-center px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('modules.performance.*') ? 'bg-gradient-to-r from-violet-600 to-purple-600 text-white shadow-lg shadow-violet-500/25' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ request()->routeIs('modules.performance.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-all duration-300 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
        </div>
        <span class="font-semibold">Performance</span>
        @if(request()->routeIs('modules.performance.*'))
            <div class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></div>
        @endif
    </a>

    <!-- HR Module -->
    <a href="{{ route('modules.hr.index') }}"
       class="group flex items-center px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('modules.hr.*') ? 'bg-gradient-to-r from-pink-600 to-rose-600 text-white shadow-lg shadow-pink-500/25' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ request()->routeIs('modules.hr.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-all duration-300 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
        <span class="font-semibold">Human Resources</span>
        @if(request()->routeIs('modules.hr.*'))
            <div class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></div>
        @endif
    </a>

    <!-- Roles Module -->
    <a href="{{ route('modules.roles.index') }}"
       class="group flex items-center px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('modules.roles.*') ? 'bg-gradient-to-r from-red-600 to-pink-600 text-white shadow-lg shadow-red-500/25' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ request()->routeIs('modules.roles.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-all duration-300 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </div>
        <span class="font-semibold">Roles & Permissions</span>
        @if(request()->routeIs('modules.roles.*'))
            <div class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></div>
        @endif
    </a>
</div>

<!-- Admin Section -->
@if(auth()->user() && auth()->user()->hasAnyRole(['top_management', 'middle_management', 'master-admin']))
    <!-- Section Divider -->
    <div class="relative my-8">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-700"></div>
        </div>
        <div class="relative flex justify-center">
            <span class="bg-gray-900 px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Administration</span>
        </div>
    </div>

    <div class="space-y-2">
        <!-- Role Management -->
        @if(auth()->user()->hasPermission('roles.view') || auth()->user()->hasRole('master-admin'))
            <a href="{{ route('admin.roles.index') }}"
               class="group flex items-center px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('admin.roles.*') ? 'bg-gradient-to-r from-cyan-600 to-blue-600 text-white shadow-lg shadow-cyan-500/25' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ request()->routeIs('admin.roles.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-all duration-300 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <span class="font-semibold">Role Management</span>
                @if(request()->routeIs('admin.roles.*'))
                    <div class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></div>
                @endif
            </a>
        @endif

        <!-- User Role Management -->
        @if(auth()->user()->hasAnyPermission(['users.view', 'roles.manage']) || auth()->user()->hasRole('master-admin'))
            <a href="{{ route('admin.user-roles.index') }}"
               class="group flex items-center px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-300 {{ request()->routeIs('admin.user-roles.*') ? 'bg-gradient-to-r from-teal-600 to-cyan-600 text-white shadow-lg shadow-teal-500/25' : 'text-gray-300 hover:text-white hover:bg-gray-800/50' }}">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ request()->routeIs('admin.user-roles.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-all duration-300 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <span class="font-semibold">User Management</span>
                @if(request()->routeIs('admin.user-roles.*'))
                    <div class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></div>
                @endif
            </a>
        @endif

        <!-- System Settings (if master admin) -->
        @if(auth()->user()->hasRole('master-admin'))
            <a href="#" onclick="alert('System Settings - Coming Soon!')"
               class="group flex items-center px-4 py-3.5 text-sm font-medium rounded-xl transition-all duration-300 text-gray-300 hover:text-white hover:bg-gray-800/50">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-700/50 group-hover:bg-gray-600/50 transition-all duration-300 {{ app()->getLocale() === 'ar' ? 'ml-3' : 'mr-3' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <span class="font-semibold">System Settings</span>
                <div class="ml-auto">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-700 text-gray-300">
                        Soon
                    </span>
                </div>
            </a>
        @endif
    </div>
@endif
