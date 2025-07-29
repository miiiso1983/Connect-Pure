@extends('layouts.app')

@section('title', __('accounting.record_expense'))

@push('styles')
<style>
    .expense-header {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        border-radius: 20px;
        color: white;
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    
    .expense-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        transform: rotate(45deg);
    }
    
    .modern-form-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: none;
        overflow: hidden;
        margin-bottom: 2rem;
    }
    
    .form-section-header {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 1.5rem;
        border: none;
    }
    
    .modern-form-control {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #f8fafc;
    }
    
    .modern-form-control:focus {
        border-color: #fa709a;
        box-shadow: 0 0 0 3px rgba(250, 112, 154, 0.1);
        background: white;
    }
    
    .modern-form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }
    
    .modern-btn-primary {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        border: none;
        border-radius: 12px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(250, 112, 154, 0.3);
    }
    
    .modern-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(250, 112, 154, 0.4);
    }
    
    .modern-btn-secondary {
        background: #f1f5f9;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        color: #64748b;
        transition: all 0.3s ease;
    }
    
    .modern-btn-secondary:hover {
        background: #e2e8f0;
        color: #475569;
        transform: translateY(-1px);
    }
    
    .form-section {
        padding: 2rem;
    }
    
    .section-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
        margin: 1.5rem 0;
    }
    
    .expense-summary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 1.5rem;
        color: white;
    }
    
    .file-upload-area {
        border: 2px dashed #e2e8f0;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        background: #f8fafc;
    }
    
    .file-upload-area:hover {
        border-color: #fa709a;
        background: white;
    }
    
    .file-upload-area.dragover {
        border-color: #fa709a;
        background: rgba(250, 112, 154, 0.1);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Modern Header -->
    <div class="expense-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="h2 mb-2 fw-bold">{{ __('accounting.record_expense') }}</h1>
                <p class="mb-0 opacity-75">{{ __('accounting.record_new_expense_description') }}</p>
            </div>
            <div class="col-lg-4 text-end">
                <a href="{{ route('modules.accounting.expenses.index') }}" class="modern-btn-secondary text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i>{{ __('accounting.back_to_expenses') }}
                </a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('modules.accounting.expenses.store') }}" enctype="multipart/form-data" id="expenseForm">
        @csrf
        
        <!-- Expense Details -->
        <div class="modern-form-card">
            <div class="form-section-header">
                <h5 class="mb-1 fw-bold">{{ __('accounting.expense_details') }}</h5>
                <p class="mb-0 opacity-75" style="font-size: 0.875rem;">{{ __('accounting.basic_expense_information') }}</p>
            </div>
            
            <div class="form-section">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="vendor_id" class="modern-form-label">{{ __('accounting.vendor') }}</label>
                        <select class="modern-form-control @error('vendor_id') is-invalid @enderror" id="vendor_id" name="vendor_id">
                            <option value="">{{ __('accounting.select_vendor') }}</option>
                            @foreach($vendors ?? [] as $vendor)
                                <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->name }} {{ $vendor->company_name ? '(' . $vendor->company_name . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('vendor_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="expense_date" class="modern-form-label">{{ __('accounting.expense_date') }} <span class="text-danger">*</span></label>
                        <input type="date" class="modern-form-control @error('expense_date') is-invalid @enderror" 
                               id="expense_date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required>
                        @error('expense_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="category" class="modern-form-label">{{ __('accounting.category') }} <span class="text-danger">*</span></label>
                        <select class="modern-form-control @error('category') is-invalid @enderror" id="category" name="category" required>
                            <option value="">{{ __('accounting.select_category') }}</option>
                            <option value="office_supplies" {{ old('category') == 'office_supplies' ? 'selected' : '' }}>{{ __('accounting.office_supplies') }}</option>
                            <option value="travel" {{ old('category') == 'travel' ? 'selected' : '' }}>{{ __('accounting.travel') }}</option>
                            <option value="meals" {{ old('category') == 'meals' ? 'selected' : '' }}>{{ __('accounting.meals') }}</option>
                            <option value="utilities" {{ old('category') == 'utilities' ? 'selected' : '' }}>{{ __('accounting.utilities') }}</option>
                            <option value="rent" {{ old('category') == 'rent' ? 'selected' : '' }}>{{ __('accounting.rent') }}</option>
                            <option value="insurance" {{ old('category') == 'insurance' ? 'selected' : '' }}>{{ __('accounting.insurance') }}</option>
                            <option value="marketing" {{ old('category') == 'marketing' ? 'selected' : '' }}>{{ __('accounting.marketing') }}</option>
                            <option value="professional_services" {{ old('category') == 'professional_services' ? 'selected' : '' }}>{{ __('accounting.professional_services') }}</option>
                            <option value="equipment" {{ old('category') == 'equipment' ? 'selected' : '' }}>{{ __('accounting.equipment') }}</option>
                            <option value="software" {{ old('category') == 'software' ? 'selected' : '' }}>{{ __('accounting.software') }}</option>
                            <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>{{ __('accounting.other') }}</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="amount" class="modern-form-label">{{ __('accounting.amount') }} <span class="text-danger">*</span></label>
                        <input type="number" class="modern-form-control @error('amount') is-invalid @enderror" 
                               id="amount" name="amount" value="{{ old('amount') }}" min="0" step="0.01" required placeholder="0.00">
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="payment_method" class="modern-form-label">{{ __('accounting.payment_method') }}</label>
                        <select class="modern-form-control @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method">
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>{{ __('accounting.cash') }}</option>
                            <option value="credit_card" {{ old('payment_method', 'credit_card') == 'credit_card' ? 'selected' : '' }}>{{ __('accounting.credit_card') }}</option>
                            <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>{{ __('accounting.check') }}</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>{{ __('accounting.bank_transfer') }}</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="receipt_number" class="modern-form-label">{{ __('accounting.receipt_number') }}</label>
                        <input type="text" class="modern-form-control @error('receipt_number') is-invalid @enderror" 
                               id="receipt_number" name="receipt_number" value="{{ old('receipt_number') }}" placeholder="{{ __('accounting.enter_receipt_number') }}">
                        @error('receipt_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="modern-form-label">{{ __('accounting.description') }} <span class="text-danger">*</span></label>
                    <textarea class="modern-form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3" required placeholder="{{ __('accounting.enter_expense_description') }}">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_billable" name="is_billable" value="1" 
                                   {{ old('is_billable') ? 'checked' : '' }}>
                            <label class="form-check-label modern-form-label" for="is_billable">
                                {{ __('accounting.is_billable') }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_reimbursable" name="is_reimbursable" value="1" 
                                   {{ old('is_reimbursable') ? 'checked' : '' }}>
                            <label class="form-check-label modern-form-label" for="is_reimbursable">
                                {{ __('accounting.is_reimbursable') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receipt Upload -->
        <div class="modern-form-card">
            <div class="form-section-header">
                <h5 class="mb-1 fw-bold">{{ __('accounting.attachments') }}</h5>
                <p class="mb-0 opacity-75" style="font-size: 0.875rem;">{{ __('accounting.upload_receipt_description') }}</p>
            </div>
            
            <div class="form-section">
                <div class="file-upload-area" id="fileUploadArea">
                    <div class="mb-3">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">{{ __('accounting.drag_drop_files') }}</h6>
                        <p class="text-muted small">{{ __('accounting.or_click_to_browse') }}</p>
                    </div>
                    <input type="file" class="d-none" id="receipt_files" name="receipt_files[]" multiple accept="image/*,.pdf">
                    <button type="button" class="modern-btn-secondary" onclick="document.getElementById('receipt_files').click()">
                        <i class="fas fa-paperclip me-2"></i>{{ __('accounting.choose_files') }}
                    </button>
                </div>
                
                <div id="fileList" class="mt-3"></div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row">
            <div class="col-lg-8">
                <div class="modern-form-card">
                    <div class="form-section">
                        <h6 class="text-muted text-uppercase fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.1em;">
                            {{ __('accounting.additional_notes') }}
                        </h6>
                        
                        <div class="mb-3">
                            <label for="notes" class="modern-form-label">{{ __('accounting.notes') }}</label>
                            <textarea class="modern-form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" placeholder="{{ __('accounting.enter_additional_notes') }}">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="expense-summary">
                    <h6 class="fw-bold mb-3">{{ __('accounting.expense_summary') }}</h6>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('accounting.amount') }}:</span>
                        <span id="expenseAmount">$0.00</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('accounting.category') }}:</span>
                        <span id="expenseCategory">—</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span>{{ __('accounting.payment_method') }}:</span>
                        <span id="expensePaymentMethod">{{ __('accounting.credit_card') }}</span>
                    </div>
                    
                    <div class="section-divider my-3" style="background: rgba(255,255,255,0.3);"></div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" name="action" value="draft" class="modern-btn-secondary flex-fill">
                            {{ __('accounting.save_as_draft') }}
                        </button>
                        <button type="submit" name="action" value="submit" class="modern-btn-primary flex-fill">
                            {{ __('accounting.submit_for_approval') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Update expense summary
function updateExpenseSummary() {
    const amount = document.getElementById('amount').value || 0;
    const category = document.getElementById('category');
    const paymentMethod = document.getElementById('payment_method');
    
    document.getElementById('expenseAmount').textContent = '$' + parseFloat(amount).toFixed(2);
    document.getElementById('expenseCategory').textContent = category.options[category.selectedIndex].text || '—';
    document.getElementById('expensePaymentMethod').textContent = paymentMethod.options[paymentMethod.selectedIndex].text;
}

// Event listeners
document.getElementById('amount').addEventListener('input', updateExpenseSummary);
document.getElementById('category').addEventListener('change', updateExpenseSummary);
document.getElementById('payment_method').addEventListener('change', updateExpenseSummary);

// File upload handling
const fileUploadArea = document.getElementById('fileUploadArea');
const fileInput = document.getElementById('receipt_files');
const fileList = document.getElementById('fileList');

fileUploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('dragover');
});

fileUploadArea.addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.classList.remove('dragover');
});

fileUploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    fileInput.files = files;
    displayFiles(files);
});

fileInput.addEventListener('change', function() {
    displayFiles(this.files);
});

function displayFiles(files) {
    fileList.innerHTML = '';
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const fileItem = document.createElement('div');
        fileItem.className = 'alert alert-info d-flex justify-content-between align-items-center';
        fileItem.innerHTML = `
            <div>
                <i class="fas fa-file me-2"></i>
                <span>${file.name}</span>
                <small class="text-muted ms-2">(${(file.size / 1024).toFixed(1)} KB)</small>
            </div>
            <button type="button" class="btn-close" onclick="removeFile(${i})"></button>
        `;
        fileList.appendChild(fileItem);
    }
}

function removeFile(index) {
    const dt = new DataTransfer();
    const files = fileInput.files;
    
    for (let i = 0; i < files.length; i++) {
        if (i !== index) {
            dt.items.add(files[i]);
        }
    }
    
    fileInput.files = dt.files;
    displayFiles(fileInput.files);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateExpenseSummary();
});
</script>
@endpush
@endsection
