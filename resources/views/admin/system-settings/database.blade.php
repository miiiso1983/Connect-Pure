@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="modern-card p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ __('Database Information') }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ __('View database connection and statistics') }}</p>
            </div>
            <a href="{{ route('admin.system-settings.index') }}" class="btn-secondary">
                {{ __('Back to Settings') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Connection Info -->
        <div class="modern-card p-6">
            <h2 class="text-lg font-semibold mb-4">{{ __('Connection Information') }}</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Driver') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ config('database.default') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Host') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ config('database.connections.' . config('database.default') . '.host') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Port') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ config('database.connections.' . config('database.default') . '.port') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Database') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ config('database.connections.' . config('database.default') . '.database') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Username') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ config('database.connections.' . config('database.default') . '.username') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Database Stats -->
        <div class="modern-card p-6">
            <h2 class="text-lg font-semibold mb-4">{{ __('Database Statistics') }}</h2>
            <dl class="space-y-3">
                @php
                    try {
                        $tables = \DB::select('SHOW TABLES');
                        $tableCount = count($tables);
                        
                        $dbName = config('database.connections.' . config('database.default') . '.database');
                        $sizeQuery = \DB::select("
                            SELECT 
                                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                            FROM information_schema.TABLES 
                            WHERE table_schema = ?
                        ", [$dbName]);
                        $dbSize = $sizeQuery[0]->size_mb ?? 0;
                        
                        $connectionOk = true;
                    } catch (\Exception $e) {
                        $tableCount = 'N/A';
                        $dbSize = 'N/A';
                        $connectionOk = false;
                    }
                @endphp
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Connection Status') }}</dt>
                    <dd class="mt-1">
                        @if($connectionOk)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ __('Connected') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ __('Disconnected') }}
                            </span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Total Tables') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $tableCount }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ __('Database Size') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $dbSize }} MB</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Backup & Maintenance -->
    <div class="modern-card p-6 mt-6">
        <h2 class="text-lg font-semibold mb-4">{{ __('Database Maintenance') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button type="button" class="btn-secondary" onclick="alert('Backup functionality coming soon')">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
                {{ __('Backup Database') }}
            </button>
            <button type="button" class="btn-secondary" onclick="alert('Optimize functionality coming soon')">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                {{ __('Optimize Tables') }}
            </button>
            <button type="button" class="btn-secondary" onclick="alert('Repair functionality coming soon')">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                {{ __('Repair Tables') }}
            </button>
        </div>
    </div>
</div>
@endsection

