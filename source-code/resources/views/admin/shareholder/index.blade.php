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
                            <i class="ri-add-line me-1"></i> {{ __('Add Shareholder') }}
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="shareholderDataTable" class="table table-bordered table-striped align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('SL') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Phone') }}</th>
                                        <th>{{ __('Share Class') }}</th>
                                        <th>{{ __('Shares Held') }}</th>
                                        <th>{{ __('Ownership %') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Hidden route inputs for JS --}}
                <input type="hidden" id="shareholderDataRoute" value="{{ route('admin.shareholders.data') }}">
                <input type="hidden" id="shareholderSuspendRoute" value="{{ route('admin.shareholders.suspend', '__ID__') }}">
                <input type="hidden" id="shareholderReactivateRoute" value="{{ route('admin.shareholders.reactivate', '__ID__') }}">
            </div>
        </div>
    </div>
</div>

{{-- Add Shareholder Modal --}}
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">{{ __('Add Shareholder') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <form action="{{ route('admin.shareholders.store') }}" method="POST" class="ajax" data-handler="getShowMessage">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('First Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required placeholder="{{ __('Enter first name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" required placeholder="{{ __('Enter last name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Email') }} <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required placeholder="{{ __('Enter email address') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <input type="text" name="phone" class="form-control" placeholder="{{ __('Enter phone number') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Share Class') }} <span class="text-danger">*</span></label>
                            <select name="share_class_id" class="form-select" required>
                                <option value="">{{ __('Select Share Class') }}</option>
                                @foreach($shareClasses as $shareClass)
                                    <option value="{{ $shareClass->id }}">{{ $shareClass->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Shares Held') }} <span class="text-danger">*</span></label>
                            <input type="number" name="shares_held" class="form-control" required min="0" step="1" placeholder="{{ __('Number of shares') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Voting Weight Per Share') }}</label>
                            <input type="number" name="voting_weight_per_share" class="form-control" min="0" step="0.0001" value="1" placeholder="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Ownership %') }}</label>
                            <div class="form-control bg-light text-muted" style="min-height:38px;">
                                <small>{{ __('Auto-calculated based on total shares issued.') }}</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info mb-0 py-2">
                                <i class="ri-mail-send-line me-1"></i>
                                {{ __('Credentials will be emailed to the shareholder automatically.') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Add Shareholder') }}</button>
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
    <script src="{{ asset('assets/js/custom/shareholder-admin.js') }}"></script>
@endpush
