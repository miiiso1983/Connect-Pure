@extends('layouts.app')

@section('title', __('accounting.expenses'))

@push('styles')
<style>
    .expenses-header {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        border-radius: 20px;
        color: white;
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    
    .expenses-header::before {
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
    
    .summary-card.total::before { background: linear-gradient(90deg, #fa709a 0%, #fee140 100%); }
    .summary-card.pending::before { background: linear-gradient(90deg, #ffecd2 0%, #fcb69f 100%); }
    .summary-card.approved::before { background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%); }
    .summary-card.rejected::before { background: linear-gradient(90deg, #ff6b6b 0%, #ee5a24 100%); }
    
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
    
    .summary-icon.total { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .summary-icon.pending { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); }
    .summary-icon.approved { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
    .summary-icon.rejected { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%); }
    
    .expenses-table-card {
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
    
    .add-expense-btn {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        border: none;
        border-radius: 15px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(250, 112, 154, 0.3);
        text-decoration: none;
    }
    
    .add-expense-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(250, 112, 154, 0.4);
        color: white;
    }
    
    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
    }
    
    .empty-icon {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Modern Header -->
    <div class="expenses-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="h2 mb-2 fw-bold">{{ __('accounting.expenses') }}</h1>
                <p class="mb-0 opacity-75">{{ __('accounting.manage_all_expenses') }}</p>
            </div>
            <div class="col-lg-4 text-end">
                <a href="{{ route('modules.accounting.expenses.create') }}" class="add-expense-btn">
                    <i class="fas fa-plus me-2"></i>{{ __('accounting.record_expense') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Modern Summary Cards -->
    <div class="row mb-5">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="summary-card total">
                <div class="summary-icon total">
                    <i class="fas fa-receipt"></i>
                </div>
                <h6 class="text-muted text-uppercase fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.1em;">
                    {{ __('accounting.total_expenses') }}
                </h6>
                <h3 class="fw-bold text-dark mb-0">
                    ${{ number_format($summary['total_expenses'] ?? 0, 2) }}
                </h3>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="summary-card pending">
                <div class="summary-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <h6 class="text-muted text-uppercase fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.1em;">
                    {{ __('accounting.pending_expenses') }}
                </h6>
                <h3 class="fw-bold text-dark mb-0">
                    ${{ number_format($summary['pending_expenses'] ?? 0, 2) }}
                </h3>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="summary-card approved">
                <div class="summary-icon approved">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h6 class="text-muted text-uppercase fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.1em;">
                    {{ __('accounting.approved_expenses') }}
                </h6>
                <h3 class="fw-bold text-dark mb-0">
                    ${{ number_format($summary['approved_expenses'] ?? 0, 2) }}
                </h3>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="summary-card rejected">
                <div class="summary-icon rejected">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h6 class="text-muted text-uppercase fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.1em;">
                    {{ __('accounting.rejected_expenses') }}
                </h6>
                <h3 class="fw-bold text-dark mb-0">
                    ${{ number_format($summary['rejected_expenses'] ?? 0, 2) }}
                </h3>
            </div>
        </div>
    </div>

    <!-- Modern Expenses Table -->
    <div class="expenses-table-card">
        <div class="table-header">
            <h5 class="mb-1 fw-bold">{{ __('accounting.expenses_list') }}</h5>
            <p class="mb-0 opacity-75" style="font-size: 0.875rem;">{{ __('accounting.manage_all_expenses_description') }}</p>
        </div>
        
        @if(isset($expenses) && $expenses->count() > 0)
            <div class="table-responsive">
                <table class="modern-table w-100">
                    <thead>
                        <tr>
                            <th>{{ __('accounting.expense_number') }}</th>
                            <th>{{ __('accounting.description') }}</th>
                            <th>{{ __('accounting.category') }}</th>
                            <th>{{ __('accounting.vendor') }}</th>
                            <th>{{ __('accounting.amount') }}</th>
                            <th>{{ __('accounting.date') }}</th>
                            <th>{{ __('accounting.status') }}</th>
                            <th>{{ __('accounting.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenses as $expense)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <i class="fas fa-receipt text-warning"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('modules.accounting.expenses.show', $expense) }}" class="text-decoration-none fw-semibold text-dark">
                                                {{ $expense->expense_number }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted">{{ Str::limit($expense->description, 40) }}</td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1 rounded-pill">
                                        {{ __('accounting.' . $expense->category) }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $expense->vendor->name ?? __('accounting.no_vendor') }}</td>
                                <td class="fw-semibold">${{ number_format($expense->amount, 2) }}</td>
                                <td class="text-muted">{{ $expense->expense_date->format('M d, Y') }}</td>
                                <td>
                                    <span class="status-badge bg-{{ $expense->status_color }} text-white">
                                        {{ __('accounting.' . $expense->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('modules.accounting.expenses.show', $expense) }}" 
                                           class="action-btn btn-view" title="{{ __('accounting.view') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($expense->status !== 'paid')
                                            <a href="{{ route('modules.accounting.expenses.edit', $expense) }}" 
                                               class="action-btn btn-edit" title="{{ __('accounting.edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($expense->status === 'pending')
                                                <button type="button" class="action-btn btn-approve" 
                                                        onclick="approveExpense({{ $expense->id }})" title="{{ __('accounting.approve') }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="action-btn btn-reject" 
                                                        onclick="rejectExpense({{ $expense->id }})" title="{{ __('accounting.reject') }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Modern Pagination -->
            <div class="p-4 border-top">
                <div class="d-flex justify-content-center">
                    {{ $expenses->links() }}
                </div>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <h5 class="text-dark fw-bold mb-2">{{ __('accounting.no_expenses_found') }}</h5>
                <p class="text-muted mb-4">{{ __('accounting.get_started_by_recording_expense') }}</p>
                <a href="{{ route('modules.accounting.expenses.create') }}" class="add-expense-btn">
                    <i class="fas fa-plus me-2"></i>{{ __('accounting.record_expense') }}
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function approveExpense(expenseId) {
    if (confirm('{{ __("accounting.confirm_approve_expense") }}')) {
        fetch(`/modules/accounting/expenses/${expenseId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || '{{ __("accounting.error_approving_expense") }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __("accounting.error_approving_expense") }}');
        });
    }
}

function rejectExpense(expenseId) {
    if (confirm('{{ __("accounting.confirm_reject_expense") }}')) {
        fetch(`/modules/accounting/expenses/${expenseId}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || '{{ __("accounting.error_rejecting_expense") }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __("accounting.error_rejecting_expense") }}');
        });
    }
}
</script>
@endpush
@endsection
