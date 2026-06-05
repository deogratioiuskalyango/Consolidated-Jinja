@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="page-content-wrapper bg-white p-30 radius-20">

                {{-- Page Header --}}
                <div class="d-sm-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                    <div>
                        <h3 class="mb-1">{{ __('Create Contract') }}</h3>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.contracts.index') }}">{{ __('Contracts') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('Create') }}</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.contracts.index') }}" class="btn btn-outline-secondary">
                        <i class="ri-arrow-left-line me-1"></i>{{ __('Back') }}
                    </a>
                </div>

                {{-- Validation Errors --}}
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.contracts.store') }}" method="POST">
                    @csrf

                    {{-- ── Tenant Selection ─────────────────────────────────────────── --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ri-user-line me-2"></i>{{ __('Section A — Tenant') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">
                                        {{ __('Tenant') }} <span class="text-danger">*</span>
                                    </label>
                                    <select name="tenant_id" id="tenantSelect" class="form-select @error('tenant_id') is-invalid @enderror" required>
                                        <option value="">{{ __('— Select Tenant —') }}</option>
                                        @foreach($tenants as $tenant)
                                            <option value="{{ $tenant->id }}"
                                                {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                                {{ $tenant->user?->first_name }} {{ $tenant->user?->last_name }}
                                                @if($tenant->property)
                                                    — {{ $tenant->property->name }}
                                                @endif
                                                @if($tenant->unit)
                                                    ({{ $tenant->unit->unit_name }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tenant_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        {{ __('Tenant name, email, phone, property and unit will be auto-populated from the selected tenant.') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Section C — Payment ─────────────────────────────────────── --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ri-money-dollar-circle-line me-2"></i>{{ __('Section C — Payment') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">
                                        {{ __('Monthly Rent') }} <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">UGX</span>
                                        <input type="number" name="monthly_rent" step="0.01" min="0"
                                               class="form-control @error('monthly_rent') is-invalid @enderror"
                                               value="{{ old('monthly_rent') }}" required>
                                        @error('monthly_rent')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        {{ __('Security Deposit') }} <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">UGX</span>
                                        <input type="number" name="security_deposit" step="0.01" min="0"
                                               class="form-control @error('security_deposit') is-invalid @enderror"
                                               value="{{ old('security_deposit') }}" required>
                                        @error('security_deposit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">{{ __('Currency') }}</label>
                                    <input type="text" name="currency" class="form-control"
                                           value="{{ old('currency', 'UGX') }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">{{ __('Payment Due Day') }}</label>
                                    <select name="payment_due_day" class="form-select @error('payment_due_day') is-invalid @enderror">
                                        @for($d = 1; $d <= 28; $d++)
                                            <option value="{{ $d }}" {{ old('payment_due_day', 5) == $d ? 'selected' : '' }}>
                                                {{ __('Day') }} {{ $d }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('payment_due_day')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">{{ __('Payment Method') }}</label>
                                    <select name="payment_method" class="form-select">
                                        <option value="">{{ __('— Select —') }}</option>
                                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>{{ __('Bank Transfer') }}</option>
                                        <option value="mobile_money"  {{ old('payment_method') == 'mobile_money'  ? 'selected' : '' }}>{{ __('Mobile Money') }}</option>
                                        <option value="cash"          {{ old('payment_method') == 'cash'          ? 'selected' : '' }}>{{ __('Cash') }}</option>
                                        <option value="cheque"        {{ old('payment_method') == 'cheque'        ? 'selected' : '' }}>{{ __('Cheque') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Section D — Bank Details ─────────────────────────────────── --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ri-bank-line me-2"></i>{{ __('Section D — Bank Details') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('Bank Name') }}</label>
                                    <input type="text" name="bank_name" class="form-control"
                                           value="{{ old('bank_name') }}" placeholder="{{ __('e.g. Stanbic Bank') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('Account Name') }}</label>
                                    <input type="text" name="bank_account_name" class="form-control"
                                           value="{{ old('bank_account_name') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('Account Number') }}</label>
                                    <input type="text" name="bank_account_number" class="form-control"
                                           value="{{ old('bank_account_number') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('Bank Branch') }}</label>
                                    <input type="text" name="bank_branch" class="form-control"
                                           value="{{ old('bank_branch') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('Mobile Money Number') }}</label>
                                    <input type="text" name="mobile_money_number" class="form-control"
                                           value="{{ old('mobile_money_number') }}" placeholder="+256...">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Section E — Period ───────────────────────────────────────── --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ri-calendar-line me-2"></i>{{ __('Section E — Tenancy Period') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">
                                        {{ __('Commencement Date') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="commencement_date"
                                           class="form-control @error('commencement_date') is-invalid @enderror"
                                           value="{{ old('commencement_date') }}" required>
                                    @error('commencement_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">
                                        {{ __('Expiry Date') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="expiry_date"
                                           class="form-control @error('expiry_date') is-invalid @enderror"
                                           value="{{ old('expiry_date') }}" required>
                                    @error('expiry_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('Notice Period (days)') }}</label>
                                    <input type="number" name="notice_period_days" min="1" class="form-control"
                                           value="{{ old('notice_period_days', 30) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── LC1 Chairperson ──────────────────────────────────────────── --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ri-government-line me-2"></i>{{ __('LC1 Chairperson') }}
                                <small class="text-muted fw-normal ms-2">{{ __('(optional)') }}</small>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('LC1 Name') }}</label>
                                    <input type="text" name="lc1_name" class="form-control"
                                           value="{{ old('lc1_name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('LC1 Phone') }}</label>
                                    <input type="text" name="lc1_phone" class="form-control"
                                           value="{{ old('lc1_phone') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Witnesses ─────────────────────────────────────────────────── --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ri-group-line me-2"></i>{{ __('Witnesses') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Witness 1 Name') }}</label>
                                    <input type="text" name="witness1_name" class="form-control"
                                           value="{{ old('witness1_name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Witness 2 Name') }}</label>
                                    <input type="text" name="witness2_name" class="form-control"
                                           value="{{ old('witness2_name') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Special Conditions ───────────────────────────────────────── --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ri-sticky-note-line me-2"></i>{{ __('Special Conditions') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <textarea name="special_conditions" rows="4" class="form-control"
                                      placeholder="{{ __('Enter any special conditions or additional clauses…') }}">{{ old('special_conditions') }}</textarea>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.contracts.index') }}" class="btn btn-outline-secondary">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i>{{ __('Create Contract') }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
