<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" {{ app()->getLocale() === 'ar' ? 'dir=rtl' : '' }}>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('erp.login') }} - {{ config('app.name', 'Connect Pure ERP') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased gradient-primary">
    <div class="min-h-screen flex">
        <!-- Left Side - Branding -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-600 to-indigo-700 relative overflow-hidden">
            <div class="absolute inset-0 bg-black opacity-20"></div>
            <div class="relative z-10 flex flex-col justify-center items-center text-white p-12">
                <div class="text-center">
                    <h1 class="text-4xl font-bold mb-4">{{ config('app.name', 'Connect Pure ERP') }}</h1>
                    <p class="text-xl mb-8 text-blue-100">{{ __('erp.welcome_back') }}</p>
                    <div class="grid grid-cols-2 gap-6 text-sm">
                        <div class="text-center">
                            <div class="bg-white bg-opacity-20 rounded-lg p-4 mb-2">
                                <svg class="w-8 h-8 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                            <div class="font-medium">{{ __('erp.hr') }}</div>
                            <div class="text-blue-200 text-xs">{{ __('erp.employee_management') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="bg-white bg-opacity-20 rounded-lg p-4 mb-2">
                                <svg class="w-8 h-8 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 4v4h4V4h-4zm-2-2h8v8h-8V2zM2 10v4h4v-4H2zm-2-2h8v8H0V8zm16 8v4h4v-4h-4zm-2-2h8v8h-8v-8zM2 20v4h4v-4H2zm-2-2h8v8H0v-8z"/>
                                </svg>
                            </div>
                            <div class="font-medium">{{ __('erp.crm') }}</div>
                            <div class="text-blue-200 text-xs">{{ __('erp.customer_management') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="bg-white bg-opacity-20 rounded-lg p-4 mb-2">
                                <svg class="w-8 h-8 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                </svg>
                            </div>
                            <div class="font-medium">{{ __('erp.accounting') }}</div>
                            <div class="text-blue-200 text-xs">{{ __('erp.financial_management') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="bg-white bg-opacity-20 rounded-lg p-4 mb-2">
                                <svg class="w-8 h-8 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                                </svg>
                            </div>
                            <div class="font-medium">{{ __('erp.performance') }}</div>
                            <div class="text-blue-200 text-xs">{{ __('erp.productivity_tracking') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Modern Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <div class="flex items-center justify-center space-x-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold text-gradient">{{ config('app.name', 'Connect Pure ERP') }}</h1>
                    </div>
                    <p class="text-gray-600 font-medium">{{ __('erp.sign_in_to_continue') }}</p>
                </div>

                <!-- Modern Login Card -->
                <div class="modern-card backdrop-blur-md">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-2xl mx-auto mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-gradient mb-2">{{ __('erp.welcome_back') }}</h2>
                        <p class="text-gray-600 font-medium">{{ __('erp.sign_in_to_continue') }}</p>
                    </div>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm font-medium text-green-800">{{ session('status') }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-6">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('erp.email_address') }}
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                    </svg>
                                </div>
                                <input id="email"
                                       type="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       required
                                       autofocus
                                       autocomplete="username"
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('email') border-red-300 @enderror"
                                       placeholder="Enter your email address">
                            </div>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-6">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('erp.password') }}
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input id="password"
                                       type="password"
                                       name="password"
                                       required
                                       autocomplete="current-password"
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('password') border-red-300 @enderror"
                                       placeholder="Enter your password">
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between mb-6">
                            <label for="remember_me" class="flex items-center">
                                <input id="remember_me"
                                       type="checkbox"
                                       name="remember"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-600">{{ __('erp.remember_me') }}</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                   class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    {{ __('erp.forgot_password') }}
                                </a>
                            @endif
                        </div>

                        <!-- Login Button -->
                        <button type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 px-4 rounded-lg font-medium hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 transform hover:scale-[1.02]"
                                id="loginButton">
                            <span id="loginText">{{ __('erp.sign_in') }}</span>
                            <span id="loginLoading" class="hidden">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('erp.signing_in') }}
                            </span>
                        </button>

                        <!-- Register Link -->
                        @if (Route::has('register'))
                            <div class="text-center mt-6 pt-6 border-t border-gray-200">
                                <p class="text-sm text-gray-600">
                                    {{ __('erp.dont_have_account') }}
                                    <a href="{{ route('register') }}"
                                       class="font-medium text-blue-600 hover:text-blue-800">
                                        {{ __('erp.register_here') }}
                                    </a>
                                </p>
                            </div>
                        @endif
                    </form>

                    <!-- Demo Accounts -->
                    <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Demo Accounts:</h3>
                        <div class="space-y-2 text-xs text-gray-600">
                            <div class="flex justify-between">
                                <span class="font-medium text-blue-700">Master Admin:</span>
                                <span class="font-mono">mustafaalrawan@gmail.com / admin123</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-green-700">HR Manager:</span>
                                <span class="font-mono">hr@connectpure.com / password</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-purple-700">Accounting:</span>
                                <span class="font-mono">accounting@connectpure.com / password</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-orange-700">System Admin:</span>
                                <span class="font-mono">admin@connectpure.com / password</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-3 italic">
                            Each account will redirect to their respective module after login
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function() {
            const button = document.getElementById('loginButton');
            const text = document.getElementById('loginText');
            const loading = document.getElementById('loginLoading');

            button.disabled = true;
            text.classList.add('hidden');
            loading.classList.remove('hidden');
        });
    </script>
</body>
</html>
