@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between border-bottom mb-20">
                            <div class="page-title-left">
                                <h3 class="mb-sm-0">{{ $pageTitle }}</h3>
                            </div>
                            <div class="page-title-right">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                    <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="ri-add-line me-1"></i> {{ __('Create Approval Request') }}
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="approvalsDataTable" class="table table-bordered table-striped align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('SL') }}</th>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                        <th>{{ __('Currency') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Approval Threshold %') }}</th>
                                        <th>{{ __('Deadline') }}</th>
                                        <th>{{ __('Requested By') }}</th>
                                        <th>{{ __('Created') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($approvals as $index => $approval)
                                    <tr>
                                        <td>{{ $approvals->firstItem() + $index }}</td>
                                        <td>{{ $approval->title }}</td>
                                        <td>{{ number_format($approval->amount, 2) }}</td>
                                        <td>{{ $approval->currency }}</td>
                                        <td>
                                            @if($approval->status == APPROVAL_STATUS_PENDING)
                                                <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                            @elseif($approval->status == APPROVAL_STATUS_APPROVED)
                                                <span class="badge bg-success">{{ __('Approved') }}</span>
                                            @elseif($approval->status == APPROVAL_STATUS_REJECTED)
                                                <span class="badge bg-danger">{{ __('Rejected') }}</span>
                                            @elseif($approval->status == APPROVAL_STATUS_EXPIRED)
                                                <span class="badge bg-secondary">{{ __('Expired') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $approval->approval_threshold ?? '—' }}%</td>
                                        <td>{{ $approval->deadline_at ? \Carbon\Carbon::parse($approval->deadline_at)->format('d M Y H:i') : '—' }}</td>
                                        <td>{{ $approval->requestedBy ? ($approval->requestedBy->full_name ?? $approval->requestedBy->name) : __('Admin') }}</td>
                                        <td>{{ $approval->created_at->format('d M Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $approvals->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Create Approval Request Modal --}}
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">{{ __('Create Approval Request') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <form action="{{ route('admin.governance.approvals.store') }}" method="POST" class="ajax" data-handler="getShowMessage">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required placeholder="{{ __('Enter approval request title') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('Description') }}</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="{{ __('Enter description') }}"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Amount') }} <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" required min="0" step="0.01" placeholder="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Currency') }} <span class="text-danger">*</span></label>
                            <input type="text" name="currency" class="form-control" required value="KES" placeholder="KES">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Approval Threshold (%)') }}</label>
                            <input type="number" name="approval_threshold" class="form-control" min="0" max="100" step="0.01" placeholder="51" value="51">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Deadline') }}</label>
                            <input type="datetime-local" name="deadline_at" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('Requested By (Shareholder)') }}</label>
                            <select name="requested_by_shareholder_id" class="form-select">
                                <option value="">{{ __('None (Admin Request)') }}</option>
                                @foreach($shareholders as $shareholder)
                                    <option value="{{ $shareholder->id }}">{{ $shareholder->full_name }} ({{ $shareholder->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('Notes') }}</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="{{ __('Optional notes') }}"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Create Request') }}</button>
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
    <script src="{{ asset('assets/js/custom/governance-approvals.js') }}"></script>
@endpush
