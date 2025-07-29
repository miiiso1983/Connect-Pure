@extends('layouts.app')

@section('title', __('accounting.bulk_upload_customers'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('accounting.bulk_upload_customers') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('accounting.upload_multiple_customers_excel') }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.accounting.customers.index') }}" class="btn-secondary">
                {{ __('accounting.back_to_customers') }}
            </a>
            <a href="{{ route('modules.accounting.customers.download-template') }}" class="btn-outline">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                {{ __('accounting.download_template') }}
            </a>
        </div>
    </div>

    <!-- Instructions -->
    <x-card title="{{ __('accounting.upload_instructions') }}">
        <div class="space-y-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="font-medium text-blue-900">{{ __('accounting.before_uploading') }}</h4>
                        <p class="text-blue-700 text-sm mt-1">{{ __('accounting.download_template_first') }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">{{ __('accounting.file_requirements') }}</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('accounting.excel_csv_format') }}
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('accounting.max_file_size_2mb') }}
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('accounting.first_row_headers') }}
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-medium text-gray-900 mb-3">{{ __('accounting.required_fields') }}</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                            {{ __('accounting.name') }} *
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                            {{ __('accounting.currency') }} *
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                            {{ __('accounting.payment_terms') }} *
                        </li>
                        <li class="text-xs text-gray-500 mt-2">
                            * {{ __('accounting.required_fields_note') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </x-card>

    <!-- Upload Form -->
    <x-card title="{{ __('accounting.upload_file') }}">
        <form action="{{ route('modules.accounting.customers.process-bulk-upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div>
                <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('accounting.select_excel_file') }}
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                <span>{{ __('accounting.upload_file') }}</span>
                                <input id="file" name="file" type="file" class="sr-only" accept=".xlsx,.xls,.csv" required>
                            </label>
                            <p class="pl-1">{{ __('accounting.or_drag_and_drop') }}</p>
                        </div>
                        <p class="text-xs text-gray-500">
                            {{ __('accounting.xlsx_xls_csv_up_to_2mb') }}
                        </p>
                    </div>
                </div>
                @error('file')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                <a href="{{ route('modules.accounting.customers.index') }}" class="btn-secondary">
                    {{ __('accounting.cancel') }}
                </a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    {{ __('accounting.upload_customers') }}
                </button>
            </div>
        </form>
    </x-card>

    <!-- Sample Data Preview -->
    <x-card title="{{ __('accounting.sample_data_format') }}">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('accounting.name') }}</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('accounting.company_name') }}</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('accounting.email') }}</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('accounting.phone') }}</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('accounting.currency') }}</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('accounting.payment_terms') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-3 py-2 text-sm text-gray-900">John Doe</td>
                        <td class="px-3 py-2 text-sm text-gray-900">Doe Enterprises</td>
                        <td class="px-3 py-2 text-sm text-gray-900">john@example.com</td>
                        <td class="px-3 py-2 text-sm text-gray-900">+1234567890</td>
                        <td class="px-3 py-2 text-sm text-gray-900">USD</td>
                        <td class="px-3 py-2 text-sm text-gray-900">net_30</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="px-3 py-2 text-sm text-gray-900">Jane Smith</td>
                        <td class="px-3 py-2 text-sm text-gray-900">Smith Corp</td>
                        <td class="px-3 py-2 text-sm text-gray-900">jane@smith.com</td>
                        <td class="px-3 py-2 text-sm text-gray-900">+1987654321</td>
                        <td class="px-3 py-2 text-sm text-gray-900">EUR</td>
                        <td class="px-3 py-2 text-sm text-gray-900">net_15</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-sm text-gray-600">
            <p>{{ __('accounting.download_template_complete_format') }}</p>
        </div>
    </x-card>
</div>

<script>
// File upload preview
document.getElementById('file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const fileName = file.name;
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        
        // Update the upload area to show selected file
        const uploadArea = e.target.closest('.border-dashed');
        uploadArea.innerHTML = `
            <div class="space-y-1 text-center">
                <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm text-gray-900">
                    <span class="font-medium">${fileName}</span>
                    <span class="text-gray-500">(${fileSize} MB)</span>
                </div>
                <button type="button" onclick="clearFile()" class="text-sm text-blue-600 hover:text-blue-500">
                    {{ __('accounting.choose_different_file') }}
                </button>
            </div>
        `;
    }
});

function clearFile() {
    document.getElementById('file').value = '';
    location.reload();
}
</script>
@endsection
