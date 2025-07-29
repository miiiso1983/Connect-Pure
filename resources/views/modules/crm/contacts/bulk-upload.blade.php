@extends('layouts.app')

@section('title', __('erp.bulk_upload_contacts'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('erp.bulk_upload_contacts') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('erp.upload_file') }} {{ __('erp.contacts') }}</p>
        </div>
        <a href="{{ route('modules.crm.contacts.index') }}" class="btn-secondary">
            {{ __('erp.back') }}
        </a>
    </div>

    <!-- Upload Form -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upload Section -->
        <x-card title="{{ __('erp.upload_file') }}">
            <form action="{{ route('modules.crm.contacts.bulk-upload.process') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                
                <div class="space-y-6">
                    <!-- File Input -->
                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('erp.choose_file') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition-colors duration-200">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>{{ __('erp.upload_file') }}</span>
                                        <input id="file" name="file" type="file" class="sr-only" accept=".xlsx,.xls,.csv" required onchange="updateFileName(this)">
                                    </label>
                                    <p class="{{ app()->getLocale() === 'ar' ? 'pr-1' : 'pl-1' }}">{{ __('erp.or_drag_and_drop') }}</p>
                                </div>
                                <p class="text-xs text-gray-500">
                                    Excel (.xlsx, .xls) {{ __('erp.or') }} CSV {{ __('erp.files_only') }}
                                </p>
                                <p id="fileName" class="text-sm text-blue-600 font-medium hidden"></p>
                            </div>
                        </div>
                        @error('file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Upload Button -->
                    <div class="flex items-center justify-between">
                        <a href="{{ route('modules.crm.contacts.download-template') }}" class="btn-secondary">
                            <svg class="w-4 h-4 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            {{ __('erp.download_template') }}
                        </a>
                        
                        <button type="submit" class="btn-primary" id="uploadBtn">
                            <svg class="w-4 h-4 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            {{ __('erp.upload_contacts') }}
                        </button>
                    </div>
                </div>
            </form>

            <!-- Progress Bar (Hidden by default) -->
            <div id="uploadProgress" class="hidden mt-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-blue-800 font-medium">{{ __('erp.processing_file') }}</span>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Instructions Section -->
        <x-card title="{{ __('erp.upload_instructions') }}">
            <div class="space-y-6">
                <!-- Template Download -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-blue-800 mb-2">{{ __('erp.download_template') }}</h3>
                    <p class="text-sm text-blue-700 mb-3">{{ __('erp.template_description') }}</p>
                    <a href="{{ route('modules.crm.contacts.download-template') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                        <svg class="w-4 h-4 {{ app()->getLocale() === 'ar' ? 'ml-1' : 'mr-1' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        contacts_template.xlsx
                    </a>
                </div>

                <!-- File Requirements -->
                <div>
                    <h3 class="text-sm font-medium text-gray-900 mb-3">{{ __('erp.file_requirements') }}</h3>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>{{ __('erp.supported_formats') }}: Excel (.xlsx, .xls), CSV</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>{{ __('erp.max_file_size') }}: 10MB</span>
                        </div>
                    </div>
                </div>

                <!-- Upload Guidelines -->
                <div>
                    <h3 class="text-sm font-medium text-gray-900 mb-3">{{ __('erp.upload_guidelines_title') }}</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        @foreach(__('erp.upload_guidelines') as $guideline)
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-blue-500 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }} mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ $guideline }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Error Display -->
    @if(session('failures'))
        <x-card title="{{ __('erp.upload_errors') }}" color="red-50">
            <div class="space-y-2">
                @foreach(session('failures') as $failure)
                    <div class="text-sm text-red-600">
                        <strong>{{ __('erp.row') }} {{ $failure->row() }}:</strong>
                        @foreach($failure->errors() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif
</div>

@push('scripts')
<script>
function updateFileName(input) {
    const fileName = document.getElementById('fileName');
    if (input.files && input.files[0]) {
        fileName.textContent = input.files[0].name;
        fileName.classList.remove('hidden');
    } else {
        fileName.classList.add('hidden');
    }
}

document.getElementById('uploadForm').addEventListener('submit', function() {
    document.getElementById('uploadBtn').disabled = true;
    document.getElementById('uploadProgress').classList.remove('hidden');
});
</script>
@endpush
@endsection
