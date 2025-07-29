@extends('layouts.app')

@section('title', __('accounting.vendors'))

@push('styles')
<style>
    .vendors-header {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border-radius: 20px;
        color: white;
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .vendors-header::before {
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

    .summary-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: none;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--card-color);
    }

    .summary-card.total::before { background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%); }
    .summary-card.active::before { background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%); }
    .summary-card.balance::before { background: linear-gradient(90deg, #fa709a 0%, #fee140 100%); }

    .summary-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin-bottom: 1rem;
    }

    .summary-icon.total { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .summary-icon.active { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
    .summary-icon.balance { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }

    .vendors-table-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: none;
        overflow: hidden;
    }

    .table-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border: none;
    }

    .add-vendor-btn {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border: none;
        border-radius: 15px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(240, 147, 251, 0.3);
        text-decoration: none;
    }

    .add-vendor-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(240, 147, 251, 0.4);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Modern Header -->
    <div class="vendors-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="h2 mb-2 fw-bold">{{ __('accounting.vendors') }}</h1>
                <p class="mb-0 opacity-75">{{ __('accounting.manage_vendors') }}</p>
            </div>
            <div class="col-lg-4 text-end">
                <a href="{{ route('modules.accounting.vendors.bulk-upload') }}" class="btn btn-outline-light me-2">
                    <i class="fas fa-upload me-2"></i>{{ __('accounting.bulk_upload') }}
                </a>
                <a href="{{ route('modules.accounting.vendors.create') }}" class="add-vendor-btn">
                    <i class="fas fa-plus me-2"></i>{{ __('accounting.add_vendor') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ __('accounting.total_vendors') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $summary['total_vendors'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                {{ __('accounting.active_vendors') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $summary['active_vendors'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                {{ __('accounting.total_balance') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($summary['total_balance'] ?? 0, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendors Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('accounting.vendors_list') }}</h6>
        </div>
        <div class="card-body">
            @if($vendors->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>{{ __('accounting.vendor_name') }}</th>
                                <th>{{ __('accounting.company_name') }}</th>
                                <th>{{ __('accounting.email') }}</th>
                                <th>{{ __('accounting.phone') }}</th>
                                <th>{{ __('accounting.balance') }}</th>
                                <th>{{ __('accounting.status') }}</th>
                                <th>{{ __('accounting.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vendors as $vendor)
                                <tr>
                                    <td>
                                        <a href="{{ route('modules.accounting.vendors.show', $vendor) }}" class="text-decoration-none">
                                            {{ $vendor->name }}
                                        </a>
                                    </td>
                                    <td>{{ $vendor->company_name }}</td>
                                    <td>{{ $vendor->email }}</td>
                                    <td>{{ $vendor->phone }}</td>
                                    <td>${{ number_format($vendor->balance, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $vendor->is_active ? 'success' : 'secondary' }}">
                                            {{ $vendor->is_active ? __('accounting.active') : __('accounting.inactive') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('modules.accounting.vendors.show', $vendor) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('modules.accounting.vendors.edit', $vendor) }}" 
                                               class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $vendors->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-building fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">{{ __('accounting.no_vendors_found') }}</h5>
                    <p class="text-muted">{{ __('accounting.get_started_by_adding_vendor') }}</p>
                    <a href="{{ route('modules.accounting.vendors.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>{{ __('accounting.add_vendor') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
