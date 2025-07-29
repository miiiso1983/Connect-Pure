@props([
    'headers' => [],
    'rows' => [],
    'searchable' => true,
    'sortable' => true,
    'pagination' => null,
    'emptyMessage' => 'No data available',
    'actions' => true,
    'selectable' => false,
    'exportable' => false,
    'compact' => false
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden']) }}>
    <!-- Header with Search and Actions -->
    @if($searchable || $exportable || isset($headerActions))
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex-1">
                    @if(isset($headerActions))
                        {{ $headerActions }}
                    @endif
                </div>
                
                <div class="flex items-center gap-3">
                    @if($searchable)
                        <div class="relative">
                            <input 
                                type="text" 
                                placeholder="{{ __('common.search') }}..." 
                                class="form-input pl-10 pr-4 py-2 w-64"
                                id="table-search"
                            >
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    @endif
                    
                    @if($exportable)
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="btn-secondary flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                {{ __('common.export') }}
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                                <div class="py-1">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('common.export_csv') }}</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('common.export_excel') }}</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('common.export_pdf') }}</a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
    
    <!-- Mobile Card View -->
    <div class="block sm:hidden">
        @if(empty($rows))
            <div class="p-8 text-center">
                <div class="text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-lg font-medium text-gray-900">{{ $emptyMessage }}</p>
                </div>
            </div>
        @else
            <div class="divide-y divide-gray-200">
                @foreach($rows as $index => $row)
                    <div class="p-4 hover:bg-gray-50">
                        @if($selectable)
                            <div class="flex items-start">
                                <input type="checkbox" name="selected[]" value="{{ $row['id'] ?? $index }}" class="row-checkbox mt-1 mr-3">
                                <div class="flex-1">
                        @endif
                        
                        @foreach($headers as $headerIndex => $header)
                            @if(isset($row[array_keys($row)[$headerIndex]]))
                                <div class="mb-2">
                                    <dt class="text-sm font-medium text-gray-500">{{ $header['label'] ?? $header }}</dt>
                                    <dd class="text-sm text-gray-900">{!! $row[array_keys($row)[$headerIndex]] !!}</dd>
                                </div>
                            @endif
                        @endforeach
                        
                        @if($selectable)
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    
    <!-- Desktop Table View -->
    <div class="hidden sm:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            @if(!empty($headers))
                <thead class="bg-gray-50">
                    <tr>
                        @if($selectable)
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                        @endif
                        
                        @foreach($headers as $header)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider {{ $compact ? 'py-2' : '' }}">
                                @if($sortable && isset($header['sortable']) && $header['sortable'])
                                    <button class="group inline-flex items-center hover:text-gray-700">
                                        {{ $header['label'] ?? $header }}
                                        <svg class="ml-2 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                                        </svg>
                                    </button>
                                @else
                                    {{ $header['label'] ?? $header }}
                                @endif
                            </th>
                        @endforeach
                        
                        @if($actions)
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('common.actions') }}
                            </th>
                        @endif
                    </tr>
                </thead>
            @endif
            
            <tbody class="bg-white divide-y divide-gray-200">
                @if(empty($rows))
                    <tr>
                        <td colspan="{{ count($headers) + ($selectable ? 1 : 0) + ($actions ? 1 : 0) }}" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-lg font-medium text-gray-900">{{ $emptyMessage }}</p>
                            </div>
                        </td>
                    </tr>
                @else
                    @foreach($rows as $index => $row)
                        <tr class="hover:bg-gray-50">
                            @if($selectable)
                                <td class="px-6 py-4 whitespace-nowrap {{ $compact ? 'py-2' : '' }}">
                                    <input type="checkbox" name="selected[]" value="{{ $row['id'] ?? $index }}" class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>
                            @endif
                            
                            @foreach($row as $key => $cell)
                                @if($key !== 'actions' && ($key !== 'id' || !$selectable))
                                    <td class="px-6 py-4 whitespace-nowrap {{ $compact ? 'py-2' : '' }}">
                                        @if(is_array($cell))
                                            @if(isset($cell['component']))
                                                @include($cell['component'], $cell['data'] ?? [])
                                            @else
                                                {{ $cell['value'] ?? '' }}
                                            @endif
                                        @else
                                            {!! $cell !!}
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                            
                            @if($actions && isset($row['actions']))
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium {{ $compact ? 'py-2' : '' }}">
                                    {!! $row['actions'] !!}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($pagination && $pagination->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $pagination->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('table-search');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr, .block.sm\\:hidden > div > div');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
    
    // Select all functionality
    const selectAll = document.getElementById('select-all');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        // Update select all when individual checkboxes change
        document.querySelectorAll('.row-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allCheckboxes = document.querySelectorAll('.row-checkbox');
                const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
                
                selectAll.checked = allCheckboxes.length === checkedCheckboxes.length;
                selectAll.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
            });
        });
    }
});
</script>
@endpush
