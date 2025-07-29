@extends('layouts.app')

@section('title', __('accounting.add_customer'))

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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        background: white;
    }

    .modern-form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .modern-btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 12px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    .modern-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
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
            <h1 class="h3 mb-0 text-gray-800 fw-bold">{{ __('accounting.add_customer') }}</h1>
            <p class="text-muted">{{ __('accounting.create_new_customer') }}</p>
        </div>
        <a href="{{ route('modules.accounting.customers.index') }}" class="modern-btn-secondary text-decoration-none">
            <i class="fas fa-arrow-left me-2"></i>{{ __('accounting.back_to_customers') }}
        </a>
    </div>

    <!-- Modern Customer Form -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="modern-form-card">
                <div class="form-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="mb-1 fw-bold">{{ __('accounting.customer_information') }}</h4>
                            <p class="mb-0 opacity-75">{{ __('accounting.fill_customer_details') }}</p>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-plus fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <form method="POST" action="{{ route('modules.accounting.customers.store') }}">
                        @csrf

                        <!-- Basic Information -->
                        <div class="mb-4">
                            <h6 class="text-muted text-uppercase fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.1em;">
                                <i class="fas fa-user me-2"></i>{{ __('accounting.basic_information') }}
                            </h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="modern-form-label">{{ __('accounting.customer_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="modern-form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}" required placeholder="{{ __('accounting.enter_customer_name') }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="company_name" class="modern-form-label">{{ __('accounting.company_name') }}</label>
                                    <input type="text" class="modern-form-control @error('company_name') is-invalid @enderror"
                                           id="company_name" name="company_name" value="{{ old('company_name') }}" placeholder="{{ __('accounting.enter_company_name') }}">
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="section-divider"></div>

                        <!-- Contact Information -->
                        <div class="mb-4">
                            <h6 class="text-muted text-uppercase fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.1em;">
                                <i class="fas fa-envelope me-2"></i>{{ __('accounting.contact_information') }}
                            </h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="modern-form-label">{{ __('accounting.email') }}</label>
                                    <input type="email" class="modern-form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email') }}" placeholder="{{ __('accounting.enter_email_address') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="modern-form-label">{{ __('accounting.phone') }}</label>
                                    <input type="text" class="modern-form-control @error('phone') is-invalid @enderror"
                                           id="phone" name="phone" value="{{ old('phone') }}" placeholder="{{ __('accounting.enter_phone_number') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="whatsapp_number" class="modern-form-label">
                                        {{ __('accounting.whatsapp_number') }}
                                        <span class="text-muted small">({{ __('accounting.optional') }})</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.785"/>
                                            </svg>
                                        </span>
                                        <input type="text" class="modern-form-control @error('whatsapp_number') is-invalid @enderror"
                                               id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number') }}"
                                               placeholder="{{ __('accounting.enter_whatsapp_number') }}">
                                    </div>
                                    <small class="form-text text-muted">{{ __('accounting.whatsapp_number_help') }}</small>
                                    @error('whatsapp_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="section-divider"></div>

                        <div class="mb-3">
                            <label for="billing_address" class="form-label">{{ __('accounting.billing_address') }}</label>
                            <textarea class="form-control @error('billing_address') is-invalid @enderror" 
                                      id="billing_address" name="billing_address" rows="3">{{ old('billing_address') }}</textarea>
                            @error('billing_address')
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
                            <a href="{{ route('modules.accounting.customers.index') }}" class="modern-btn-secondary text-decoration-none">
                                <i class="fas fa-times me-2"></i>{{ __('accounting.cancel') }}
                            </a>
                            <button type="submit" class="modern-btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('accounting.save_customer') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
