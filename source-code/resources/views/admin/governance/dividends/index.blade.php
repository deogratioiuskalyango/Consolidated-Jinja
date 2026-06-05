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
                            <i class="ri-add-line me-1"></i> {{ __('Declare Dividend') }}
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="dividendsDataTable" class="table table-bordered table-striped align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('SL') }}</th>
                                        <th>{{ __('Period Label') }}</th>
                                        <th>{{ __('Per-Share Amount') }}</th>
                                        <th>{{ __('Currency') }}</th>
                                        <th>{{ __('Total Pool') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Declaration Date') }}</th>
                                        <th>{{ __('Record Date') }}</th>
                                        <th>{{ __('Payment Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($declarations as $index => $declaration)
                                    <tr>
                                        <td>{{ $declarations->firstItem() + $index }}</td>
                                        <td>{{ $declaration->title }}</td>
                                        <td>{{ number_format($declaration->per_share_amount, 4) }}</td>
                                        <td>{{ $declaration->currency }}</td>
                                        <td>{{ number_format($declaration->total_amount, 2) }}</td>
                                        <td>
                                            @if($declaration->status == DIVIDEND_STATUS_DECLARED)
                                                <span class="badge bg-primary">{{ __('Declared') }}</span>
                                            @elseif($declaration->status == DIVIDEND_STATUS_PAID)
                                                <span class="badge bg-success">{{ __('Paid') }}</span>
                                            @elseif($declaration->status == DIVIDEND_STATUS_CANCELLED)
                                                <span class="badge bg-danger">{{ __('Cancelled') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $declaration->declaration_date ? \Carbon\Carbon::parse($declaration->declaration_date)->format('d M Y') : '—' }}</td>
                                        <td>—</td>
                                        <td>{{ $declaration->payment_date ? \Carbon\Carbon::parse($declaration->payment_date)->format('d M Y') : '—' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $declarations->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Declare Dividend Modal --}}
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">{{ __('Declare Dividend') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <form action="{{ route('admin.governance.dividends.store') }}" method="POST" class="ajax" data-handler="getShowMessage">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required placeholder="{{ __('e.g. Q1 2026 Dividend') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Reference Number') }}</label>
                            <input type="text" name="reference_number" class="form-control" placeholder="{{ __('Auto-generated if empty') }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Total Amount') }} <span class="text-danger">*</span></label>
                            <input type="number" name="total_amount" class="form-control" required min="0" step="0.01" placeholder="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Per-Share Amount') }} <span class="text-danger">*</span></label>
                            <input type="number" name="per_share_amount" class="form-control" required min="0" step="0.0001" placeholder="0.0000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Currency') }}</label>
                            <input type="text" name="currency" class="form-control" value="UGX" placeholder="UGX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Declaration Date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="declaration_date" class="form-control" required value="{{ now()->toDateString() }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Payment Date') }}</label>
                            <input type="date" name="payment_date" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('Notes') }}</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="{{ __('Optional notes') }}"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info mb-0 py-2">
                                <i class="ri-information-line me-1"></i>
                                {{ __('Individual shareholder payments will be auto-calculated based on shares held.') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Declare Dividend') }}</button>
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
    <script src="{{ asset('assets/js/custom/governance-dividends.js') }}"></script>
@endpush
