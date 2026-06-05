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
                                <h3 class="mb-sm-0">{{ __('Expense Detail') }}</h3>
                            </div>
                            <div class="page-title-right">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('accountant.expenses.index') }}">{{ __('Expenses') }}</a></li>
                                    <li class="breadcrumb-item active">{{ __('View') }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">

                    {{-- Left: Expense Details --}}
                    <div class="col-lg-8">
                        <div class="card border shadow-sm">
                            <div class="card-header bg-light d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">{{ __('Expense Information') }}</h5>
                                @if($expense->status == 'pending')
                                    <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                @elseif($expense->status == 'approved')
                                    <span class="badge bg-success">{{ __('Approved') }}</span>
                                @elseif($expense->status == 'rejected')
                                    <span class="badge bg-danger">{{ __('Rejected') }}</span>
                                @elseif($expense->status == 'paid')
                                    <span class="badge bg-info">{{ __('Paid') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($expense->status) }}</span>
                                @endif
                            </div>
                            <div class="card-body">
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
                                <dl class="row mb-0">
                                    <dt class="col-sm-4 text-muted">{{ __('Reference') }}</dt>
                                    <dd class="col-sm-8 fw-semibold">{{ $expense->reference ?? '—' }}</dd>

                                    <dt class="col-sm-4 text-muted">{{ __('Title') }}</dt>
                                    <dd class="col-sm-8">{{ $expense->title }}</dd>

                                    <dt class="col-sm-4 text-muted">{{ __('Category') }}</dt>
                                    <dd class="col-sm-8">{{ $categoryLabels[$expense->category] ?? ($expenseCategories[$expense->category] ?? '—') }}</dd>

                                    <dt class="col-sm-4 text-muted">{{ __('Amount') }}</dt>
                                    <dd class="col-sm-8 fw-bold fs-5">UGX {{ number_format($expense->amount) }}</dd>

                                    <dt class="col-sm-4 text-muted">{{ __('Expense Date') }}</dt>
                                    <dd class="col-sm-8">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</dd>

                                    <dt class="col-sm-4 text-muted">{{ __('Description') }}</dt>
                                    <dd class="col-sm-8">{{ $expense->description ?? '—' }}</dd>

                                    <dt class="col-sm-4 text-muted">{{ __('Submitted By') }}</dt>
                                    <dd class="col-sm-8">{{ $expense->submittedBy->name ?? '—' }}</dd>

                                    @if(in_array($expense->status, ['approved', 'paid']))
                                    <dt class="col-sm-4 text-muted">{{ __('Approved By') }}</dt>
                                    <dd class="col-sm-8">{{ $expense->approvedBy->name ?? '—' }}</dd>

                                    <dt class="col-sm-4 text-muted">{{ __('Approval Date') }}</dt>
                                    <dd class="col-sm-8">{{ $expense->approved_at ? \Carbon\Carbon::parse($expense->approved_at)->format('d M Y H:i') : '—' }}</dd>
                                    @endif

                                    @if($expense->status == 'rejected')
                                    <dt class="col-sm-4 text-muted">{{ __('Rejected By') }}</dt>
                                    <dd class="col-sm-8">{{ $expense->rejectedBy->name ?? '—' }}</dd>

                                    <dt class="col-sm-4 text-muted">{{ __('Rejection Reason') }}</dt>
                                    <dd class="col-sm-8 text-danger">{{ $expense->reject_reason ?? '—' }}</dd>
                                    @endif

                                    <dt class="col-sm-4 text-muted">{{ __('Recorded At') }}</dt>
                                    <dd class="col-sm-8">{{ $expense->created_at->format('d M Y H:i') }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    {{-- Right: Actions --}}
                    <div class="col-lg-4">
                        <div class="card border shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">{{ __('Actions') }}</h5>
                            </div>
                            <div class="card-body d-grid gap-2">
                                @if($expense->status == 'pending')
                                <button type="button" class="btn btn-success" id="approveBtn"
                                        data-url="{{ route('accountant.expenses.approve', $expense) }}">
                                    <i class="ri-checkbox-circle-line me-1"></i> {{ __('Approve') }}
                                </button>
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="ri-close-circle-line me-1"></i> {{ __('Reject') }}
                                </button>
                                <button type="button" class="btn btn-outline-danger" id="deleteBtn"
                                        data-url="{{ route('accountant.expenses.destroy', $expense) }}">
                                    <i class="ri-delete-bin-line me-1"></i> {{ __('Delete') }}
                                </button>
                                @endif

                                <a href="{{ route('accountant.expenses.index') }}" class="btn btn-outline-secondary">
                                    <i class="ri-arrow-left-line me-1"></i> {{ __('Back to List') }}
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
@if($expense->status == 'pending')
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">{{ __('Reject Expense') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('accountant.expenses.reject', $expense) }}"
                  method="POST"
                  class="ajax"
                  data-handler="expenseActionMessage">
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
@endif
@endsection

@push('script')
<script>
    $(document).ready(function () {
        // Approve
        $('#approveBtn').on('click', function () {
            var url = $(this).data('url');
            $.post(url, { _token: $('meta[name=csrf-token]').attr('content') }, function (response) {
                window.expenseActionMessage(response);
            }).fail(function () {
                alert('{{ __("An error occurred. Please try again.") }}');
            });
        });

        // Delete
        $('#deleteBtn').on('click', function () {
            if (!confirm('{{ __("Are you sure you want to delete this expense?") }}')) return;
            var url = $(this).data('url');
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: $('meta[name=csrf-token]').attr('content'),
                    _method: 'DELETE'
                },
                success: function (response) {
                    window.expenseActionMessage(response);
                }
            });
        });
    });

    window.expenseActionMessage = function (response) {
        if (response.status === 'success') {
            window.location.href = '{{ route("accountant.expenses.index") }}';
        } else {
            commonHandler(response);
        }
    };
</script>
@endpush
