@extends('owner.layouts.app')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="page-content-wrapper bg-white p-30 radius-20">

                    {{-- Page Title --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between border-bottom mb-20">
                                <div class="page-title-left">
                                    <h3 class="mb-sm-0">{{ $pageTitle }}</h3>
                                </div>
                                <div class="page-title-right">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('owner.dashboard') }}" title="{{ __('Dashboard') }}">{{ __('Dashboard') }}</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Filter Bar --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm" style="border-radius:12px;">
                                <div class="card-body py-3 px-4">
                                    <div class="row align-items-end g-3">
                                        <div class="col-md-5 col-lg-4">
                                            <label class="form-label fw-semibold mb-1 text-muted small">
                                                <i class="ri-building-line me-1"></i>{{ __('Property') }}
                                            </label>
                                            <select class="form-select property_id" id="search_property">
                                                <option value="">{{ __('All Properties') }}</option>
                                                @foreach ($properties as $property)
                                                    <option value="{{ $property->id }}">{{ $property->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-5 col-lg-4">
                                            <label class="form-label fw-semibold mb-1 text-muted small">
                                                <i class="ri-home-4-line me-1"></i>{{ __('Unit') }}
                                            </label>
                                            <select class="form-select unit_id">
                                                <option value="0">--{{ __('Select Unit') }}--</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Documents Table --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm" style="border-radius:12px; overflow:hidden;">
                                <div class="card-header bg-white border-bottom px-4 py-3 d-flex align-items-center gap-2">
                                    <i class="ri-file-shield-2-line fs-5 text-primary"></i>
                                    <h5 class="mb-0 fw-semibold">{{ __('All Documents') }}</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table id="allDataTableDoc" class="table table-hover align-middle mb-0 dt-responsive">
                                            <thead style="background:var(--t-bg-surface,#f8fafc);">
                                                <tr>
                                                    <th>{{ __('SL') }}</th>
                                                    <th>{{ __('Document Type') }}</th>
                                                    <th data-priority="1">{{ __('Tenant Name') }}</th>
                                                    <th class="desktop">{{ __('Tenant Name') }}</th>
                                                    <th>{{ __('Property') }}</th>
                                                    <th>{{ __('Unit') }}</th>
                                                    <th>{{ __('Front Side') }}</th>
                                                    <th>{{ __('Back Side') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                    <th>{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Hidden route inputs (moved out of duplicate main-content wrapper) --}}
    <input type="hidden" id="route" value="{{ route('owner.documents.index') }}?property_id=0&unit_id=0">
    <input type="hidden" id="getInfoRoute" value="{{ route('owner.documents.get.info') }}">
    <input type="hidden" id="getPropertyUnitsRoute" value="{{ route('owner.property.getPropertyUnits') }}">

    {{-- ===================== Reject Modal ===================== --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="rejectModalLabel">
                        <i class="ri-close-circle-line text-danger"></i>
                        {{ __('Reject Document') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>

                <form class="ajax" action="{{ route('owner.documents.reject.reason.store') }}" method="POST"
                    enctype="multipart/form-data" data-handler="getShowMessage"
                    style="display:flex; flex-direction:column; overflow:hidden; min-height:0; flex:1;">
                    <input type="hidden" name="id" class="id">

                    <div class="modal-body">

                        {{-- Config Name --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Document Type') }}</label>
                            <input type="text" class="form-control kyc_config_name bg-light" disabled>
                        </div>

                        {{-- Tenant / Property / Unit info --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="bg-light rounded p-3 h-100">
                                    <div class="text-muted small mb-1"><i class="ri-user-line me-1"></i>{{ __('Tenant') }}</div>
                                    <div class="fw-semibold tenant_name"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light rounded p-3 h-100">
                                    <div class="text-muted small mb-1"><i class="ri-building-line me-1"></i>{{ __('Property') }}</div>
                                    <div class="fw-semibold property_name"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light rounded p-3 h-100">
                                    <div class="text-muted small mb-1"><i class="ri-home-4-line me-1"></i>{{ __('Unit') }}</div>
                                    <div class="fw-semibold unit_name"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Document images --}}
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <div class="p-2 border rounded text-center" style="background:#f8fafc;">
                                    <div class="text-muted small mb-2 fw-semibold">
                                        <i class="ri-image-line me-1"></i>{{ __('Front Side') }}
                                    </div>
                                    <img src="" alt="{{ __('Front Side') }}"
                                        class="front-img img-fluid rounded"
                                        style="max-height:200px; object-fit:contain;">
                                </div>
                            </div>
                            <div class="col-12 col-md-6 d-none isBoth">
                                <div class="p-2 border rounded text-center" style="background:#f8fafc;">
                                    <div class="text-muted small mb-2 fw-semibold">
                                        <i class="ri-image-2-line me-1"></i>{{ __('Back Side') }}
                                    </div>
                                    <img src="" alt="{{ __('Back Side') }}"
                                        class="back-img img-fluid rounded"
                                        style="max-height:200px; object-fit:contain;">
                                </div>
                            </div>
                        </div>

                        {{-- Reason textarea --}}
                        <div class="mb-2">
                            <label class="form-label fw-semibold">
                                {{ __('Rejection Reason') }} <span class="text-danger">*</span>
                            </label>
                            <textarea name="reason" rows="4"
                                class="form-control reason"
                                placeholder="{{ __('Explain why this document is being rejected...') }}"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-danger d-flex align-items-center gap-2">
                            <i class="ri-close-circle-line"></i>{{ __('Reject Document') }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    {{-- ===================== End Reject Modal ===================== --}}

    {{-- ===================== View Modal ===================== --}}
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="viewModalLabel">
                        <i class="ri-file-search-line text-primary"></i>
                        {{ __('Document Details') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>

                <div class="modal-body">

                    {{-- Document images --}}
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <div class="p-3 border rounded text-center" style="background:#f8fafc; border-radius:10px!important;">
                                <div class="text-muted small fw-semibold mb-2">
                                    <i class="ri-image-line me-1"></i>{{ __('Front Side') }}
                                </div>
                                <img src="" alt="{{ __('Front Side') }}"
                                    class="front-img img-fluid rounded"
                                    style="max-width:100%; border-radius:8px; object-fit:contain; max-height:260px;">
                                <div class="mt-2">
                                    <a href="#" class="btn btn-sm btn-outline-secondary front-download" target="_blank" download>
                                        <i class="ri-download-2-line me-1"></i>{{ __('Download') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 d-none isBoth">
                            <div class="p-3 border rounded text-center" style="background:#f8fafc; border-radius:10px!important;">
                                <div class="text-muted small fw-semibold mb-2">
                                    <i class="ri-image-2-line me-1"></i>{{ __('Back Side') }}
                                </div>
                                <img src="" alt="{{ __('Back Side') }}"
                                    class="back-img img-fluid rounded"
                                    style="max-width:100%; border-radius:8px; object-fit:contain; max-height:260px;">
                                <div class="mt-2">
                                    <a href="#" class="btn btn-sm btn-outline-secondary back-download" target="_blank" download>
                                        <i class="ri-download-2-line me-1"></i>{{ __('Download') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Info fields in 2-col grid --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="bg-light rounded p-3">
                                <div class="text-muted small mb-1">{{ __('Document Type') }}</div>
                                <div class="fw-semibold config_name"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded p-3">
                                <div class="text-muted small mb-1"><i class="ri-user-line me-1"></i>{{ __('Tenant') }}</div>
                                <div class="fw-semibold tenant_name"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded p-3">
                                <div class="text-muted small mb-1"><i class="ri-building-line me-1"></i>{{ __('Property') }}</div>
                                <div class="fw-semibold property_name"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded p-3">
                                <div class="text-muted small mb-1"><i class="ri-home-4-line me-1"></i>{{ __('Unit') }}</div>
                                <div class="fw-semibold unit_name"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Rejection reason (only shown when status == 3) --}}
                    <div class="alert alert-danger d-flex align-items-start gap-2 d-none reasonDiv" role="alert">
                        <i class="ri-error-warning-line flex-shrink-0 mt-1"></i>
                        <div>
                            <div class="fw-semibold mb-1">{{ __('Rejection Reason') }}</div>
                            <span class="reason"></span>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('Close') }}
                    </button>
                </div>

            </div>
        </div>
    </div>
    {{-- ===================== End View Modal ===================== --}}

@endsection

@push('style')
    @include('common.layouts.datatable-style')
@endpush

@push('script')
    @include('common.layouts.datatable-script')
    <script src="{{ asset('assets/js/custom/documents.js') }}"></script>
@endpush
