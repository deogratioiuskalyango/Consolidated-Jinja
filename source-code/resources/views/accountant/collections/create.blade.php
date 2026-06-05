@php $navCollectionsActiveClass = 'active mm-active'; @endphp
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
                                <h3 class="mb-sm-0">{{ __('Record Payment') }}</h3>
                            </div>
                            <div class="page-title-right">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('accountant.collections.index') }}">{{ __('Collections') }}</a></li>
                                    <li class="breadcrumb-item active">{{ __('Record Payment') }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form --}}
                <div class="row">
                    <div class="col-12">
                        <form action="{{ route('accountant.collections.store') }}"
                              method="POST"
                              class="ajax"
                              data-handler="collectionShowMessage">
                            @csrf

                            {{-- Row 1: Tenant & Property --}}
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Tenant') }} <span class="text-danger">*</span></label>
                                    <select name="tenant_id" class="form-select" required>
                                        <option value="">{{ __('Select Tenant') }}</option>
                                        @foreach($tenants as $tenant)
                                            <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                                {{ $tenant->name }}@if($tenant->unit) — {{ $tenant->unit->unit_number ?? $tenant->unit->name }}@endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tenant_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Property') }} <span class="text-danger">*</span></label>
                                    <select name="property_id" class="form-select" required>
                                        <option value="">{{ __('Select Property') }}</option>
                                        @foreach($properties as $property)
                                            <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                                {{ $property->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('property_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Row 2: Collection Type & Payment Method --}}
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Collection Type') }} <span class="text-danger">*</span></label>
                                    <select name="collection_type" class="form-select" required>
                                        <option value="">{{ __('Select Type') }}</option>
                                        @foreach($collectionTypes as $key => $label)
                                            <option value="{{ $key }}" {{ old('collection_type') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('collection_type')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Payment Method') }} <span class="text-danger">*</span></label>
                                    <select name="payment_method" class="form-select" required>
                                        <option value="">{{ __('Select Method') }}</option>
                                        @foreach($paymentMethods as $key => $label)
                                            <option value="{{ $key }}" {{ old('payment_method') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payment_method')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Row 3: Amount & Payment Date --}}
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Amount (UGX)') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">UGX</span>
                                        <input type="number"
                                               name="amount"
                                               class="form-control"
                                               step="0.01"
                                               min="0"
                                               required
                                               value="{{ old('amount') }}"
                                               placeholder="0.00">
                                    </div>
                                    @error('amount')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Payment Date') }} <span class="text-danger">*</span></label>
                                    <input type="date"
                                           name="payment_date"
                                           class="form-control"
                                           required
                                           value="{{ old('payment_date', date('Y-m-d')) }}">
                                    @error('payment_date')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Row 4: Transaction Reference & Notes --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Transaction Reference') }}</label>
                                    <input type="text"
                                           name="transaction_ref"
                                           class="form-control"
                                           value="{{ old('transaction_ref') }}"
                                           placeholder="{{ __('e.g. Bank ref, mobile money ID') }}">
                                    @error('transaction_ref')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Notes') }}</label>
                                    <textarea name="notes"
                                              class="form-control"
                                              rows="3"
                                              placeholder="{{ __('Any additional notes...') }}">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Redirect helper --}}
                            <input type="hidden" id="collectionIndexUrl" value="{{ route('accountant.collections.index') }}">

                            {{-- Submit --}}
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i> {{ __('Record Payment') }}
                                </button>
                                <a href="{{ route('accountant.collections.index') }}" class="btn btn-outline-secondary">
                                    {{ __('Cancel') }}
                                </a>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    window.collectionShowMessage = function (response) {
        if (response.status === 'success') {
            window.location.href = document.getElementById('collectionIndexUrl').value;
        } else {
            commonHandler(response);
        }
    };
</script>
@endpush
