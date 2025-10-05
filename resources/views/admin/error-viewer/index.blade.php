@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="modern-card p-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">System Error Viewer</h1>
                <p class="text-sm text-gray-500">Only visible to master-admin/top_management. Log: <span class="font-mono text-xs">{{ $path }}</span></p>
            </div>
            <div class="flex items-center gap-2">
                <form method="GET" class="flex items-center gap-2">
                    <input type="text" name="q" value="{{ $query }}" placeholder="Search in errors..." class="form-input w-64" />
                    <button class="btn-primary" type="submit">Search</button>
                </form>
                <a href="{{ route('admin.system-errors.download') }}" class="btn-secondary">Download Log</a>
                <form method="POST" action="{{ route('admin.system-errors.clear') }}" onsubmit="return confirm('Clear laravel.log?');">
                    @csrf
                    <button class="btn-danger" type="submit">Clear Log</button>
                </form>
            </div>
        </div>
    </div>

    <div class="modern-card p-0 mt-6">
        <div class="overflow-x-auto">
            <table class="modern-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Time</th>
                        <th class="text-left">Env</th>
                        <th class="text-left">Level</th>
                        <th class="text-left">Summary</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $e)
                        <tr class="align-top">
                            <td class="whitespace-nowrap text-sm text-gray-500">{{ $e['timestamp'] }}</td>
                            <td class="whitespace-nowrap"><x-badge variant="info" size="sm">{{ $e['env'] }}</x-badge></td>
                            <td class="whitespace-nowrap">
                                @php $variant = match(strtolower($e['level'])) {
                                    'error' => 'danger', 'critical' => 'danger', 'warning' => 'warning', default => 'secondary'
                                }; @endphp
                                <x-badge :variant="$variant" size="sm">{{ strtoupper($e['level']) }}</x-badge>
                            </td>
                            <td class="text-sm">{{ Str::limit($e['summary'], 160) }}</td>
                            <td class="text-right">
                                <details>
                                    <summary class="cursor-pointer text-primary-600 hover:underline">Details</summary>
                                    <pre class="mt-2 p-3 bg-gray-50 dark:bg-slate-800 rounded-lg overflow-auto text-xs leading-5">{{ $e['summary'] }}

{{ $e['body'] }}</pre>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-6 text-center text-gray-500">No log entries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

