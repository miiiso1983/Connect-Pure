@extends('layouts.app')

@section('title', __('accounting.create_invoice'))

@section('content')
<div class="space-y-8">
    <!-- Professional Header -->
    <div class="relative">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600/10 via-purple-600/10 to-pink-600/10 rounded-3xl"></div>
        <div class="relative modern-card p-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-2xl">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold text-gradient mb-2">{{ __('accounting.create_invoice') }}</h1>
                        <p class="text-lg text-gray-600 font-medium">Create a professional invoice for your customer</p>
                        <div class="flex items-center mt-3 space-x-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Auto-save enabled
                            </div>
                            <div class="flex items-center text-sm text-green-600">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                                Draft mode
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('modules.accounting.invoices.index') }}" class="btn btn-outline">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('accounting.back_to_invoices') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="modern-card p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold">1</div>
                    <span class="font-semibold text-gray-900">Invoice Details</span>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <div class="flex items-center space-x-2 px-4 py-2 bg-gray-50 rounded-xl">
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-white text-sm font-bold">2</div>
                    <span class="font-semibold text-gray-500">Line Items</span>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <div class="flex items-center space-x-2 px-4 py-2 bg-gray-50 rounded-xl">
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-white text-sm font-bold">3</div>
                    <span class="font-semibold text-gray-500">Review & Send</span>
                </div>
            </div>
            <div class="text-sm text-gray-500">
                Step 1 of 3
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <form method="POST" action="{{ route('modules.accounting.invoices.store') }}" id="invoiceForm">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Form Fields -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Invoice Details Section -->
                <x-card title="{{ __('accounting.invoice_details') }}" 
                        subtitle="Basic invoice information and customer details"
                        gradient="true"
                        icon='<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Customer Selection -->
                        <div class="md:col-span-2">
                            <label for="customer_id" class="form-label">
                                {{ __('accounting.customer') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="customer_id" id="customer_id" class="form-input @error('customer_id') border-red-300 @enderror" required>
                                <option value="">{{ __('accounting.select_customer') }}</option>
                                @foreach($customers ?? [] as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} @if($customer->company_name) - {{ $customer->company_name }} @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Invoice Number -->
                        <div>
                            <label for="invoice_number" class="form-label">
                                {{ __('accounting.invoice_number') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="invoice_number" 
                                   id="invoice_number" 
                                   class="form-input @error('invoice_number') border-red-300 @enderror"
                                   value="{{ old('invoice_number', $nextInvoiceNumber ?? 'INV-' . date('Y') . '-' . str_pad(1, 4, '0', STR_PAD_LEFT)) }}" 
                                   required>
                            @error('invoice_number')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Invoice Date -->
                        <div>
                            <label for="invoice_date" class="form-label">
                                {{ __('accounting.invoice_date') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="invoice_date" 
                                   id="invoice_date" 
                                   class="form-input @error('invoice_date') border-red-300 @enderror"
                                   value="{{ old('invoice_date', date('Y-m-d')) }}" 
                                   required>
                            @error('invoice_date')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Due Date -->
                        <div>
                            <label for="due_date" class="form-label">
                                {{ __('accounting.due_date') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="due_date" 
                                   id="due_date" 
                                   class="form-input @error('due_date') border-red-300 @enderror"
                                   value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" 
                                   required>
                            @error('due_date')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- PO Number -->
                        <div>
                            <label for="po_number" class="form-label">
                                {{ __('accounting.po_number') }}
                            </label>
                            <input type="text" 
                                   name="po_number" 
                                   id="po_number" 
                                   class="form-input @error('po_number') border-red-300 @enderror"
                                   value="{{ old('po_number') }}" 
                                   placeholder="Optional">
                            @error('po_number')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-card>

                <!-- Invoice Items Section -->
                <x-card title="{{ __('accounting.invoice_items') }}" 
                        subtitle="Add products or services to your invoice"
                        gradient="true"
                        icon='<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>'>
                    
                    <!-- Items Table -->
                    <div class="modern-table overflow-hidden">
                        <table class="w-full" id="invoiceItemsTable">
                            <thead>
                                <tr>
                                    <th class="text-left py-4 px-6 font-bold text-gray-700 uppercase text-xs">Description</th>
                                    <th class="text-center py-4 px-4 font-bold text-gray-700 uppercase text-xs w-24">Qty</th>
                                    <th class="text-right py-4 px-4 font-bold text-gray-700 uppercase text-xs w-32">Unit Price</th>
                                    <th class="text-right py-4 px-4 font-bold text-gray-700 uppercase text-xs w-24">Tax %</th>
                                    <th class="text-right py-4 px-4 font-bold text-gray-700 uppercase text-xs w-32">Total</th>
                                    <th class="text-center py-4 px-4 font-bold text-gray-700 uppercase text-xs w-16">Action</th>
                                </tr>
                            </thead>
                            <tbody id="invoiceItemsBody">
                                <!-- Dynamic rows will be added here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Add Item Button -->
                    <div class="mt-6 flex justify-center">
                        <button type="button" id="addItemBtn" class="btn btn-success">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Item
                        </button>
                    </div>
                </x-card>

                <!-- Additional Information Section -->
                <x-card title="Additional Information" 
                        subtitle="Optional invoice details and terms"
                        gradient="true"
                        icon='<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'>
                    
                    <div class="space-y-6">
                        <!-- Notes -->
                        <div>
                            <label for="notes" class="form-label">
                                Notes
                            </label>
                            <textarea name="notes" 
                                      id="notes" 
                                      rows="4"
                                      class="form-input @error('notes') border-red-300 @enderror"
                                      placeholder="Internal notes (not visible to customer)">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Terms -->
                        <div>
                            <label for="terms" class="form-label">
                                Terms & Conditions
                            </label>
                            <textarea name="terms" 
                                      id="terms" 
                                      rows="3"
                                      class="form-input @error('terms') border-red-300 @enderror"
                                      placeholder="Payment terms and conditions">{{ old('terms') }}</textarea>
                            @error('terms')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Right Column - Invoice Summary -->
            <div class="space-y-8">
                <!-- Invoice Summary Card -->
                <div class="modern-card bg-gradient-to-br from-blue-600 to-purple-600 text-white sticky top-8">
                    <div class="p-8">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold">Invoice Summary</h3>
                                <p class="text-white/80 text-sm">Real-time calculation</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-white/20">
                                <span class="text-white/90">Subtotal:</span>
                                <span class="font-bold" id="subtotalDisplay">$0.00</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-white/20">
                                <span class="text-white/90">Tax:</span>
                                <span class="font-bold" id="taxDisplay">$0.00</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-t-2 border-white/30 text-lg">
                                <span class="font-bold">Total:</span>
                                <span class="font-bold text-2xl" id="totalDisplay">$0.00</span>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="mt-8 grid grid-cols-2 gap-4">
                            <div class="bg-white/10 rounded-xl p-4 text-center">
                                <div class="text-2xl font-bold" id="itemCount">0</div>
                                <div class="text-white/80 text-sm">Items</div>
                            </div>
                            <div class="bg-white/10 rounded-xl p-4 text-center">
                                <div class="text-2xl font-bold" id="daysUntilDue">30</div>
                                <div class="text-white/80 text-sm">Days</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="modern-card p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <button type="button" class="w-full btn btn-outline text-left">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Preview Invoice
                        </button>
                        <button type="button" class="w-full btn btn-outline text-left">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                            </svg>
                            Save as Draft
                        </button>
                        <button type="button" class="w-full btn btn-outline text-left">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Duplicate Invoice
                        </button>
                    </div>
                </div>

                <!-- Help & Tips -->
                <div class="modern-card p-6 bg-gradient-to-br from-green-50 to-blue-50">
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mr-3 flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Pro Tips</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Use clear item descriptions</li>
                                <li>• Set appropriate payment terms</li>
                                <li>• Include your business details</li>
                                <li>• Review before sending</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="modern-card p-8 mt-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="send_email" name="send_email" class="form-checkbox h-4 w-4 text-blue-600 rounded">
                        <label for="send_email" class="ml-2 text-sm text-gray-700">Send email notification to customer</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="send_whatsapp" name="send_whatsapp" class="form-checkbox h-4 w-4 text-green-600 rounded">
                        <label for="send_whatsapp" class="ml-2 text-sm text-gray-700">Send WhatsApp notification</label>
                    </div>
                </div>

                <div class="flex space-x-4">
                    <button type="button" class="btn btn-outline">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Save as Draft
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Create & Send Invoice
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- JavaScript for Dynamic Invoice Items -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemCount = 0;

    // Add item functionality
    document.getElementById('addItemBtn').addEventListener('click', function() {
        addInvoiceItem();
    });

    function addInvoiceItem() {
        itemCount++;
        const tbody = document.getElementById('invoiceItemsBody');
        const row = document.createElement('tr');
        row.className = 'border-b border-gray-100 hover:bg-gray-50';
        row.innerHTML = `
            <td class="py-4 px-6">
                <input type="text" name="items[${itemCount}][description]"
                       class="form-input w-full"
                       placeholder="Item description" required>
            </td>
            <td class="py-4 px-4">
                <input type="number" name="items[${itemCount}][quantity]"
                       class="form-input w-full text-center"
                       value="1" min="1" step="0.01" required>
            </td>
            <td class="py-4 px-4">
                <input type="number" name="items[${itemCount}][unit_price]"
                       class="form-input w-full text-right"
                       value="0.00" min="0" step="0.01" required>
            </td>
            <td class="py-4 px-4">
                <input type="number" name="items[${itemCount}][tax_rate]"
                       class="form-input w-full text-right"
                       value="0" min="0" max="100" step="0.01">
            </td>
            <td class="py-4 px-4">
                <div class="text-right font-bold text-gray-900">$0.00</div>
            </td>
            <td class="py-4 px-4 text-center">
                <button type="button" class="remove-item-btn" onclick="removeItem(this)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </td>
        `;
        tbody.appendChild(row);
        updateItemCount();
        calculateTotals();
    }

    function removeItem(button) {
        button.closest('tr').remove();
        updateItemCount();
        calculateTotals();
    }

    function updateItemCount() {
        const count = document.querySelectorAll('#invoiceItemsBody tr').length;
        document.getElementById('itemCount').textContent = count;
    }

    function calculateTotals() {
        // Add calculation logic here
        // This is a simplified version
        document.getElementById('subtotalDisplay').textContent = '$0.00';
        document.getElementById('taxDisplay').textContent = '$0.00';
        document.getElementById('totalDisplay').textContent = '$0.00';
    }

    // Add first item by default
    addInvoiceItem();

    // Make removeItem function global
    window.removeItem = removeItem;
});
</script>

<style>
.remove-item-btn {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    border: none;
    border-radius: 8px;
    padding: 0.5rem;
    color: white;
    transition: all 0.3s ease;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.remove-item-btn:hover {
    transform: scale(1.1);
    color: white;
}

.form-checkbox:checked {
    background-color: currentColor;
    border-color: currentColor;
}
</style>
@endsection
