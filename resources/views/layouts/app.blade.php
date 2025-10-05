<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ffffff">

    <title>{{ config('app.name', 'Connect Pure ERP') }} - @yield('title', __('erp.dashboard'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/heroicons@2.0.18/24/outline/index.css">

    <!-- Additional Styles -->
    @stack('styles')

    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    <!-- Theme Initialization Script (prevents flash) -->
    <script>
        (function() {
            const theme = localStorage.getItem('theme') ||
                         (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>
<body class="font-sans antialiased gradient-primary min-h-screen {{ app()->getLocale() === 'ar' ? 'font-arabic' : '' }}">
    <div class="min-h-screen">
        <!-- Modern Navigation Header -->
        <nav class="modern-header sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <!-- Logo and Mobile Menu Button -->
                    <div class="flex items-center">
                        <!-- Mobile menu button -->
                        <button type="button"
                                class="md:hidden inline-flex items-center justify-center p-3 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200"
                                onclick="toggleSidebar()">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <!-- Modern Logo -->
                        <div class="flex-shrink-0 flex items-center {{ app()->getLocale() === 'ar' ? 'mr-4' : 'ml-4' }} md:ml-0">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h1 class="text-xl font-bold text-gradient">
                                        {{ config('app.name', 'Connect Pure ERP') }}
                                    </h1>
                                    <p class="text-xs text-gray-600 font-medium">Enterprise Solution</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right side navigation -->
                    <div class="flex items-center space-x-6 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <!-- Notifications -->
                        <button class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-white/50 rounded-xl transition-all duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.07 2.82l3.12 3.12M7.05 5.84l3.12 3.12M4.03 8.86l3.12 3.12M1.01 11.88l3.12 3.12"></path>
                            </svg>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>


                        <!-- Global Search -->
                        <div class="hidden md:block w-72">
                            <div class="relative">
                                <input type="text" placeholder="Search…" class="form-input pl-10 pr-3 py-2 w-full" />
                                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Language Switcher -->
                        <div class="relative">
                            <select onchange="switchLanguage(this.value)"
                                    class="appearance-none bg-white/50 backdrop-blur-sm border border-white/30 rounded-xl px-4 py-2 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>🇺🇸 English</option>
                                <option value="ar" {{ app()->getLocale() === 'ar' ? 'selected' : '' }}>🇸🇦 العربية</option>
                            </select>
                        </div>

                        <!-- Theme Toggle -->
                        <x-theme-toggle size="md" variant="button" />

                        <!-- Simple Theme Toggle Button (Fallback) -->
                        <button type="button"
                                onclick="toggleTheme()"
                                class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center transition-all duration-300 ml-2"
                                title="Toggle Theme">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </button>

                        <!-- User Menu -->
                        @auth
                        <div class="relative">
                            <button type="button"
                                    class="flex items-center space-x-3 bg-white/50 backdrop-blur-sm rounded-2xl px-4 py-2 hover:bg-white/70 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200"
                                    onclick="toggleUserMenu()">
                                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center shadow-lg">
                                    <span class="text-white text-sm font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                                </div>
                                <div class="hidden md:block text-left">
                                    <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-gray-600">{{ auth()->user()->roles->first()?->name ?? 'User' }}</div>
                                </div>
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Modern User dropdown menu -->
                            <div id="userMenu" class="hidden absolute {{ app()->getLocale() === 'ar' ? 'left-0' : 'right-0' }} mt-3 w-72 modern-card p-0 z-50 animate-fade-in-up">
                                <!-- User Info Header -->
                                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-t-2xl border-b border-gray-100">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center shadow-lg">
                                            <span class="text-white font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</div>
                                            <div class="text-sm text-gray-600">{{ auth()->user()->email }}</div>
                                            @if(auth()->user()->roles->first())
                                                <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                                    {{ auth()->user()->roles->first()->name }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Menu Items -->
                                <div class="py-2">
                                    <a href="#" class="flex items-center px-6 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 transition-all duration-200">
                                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium">{{ __('erp.profile') }}</div>
                                            <div class="text-xs text-gray-500">Manage your account</div>
                                        </div>
                                    </a>

                                    <a href="#" class="flex items-center px-6 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 transition-all duration-200">
                                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium">{{ __('erp.settings') }}</div>
                                            <div class="text-xs text-gray-500">Preferences & configuration</div>
                                        </div>
                                    </a>

                                    <!-- Theme Toggle in Dropdown -->
                                    <x-theme-toggle variant="dropdown-item" />

                                    <div class="border-t border-gray-100 my-2"></div>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full px-6 py-3 text-sm text-red-600 hover:bg-red-50 transition-all duration-200">
                                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-medium">{{ __('erp.logout') }}</div>
                                                <div class="text-xs text-red-500">Sign out of your account</div>
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900">{{ __('erp.login') }}</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-sm bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700">{{ __('erp.register') }}</a>
                            @endif
                        </div>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex min-h-screen pt-20">
            <!-- Modern Sidebar -->
            <aside id="sidebar" class="fixed inset-y-0 {{ app()->getLocale() === 'ar' ? 'right-0' : 'left-0' }} z-40 w-72 bg-gradient-to-b from-gray-900 via-gray-900 to-gray-800 shadow-2xl transform -translate-x-full md:translate-x-0 transition-all duration-300 ease-in-out md:static md:inset-0">
                <div class="flex flex-col h-full pt-20 md:pt-6">
                    <!-- Dark Sidebar Header -->
                    <div class="flex items-center justify-between p-6 border-b border-gray-700/50 md:hidden">
                        <h2 class="text-xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">Modules</h2>
                        <button onclick="toggleSidebar()" class="p-3 rounded-xl text-gray-400 hover:text-white hover:bg-gray-800/50 transition-all duration-200">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modern Dark Sidebar Navigation -->
                    <nav class="flex-1 px-6 py-6 space-y-3 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-700 scrollbar-track-gray-800">
                        @include('layouts.sidebar')
                    </nav>

                    <!-- Dark Sidebar Footer -->
                    <div class="p-6 border-t border-gray-700/50">
                        <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl p-4 text-center border border-gray-700/30">
                            <div class="text-sm font-semibold text-white">Connect Pure ERP</div>
                            <div class="text-xs text-gray-400 mt-1">v2.0 Enterprise</div>
                            <div class="mt-3">
                                <div class="w-full bg-gray-700 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full shadow-lg" style="width: 85%"></div>
                                </div>
                                <div class="text-xs text-gray-400 mt-1">System Health: 85%</div>
                            </div>
                            <div class="mt-3 flex items-center justify-center space-x-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                <span class="text-xs text-gray-400">Online</span>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Modern Main Content -->
            <main class="flex-1 md:{{ app()->getLocale() === 'ar' ? 'mr-72' : 'ml-72' }} min-h-screen">
                <div class="p-8 max-w-7xl mx-auto">
                    <!-- Content Container -->
                    <div class="animate-fade-in-up">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div id="sidebarOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden" onclick="toggleSidebar()"></div>

    <!-- Scripts -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function toggleUserMenu() {
            const menu = document.getElementById('userMenu');
            menu.classList.toggle('hidden');
        }

        function switchLanguage(locale) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("switch-language") }}';

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';

            const localeInput = document.createElement('input');
            localeInput.type = 'hidden';
            localeInput.name = 'locale';
            localeInput.value = locale;

            form.appendChild(csrfToken);
            form.appendChild(localeInput);
            document.body.appendChild(form);
            form.submit();
        }

        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('userMenu');
            const userButton = event.target.closest('button');

            if (!userButton || !userButton.onclick || userButton.onclick.toString().indexOf('toggleUserMenu') === -1) {
                userMenu.classList.add('hidden');
            }
        });

        // Theme Toggle Functionality
        function toggleTheme() {
            console.log('Theme toggle clicked!'); // Debug log

            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';

            console.log('Current theme:', currentTheme, 'New theme:', newTheme); // Debug log

            // Apply theme
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            console.log('Theme applied to document'); // Debug log

            // Update meta theme color
            const metaThemeColor = document.querySelector('meta[name="theme-color"]');
            if (metaThemeColor) {
                const color = newTheme === 'dark' ? '#0f172a' : '#ffffff';
                metaThemeColor.setAttribute('content', color);
            }

            // Update toggle buttons
            updateThemeToggleButtons(newTheme);

            // Add transition effect
            document.documentElement.style.transition = 'background-color 0.3s ease, color 0.3s ease';
            setTimeout(() => {
                document.documentElement.style.transition = '';
            }, 300);

            // Force a style recalculation
            document.body.offsetHeight;
        }

        function updateThemeToggleButtons(theme) {
            const toggleButtons = document.querySelectorAll('[data-theme-toggle]');
            toggleButtons.forEach(button => {
                const lightIcon = button.querySelector('.theme-icon-light');
                const darkIcon = button.querySelector('.theme-icon-dark');

                if (theme === 'dark') {
                    if (lightIcon) lightIcon.classList.remove('hidden');
                    if (darkIcon) darkIcon.classList.add('hidden');
                    button.setAttribute('aria-label', 'Switch to light mode');
                    button.setAttribute('title', 'Switch to light mode');
                } else {
                    if (lightIcon) lightIcon.classList.add('hidden');
                    if (darkIcon) darkIcon.classList.remove('hidden');
                    button.setAttribute('aria-label', 'Switch to dark mode');
                    button.setAttribute('title', 'Switch to dark mode');
                }
            });
        }

        // Initialize theme toggle buttons
        document.addEventListener('DOMContentLoaded', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            updateThemeToggleButtons(currentTheme);

            // Add click event listeners to theme toggle buttons
            document.addEventListener('click', function(e) {
                if (e.target.closest('[data-theme-toggle]')) {
                    e.preventDefault();
                    toggleTheme();
                }
            });

            // Keyboard shortcut (Ctrl/Cmd + Shift + T)
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'T') {
                    e.preventDefault();
                    toggleTheme();
                }
            });
        });
    </script>

    <!-- Floating Theme Toggle (Optional) -->
    <!-- Uncomment to enable floating theme toggle -->
    <!-- <x-theme-toggle variant="floating" /> -->

    @stack('scripts')
</body>
</html>
