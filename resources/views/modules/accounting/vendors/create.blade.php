@extends('layouts.app')

@section('title', __('accounting.add_vendor'))

@push('styles')
<style>
    .modern-form-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: none;
        overflow: hidden;
    }

    .form-header {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 2rem;
        position: relative;
    }

    .form-header::before {
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

    .modern-form-control {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #f8fafc;
    }

    .modern-form-control:focus {
        border-color: #f093fb;
        box-shadow: 0 0 0 3px rgba(240, 147, 251, 0.1);
        background: white;
    }

    .modern-form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .modern-btn-primary {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border: none;
        border-radius: 12px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(240, 147, 251, 0.3);
    }

    .modern-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(240, 147, 251, 0.4);
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
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Modern Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">{{ __('accounting.add_vendor') }}</h1>
            <p class="text-muted">{{ __('accounting.create_new_vendor') }}</p>
        </div>
        <a href="{{ route('modules.accounting.vendors.index') }}" class="modern-btn-secondary text-decoration-none">
            <i class="fas fa-arrow-left me-2"></i>{{ __('accounting.back_to_vendors') }}
        </a>
    </div>

    <!-- Modern Vendor Form -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="modern-form-card">
                <div class="form-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="mb-1 fw-bold">{{ __('accounting.vendor_information') }}</h4>
                            <p class="mb-0 opacity-75">{{ __('accounting.fill_vendor_details') }}</p>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <form method="POST" action="{{ route('modules.accounting.vendors.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">{{ __('accounting.vendor_name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label">{{ __('accounting.company_name') }}</label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                       id="company_name" name="company_name" value="{{ old('company_name') }}">
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">{{ __('accounting.email') }}</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">{{ __('accounting.phone') }}</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">{{ __('accounting.address') }}</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="city" class="form-label">{{ __('accounting.city') }}</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                       id="city" name="city" value="{{ old('city') }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="state" class="form-label">{{ __('accounting.state') }}</label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                       id="state" name="state" value="{{ old('state') }}">
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="postal_code" class="form-label">{{ __('accounting.postal_code') }}</label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                       id="postal_code" name="postal_code" value="{{ old('postal_code') }}">
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="currency" class="form-label">{{ __('accounting.currency') }} <span class="text-danger">*</span></label>
                                <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                                    <option value="">{{ __('accounting.select_currency') }}</option>
                                    <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                    <option value="SAR" {{ old('currency') == 'SAR' ? 'selected' : '' }}>SAR - Saudi Riyal</option>
                                    <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                </select>
                                @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="payment_terms" class="form-label">{{ __('accounting.payment_terms') }} <span class="text-danger">*</span></label>
                                <select class="form-select @error('payment_terms') is-invalid @enderror" id="payment_terms" name="payment_terms" required>
                                    <option value="">{{ __('accounting.select_payment_terms') }}</option>
                                    <option value="net_15" {{ old('payment_terms') == 'net_15' ? 'selected' : '' }}>{{ __('accounting.net_15') }}</option>
                                    <option value="net_30" {{ old('payment_terms') == 'net_30' ? 'selected' : '' }}>{{ __('accounting.net_30') }}</option>
                                    <option value="net_45" {{ old('payment_terms') == 'net_45' ? 'selected' : '' }}>{{ __('accounting.net_45') }}</option>
                                    <option value="net_60" {{ old('payment_terms') == 'net_60' ? 'selected' : '' }}>{{ __('accounting.net_60') }}</option>
                                    <option value="due_on_receipt" {{ old('payment_terms') == 'due_on_receipt' ? 'selected' : '' }}>{{ __('accounting.due_on_receipt') }}</option>
                                </select>
                                @error('payment_terms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">{{ __('accounting.notes') }}</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    {{ __('accounting.active') }}
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 pt-3">
                            <a href="{{ route('modules.accounting.vendors.index') }}" class="modern-btn-secondary text-decoration-none">
                                <i class="fas fa-times me-2"></i>{{ __('accounting.cancel') }}
                            </a>
                            <button type="submit" class="modern-btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('accounting.save_vendor') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
