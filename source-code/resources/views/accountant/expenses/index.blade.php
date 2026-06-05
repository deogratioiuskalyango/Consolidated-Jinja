@php $navExpensesActiveClass = 'active mm-active'; @endphp
@extends('accountant.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">

                {{-- Page Title & Breadcrumb --}}
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between border-bottom mb-20">
                            <div class="page-title-left">
                                <h3 class="mb-sm-0">{{ __('Expenses') }}</h3>
                            </div>
                            <div class="page-title-right">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                    <li class="breadcrumb-item active">{{ __('Expenses') }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stats Row --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-warning border-start border-4 shadow-sm">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                    <i class="ri-time-line fs-4 text-warning"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">{{ __('Pending') }}</div>
                                    <div class="fs-4 fw-bold">{{ $stats['pending'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-success border-start border-4 shadow-sm">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <i class="ri-checkbox-circle-line fs-4 text-success"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">{{ __('Approved') }}</div>
                                    <div class="fs-4 fw-bold">{{ $stats['approved'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-primary border-start border-4 shadow-sm">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="ri-money-dollar-circle-line fs-4 text-primary"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">{{ __('Total This Month (UGX)') }}</div>
                                    <div class="fs-4 fw-bold">{{ number_format($stats['total_this_month'] ?? 0) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Add Expense Button --}}
                <div class="row mb-3">
                    <div class="col-12 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="ri-add-line me-1"></i> {{ __('Add Expense') }}
                        </button>
                    </div>
                </div>

                {{-- DataTable --}}
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="expensesTable" class="table table-bordered table-striped align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('SL') }}</th>
                                        <th>{{ __('Reference') }}</th>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Category') }}</th>
                                        <th>{{ __('Amount (UGX)') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $categoryLabels = [
                                            1 => 'Maintenance',
                                            2 => 'Utilities',
                                            3 => 'Contractor',
                                            4 => 'Salary',
                                            5 => 'Insurance',
                                            6 => 'Legal',
                                            7 => 'Marketing',
                                            8 => 'Other',
                                        ];
                                    @endphp
                                    @foreach($expenses as $index => $e)
                                    <tr>
                                        <td>{{ $expenses->firstItem() + $index }}</td>
                                        <td>{{ $e->reference ?? '—' }}</td>
                                        <td>{{ $e->title }}</td>
                                        <td>{{ $categoryLabels[$e->category] ?? ($expenseCategories[$e->category] ?? '—') }}</td>
                                        <td>{{ number_format($e->amount) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($e->expense_date)->format('d M Y') }}</td>
                                        <td>
                                            @if($e->status == 'pending')
                                                <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                            @elseif($e->status == 'approved')
                                                <span class="badge bg-success">{{ __('Approved') }}</span>
                                            @elseif($e->status == 'rejected')
                                                <span class="badge bg-danger">{{ __('Rejected') }}</span>
                                            @elseif($e->status == 'paid')
                                                <span class="badge bg-info">{{ __('Paid') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($e->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-inline-flex gap-1">
                                                <a href="{{ route('accountant.expenses.show', $e) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="{{ __('View') }}">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                                @if($e->status == 'pending')
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-success expense-approve-btn"
                                                        data-url="{{ route('accountant.expenses.approve', $e) }}"
                                                        title="{{ __('Approve') }}">
                                                    <i class="ri-checkbox-circle-line"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-warning expense-reject-btn"
                                                        data-id="{{ $e->id }}"
                                                        data-url="{{ route('accountant.expenses.reject', $e) }}"
                                                        title="{{ __('Reject') }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#rejectModal">
                                                    <i class="ri-close-circle-line"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger expense-delete-btn"
                                                        data-url="{{ route('accountant.expenses.destroy', $e) }}"
                                                        title="{{ __('Delete') }}">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $expenses->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Add Expense Modal --}}
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">{{ __('Add Expense') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <form action="{{ route('accountant.expenses.store') }}"
                  method="POST"
                  class="ajax"
                  data-handler="expenseShowMessage">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required
                                   placeholder="{{ __('Enter expense title') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Category') }} <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="">{{ __('Select Category') }}</option>
                                <option value="1">{{ __('Maintenance') }}</option>
                                <option value="2">{{ __('Utilities') }}</option>
                                <option value="3">{{ __('Contractor') }}</option>
                                <option value="4">{{ __('Salary') }}</option>
                                <option value="5">{{ __('Insurance') }}</option>
                                <option value="6">{{ __('Legal') }}</option>
                                <option value="7">{{ __('Marketing') }}</option>
                                <option value="8">{{ __('Other') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Amount (UGX)') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">UGX</span>
                                <input type="number" name="amount" class="form-control" step="0.01" min="0" required
                                       placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Expense Date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control" required
                                   value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('Description') }}</label>
                            <textarea name="description" class="form-control" rows="3"
                                      placeholder="{{ __('Enter description or justification...') }}"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save Expense') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject Expense Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">{{ __('Reject Expense') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectExpenseForm" method="POST" class="ajax" data-handler="expenseRejectMessage">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Reason for Rejection') }} <span class="text-danger">*</span></label>
                        <textarea name="reject_reason" class="form-control" rows="3" required
                                  placeholder="{{ __('Enter reason for rejection...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-warning">{{ __('Reject') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('style')
    @include('common.layouts.datatable-style')
@endpush

@push('script')
    @include('common.layouts.datatable-script')
    <script>
        $(document).ready(function () {
            $('#expensesTable').DataTable({
                responsive: true,
                pageLength: 25,
                order: [[5, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [7] }
                ]
            });

            // Approve via AJAX POST
            $(document).on('click', '.expense-approve-btn', function () {
                var url = $(this).data('url');
                $.post(url, { _token: $('meta[name=csrf-token]').attr('content') }, function (response) {
                    window.expenseShowMessage(response);
                }).fail(function () {
                    alert('{{ __("An error occurred. Please try again.") }}');
                });
            });

            // Populate reject modal
            $(document).on('click', '.expense-reject-btn', function () {
                $('#rejectExpenseForm').attr('action', $(this).data('url'));
            });

            // Delete via AJAX POST
            $(document).on('click', '.expense-delete-btn', function () {
                var url = $(this).data('url');
                if (!confirm('{{ __("Are you sure you want to delete this expense?") }}')) return;
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: $('meta[name=csrf-token]').attr('content'),
                        _method: 'DELETE'
                    },
                    success: function (response) {
                        window.expenseShowMessage(response);
                    }
                });
            });
        });

        window.expenseShowMessage = function (response) {
            if (response.status === 'success') {
                window.location.reload();
            } else {
                commonHandler(response);
            }
        };

        window.expenseRejectMessage = function (response) {
            if (response.status === 'success') {
                window.location.reload();
            } else {
                commonHandler(response);
            }
        };
    </script>
@endpush
