@extends('layouts.app')

@section('title', 'WhatsApp Logs')

@section('content')
<div class="space-y-6">
    <div class="modern-card p-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">WhatsApp Message Logs</h1>
        <form method="GET" class="flex items-center gap-2">
            <input type="text" name="message_id" value="{{ request('message_id') }}" placeholder="Message ID" class="form-input" />
            <input type="number" name="invoice_id" value="{{ request('invoice_id') }}" placeholder="Invoice ID" class="form-input w-32" />
            <select name="status" class="form-select">
                <option value="">All statuses</option>
                @foreach(['sent','delivered','read','failed'] as $s)
                    <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button class="btn btn-outline" type="submit">Filter</button>
        </form>
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Message ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payload</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm">{{ $log->id }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($log->invoice_id)
                                    <a class="text-blue-600" href="{{ route('modules.accounting.invoices.show', $log->invoice_id) }}">#{{ $log->invoice_id }}</a>
                                @else
                                    &mdash;
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $log->message_id ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100">{{ $log->status ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $log->created_at }}</td>
                            <td class="px-6 py-4 text-xs text-gray-600">
                                <details>
                                    <summary class="cursor-pointer text-blue-600">View</summary>
                                    <pre class="mt-2 max-w-3xl whitespace-pre-wrap break-words">{{ json_encode($log->payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-6 text-center text-gray-500">No logs found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </x-card>
</div>
@endsection

