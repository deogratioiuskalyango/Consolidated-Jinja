@php $navReconciliationActiveClass = 'active mm-active'; @endphp
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
                                <h3 class="mb-sm-0">{{ __('Reconciliation') }}</h3>
                            </div>
                            <div class="page-title-right">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                    <li class="breadcrumb-item active">{{ __('Reconciliation') }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Summary Stats --}}
                @php
                    $totalUploaded  = $logs->total();
                    $matched        = $logs->getCollection()->where('status', 1)->count();
                    $unmatched      = $logs->getCollection()->where('status', 2)->count();
                    $suspicious     = $logs->getCollection()->whereIn('status', [3, 4])->count();
                @endphp
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card border text-center h-100">
                            <div class="card-body py-3">
                                <p class="text-muted mb-1 small">{{ __('Total Uploaded') }}</p>
                                <h4 class="fw-bold text-dark mb-0">{{ $logs->total() }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border text-center h-100">
                            <div class="card-body py-3">
                                <p class="text-muted mb-1 small">{{ __('Matched') }}</p>
                                <h4 class="fw-bold text-success mb-0">{{ $logs->getCollection()->where('status', 1)->count() }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border text-center h-100">
                            <div class="card-body py-3">
                                <p class="text-muted mb-1 small">{{ __('Unmatched') }}</p>
                                <h4 class="fw-bold text-warning mb-0">{{ $logs->getCollection()->where('status', 2)->count() }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border text-center h-100">
                            <div class="card-body py-3">
                                <p class="text-muted mb-1 small">{{ __('Suspicious') }}</p>
                                <h4 class="fw-bold text-danger mb-0">{{ $logs->getCollection()->whereIn('status', [3, 4])->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="row mb-3">
                    <div class="col-12 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="ri-upload-line me-1"></i> {{ __('Upload Transaction') }}
                        </button>
                    </div>
                </div>

                {{-- DataTable --}}
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="reconTable" class="table table-bordered table-striped align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('SL') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{ __('Transaction Ref') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $reconTypeLabels = [
                                            1 => 'MTN MoMo',
                                            2 => 'Airtel Money',
                                            3 => 'Bank',
                                            4 => 'Cash',
                                        ];
                                    @endphp
                                    @foreach($logs as $index => $log)
                                    <tr>
                                        <td>{{ $logs->firstItem() + $index }}</td>
                                        <td>{{ $reconTypeLabels[$log->recon_type] ?? '—' }}</td>
                                        <td>{{ $log->transaction_ref ?? '—' }}</td>
                                        <td>{{ number_format($log->amount) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($log->transaction_date)->format('d M Y') }}</td>
                                        <td>
                                            @if($log->status == 1)
                                                <span class="badge bg-success">{{ __('Matched') }}</span>
                                            @elseif($log->status == 2)
                                                <span class="badge bg-warning text-dark">{{ __('Unmatched') }}</span>
                                            @elseif($log->status == 3)
                                                <span class="badge bg-secondary">{{ __('Duplicate') }}</span>
                                            @elseif($log->status == 4)
                                                <span class="badge bg-danger">{{ __('Suspicious') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('Unknown') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-inline-flex gap-1">
                                                @if($log->status == 2)
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-success recon-match-btn"
                                                        data-id="{{ $log->id }}"
                                                        data-url="{{ route('accountant.reconciliation.match', $log->id) }}"
                                                        title="{{ __('Match') }}">
                                                    <i class="ri-links-line"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-warning recon-flag-btn"
                                                        data-id="{{ $log->id }}"
                                                        data-url="{{ route('accountant.reconciliation.flag', $log->id) }}"
                                                        title="{{ __('Flag') }}">
                                                    <i class="ri-flag-line"></i>
                                                </button>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </div>
                                        </td>
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

{{-- Upload Transaction Modal --}}
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">{{ __('Upload Transaction') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <form action="{{ route('accountant.reconciliation.upload') }}" method="POST" class="ajax" data-handler="reconShowMessage">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">{{ __('Transaction Type') }} <span class="text-danger">*</span></label>
                            <select name="recon_type" class="form-select" required>
                                <option value="">{{ __('Select Type') }}</option>
                                <option value="1">{{ __('MTN MoMo') }}</option>
                                <option value="2">{{ __('Airtel Money') }}</option>
                                <option value="3">{{ __('Bank') }}</option>
                                <option value="4">{{ __('Cash') }}</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('Transaction Reference') }} <span class="text-danger">*</span></label>
                            <input type="text" name="transaction_ref" class="form-control" required placeholder="{{ __('Enter transaction reference') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Amount') }} <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" required min="0" step="0.01" placeholder="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Transaction Date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="transaction_date" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('Description') }}</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="{{ __('Optional description...') }}"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-upload-line me-1"></i> {{ __('Upload') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Match Modal --}}
<div class="modal fade" id="matchModal" tabindex="-1" aria-labelledby="matchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="matchModalLabel">{{ __('Match Transaction') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <form id="matchForm" method="POST" class="ajax" data-handler="matchShowMessage">
                @csrf
                <div class="modal-body">
                    <p class="text-muted">{{ __('Enter the Rent Collection ID to match against this transaction.') }}</p>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Collection ID') }} <span class="text-danger">*</span></label>
                        <input type="text" name="collection_id" class="form-control" required placeholder="{{ __('e.g. 123') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ri-links-line me-1"></i> {{ __('Match') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Flag Modal --}}
<div class="modal fade" id="flagModal" tabindex="-1" aria-labelledby="flagModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="flagModalLabel">{{ __('Flag Transaction') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <form id="flagForm" method="POST" class="ajax" data-handler="flagShowMessage">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Flag As') }} <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="">{{ __('Select Status') }}</option>
                            <option value="3">{{ __('Duplicate') }}</option>
                            <option value="4">{{ __('Suspicious') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Flag Reason') }} <span class="text-danger">*</span></label>
                        <textarea name="flag_reason" class="form-control" rows="3" required placeholder="{{ __('Enter reason for flagging...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="ri-flag-line me-1"></i> {{ __('Flag') }}
                    </button>
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
    <script src="{{ asset('assets/js/custom/accountant-reconciliation.js') }}"></script>
@endpush
