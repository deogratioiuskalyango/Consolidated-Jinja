@php $navAuditLogsActiveClass = 'active mm-active'; @endphp
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
                                <h3 class="mb-sm-0">{{ __('Financial Audit Logs') }}</h3>
                            </div>
                            <div class="page-title-right">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                    <li class="breadcrumb-item active">{{ __('Audit Logs') }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filter Form --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-body">
                                <form method="GET" action="{{ route('accountant.audit-logs') }}">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label">{{ __('Action Type') }}</label>
                                            <select name="action" class="form-select form-select-sm">
                                                <option value="">{{ __('All Actions') }}</option>
                                                <option value="payment_recorded"  {{ request('action') == 'payment_recorded'  ? 'selected' : '' }}>{{ __('Payment Recorded') }}</option>
                                                <option value="payment_reversed"  {{ request('action') == 'payment_reversed'  ? 'selected' : '' }}>{{ __('Payment Reversed') }}</option>
                                                <option value="expense_created"   {{ request('action') == 'expense_created'   ? 'selected' : '' }}>{{ __('Expense Created') }}</option>
                                                <option value="expense_updated"   {{ request('action') == 'expense_updated'   ? 'selected' : '' }}>{{ __('Expense Updated') }}</option>
                                                <option value="expense_deleted"   {{ request('action') == 'expense_deleted'   ? 'selected' : '' }}>{{ __('Expense Deleted') }}</option>
                                                <option value="report_generated"  {{ request('action') == 'report_generated'  ? 'selected' : '' }}>{{ __('Report Generated') }}</option>
                                                <option value="report_shared"     {{ request('action') == 'report_shared'     ? 'selected' : '' }}>{{ __('Report Shared') }}</option>
                                                <option value="recon_uploaded"    {{ request('action') == 'recon_uploaded'    ? 'selected' : '' }}>{{ __('Reconciliation Uploaded') }}</option>
                                                <option value="recon_matched"     {{ request('action') == 'recon_matched'     ? 'selected' : '' }}>{{ __('Reconciliation Matched') }}</option>
                                                <option value="recon_flagged"     {{ request('action') == 'recon_flagged'     ? 'selected' : '' }}>{{ __('Reconciliation Flagged') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">{{ __('Date From') }}</label>
                                            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">{{ __('Date To') }}</label>
                                            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                                <i class="ri-filter-line"></i>
                                            </button>
                                        </div>
                                        <div class="col-md-1">
                                            <a href="{{ route('accountant.audit-logs') }}" class="btn btn-outline-secondary btn-sm w-100">
                                                <i class="ri-refresh-line"></i>
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DataTable --}}
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="auditTable" class="table table-bordered table-striped align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('SL') }}</th>
                                        <th>{{ __('Action') }}</th>
                                        <th>{{ __('Actor') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th>{{ __('IP') }}</th>
                                        <th>{{ __('Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $actionLabels = [
                                            'payment_recorded'  => 'Payment Recorded',
                                            'payment_reversed'  => 'Payment Reversed',
                                            'expense_created'   => 'Expense Created',
                                            'expense_updated'   => 'Expense Updated',
                                            'expense_deleted'   => 'Expense Deleted',
                                            'report_generated'  => 'Report Generated',
                                            'report_shared'     => 'Report Shared',
                                            'recon_uploaded'    => 'Reconciliation Uploaded',
                                            'recon_matched'     => 'Reconciliation Matched',
                                            'recon_flagged'     => 'Reconciliation Flagged',
                                        ];
                                    @endphp
                                    @foreach($logs as $index => $log)
                                    <tr>
                                        <td>{{ $logs->firstItem() + $index }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                {{ $actionLabels[$log->action] ?? ucwords(str_replace('_', ' ', $log->action)) }}
                                            </span>
                                        </td>
                                        <td>{{ $log->actor_name ?? '—' }}</td>
                                        <td>{{ $log->note ?? '—' }}</td>
                                        <td>
                                            <code>{{ $log->ip_address ?? '—' }}</code>
                                        </td>
                                        <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $logs->links() }}
                        </div>
                    </div>
                </div>

            </div>
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
        $(function () {
            if ($('#auditTable').length) {
                $('#auditTable').DataTable({
                    responsive: true,
                    order: [[5, 'desc']],
                    columnDefs: [
                        { orderable: false, targets: [3] }
                    ]
                });
            }
        });
    </script>
@endpush
