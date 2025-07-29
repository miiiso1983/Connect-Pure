@props([
    'headers' => [],
    'rows' => [],
    'searchable' => false,
    'sortable' => false,
    'selectable' => false,
    'pagination' => null,
    'emptyMessage' => 'No data available',
    'emptyIcon' => null,
    'loading' => false,
    'striped' => true,
    'hover' => true,
    'compact' => false
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden']) }}>
    @if($searchable || isset($header))
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                @if(isset($header))
                    {{ $header }}
                @endif
                
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
            </div>
        </div>
    @endif
    
    <div class="overflow-x-auto">
        @if($loading)
            <div class="p-12 text-center">
                <div class="inline-flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-gray-600">{{ __('common.loading') }}...</span>
                </div>
            </div>
        @elseif(empty($rows) && empty($slot))
            <div class="p-12 text-center">
                <div class="text-gray-500">
                    @if($emptyIcon)
                        <div class="mx-auto mb-4 w-12 h-12 text-gray-400">
                            {!! $emptyIcon !!}
                        </div>
                    @else
                        <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    @endif
                    <p class="text-lg font-medium text-gray-900">{{ $emptyMessage }}</p>
                </div>
            </div>
        @else
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
                        </tr>
                    </thead>
                @endif
                
                <tbody class="bg-white divide-y divide-gray-200 {{ $striped ? 'divide-y' : '' }}">
                    @if(!empty($rows))
                        @foreach($rows as $index => $row)
                            <tr class="{{ $hover ? 'hover:bg-gray-50' : '' }} {{ $striped && $index % 2 === 1 ? 'bg-gray-25' : '' }}">
                                @if($selectable)
                                    <td class="px-6 py-4 whitespace-nowrap {{ $compact ? 'py-2' : '' }}">
                                        <input type="checkbox" name="selected[]" value="{{ $row['id'] ?? $index }}" class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                @endif
                                
                                @foreach($row as $key => $cell)
                                    @if($key !== 'id' || !$selectable)
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
                            </tr>
                        @endforeach
                    @else
                        {{ $slot }}
                    @endif
                </tbody>
            </table>
        @endif
    </div>
    
    @if($pagination && $pagination->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $pagination->links() }}
        </div>
    @endif
</div>

@if($searchable || $selectable)
    @push('scripts')
    <script>
        @if($searchable)
        // Search functionality
        document.getElementById('table-search')?.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
        @endif
        
        @if($selectable)
        // Select all functionality
        document.getElementById('select-all')?.addEventListener('change', function() {
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
                const selectAll = document.getElementById('select-all');
                
                if (selectAll) {
                    selectAll.checked = allCheckboxes.length === checkedCheckboxes.length;
                    selectAll.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
                }
            });
        });
        @endif
    </script>
    @endpush
@endif
