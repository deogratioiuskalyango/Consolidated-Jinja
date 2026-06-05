@extends('owner.layouts.app')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="page-content-wrapper bg-white p-30 radius-20">

                    {{-- Breadcrumb --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between border-bottom mb-20">
                                <div class="page-title-left">
                                    <h3 class="mb-sm-0">{{ __('Settings') }}</h3>
                                </div>
                                <div class="page-title-right">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('owner.dashboard') }}" title="{{ __('Dashboard') }}">{{ __('Dashboard') }}</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <a href="#" title="{{ __('Settings') }}">{{ __('Settings') }}</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Settings Layout --}}
                    <div class="settings-page-layout-wrap position-relative">
                        <div class="row">
                            @include('owner.setting.sidebar')

                            <div class="col-md-12 col-lg-12 col-xl-8 col-xxl-9">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                                        <h5 class="mb-0 fw-semibold">{{ $pageTitle }}</h5>
                                        <button type="button" class="btn btn-primary btn-sm" id="add" title="{{ __('Add Document Config') }}">
                                            <i class="ri-add-line me-1"></i>{{ __('Add Document Config') }}
                                        </button>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="table-responsive">
                                            <table id="allDataTable" class="table table-hover align-middle dt-responsive w-100">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>{{ __('Demo') }}</th>
                                                        <th data-priority="1">{{ __('Name') }}</th>
                                                        <th>{{ __('Tenant') }}</th>
                                                        <th>{{ __('Details') }}</th>
                                                        <th>{{ __('Both Sides') }}</th>
                                                        <th>{{ __('Status') }}</th>
                                                        <th>{{ __('Actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($kycConfigs as $kycConfig)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>

                                                            <td>
                                                                @if ($kycConfig->image)
                                                                    <img src="{{ $kycConfig->image }}"
                                                                         width="50" height="40"
                                                                         style="object-fit:cover;border-radius:4px;"
                                                                         alt="{{ __('Demo') }}">
                                                                @else
                                                                    <span class="text-muted">&mdash;</span>
                                                                @endif
                                                            </td>

                                                            <td>{{ $kycConfig->name }}</td>

                                                            <td>
                                                                @if ($kycConfig->first_name)
                                                                    {{ $kycConfig->first_name }} {{ $kycConfig->last_name }}
                                                                @else
                                                                    {{ __('All') }}
                                                                @endif
                                                            </td>

                                                            <td>{{ Str::limit($kycConfig->details, 25, '...') }}</td>

                                                            <td>
                                                                @if ($kycConfig->is_both == ACTIVE)
                                                                    <span class="badge bg-success">{{ __('Yes') }}</span>
                                                                @else
                                                                    <span class="badge bg-danger">{{ __('No') }}</span>
                                                                @endif
                                                            </td>

                                                            <td>
                                                                @if ($kycConfig->status == ACTIVE)
                                                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                                                @else
                                                                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                                                @endif
                                                            </td>

                                                            <td>
                                                                <div class="d-inline-flex gap-1">
                                                                    <button type="button"
                                                                            class="btn btn-sm btn-outline-secondary edit"
                                                                            data-id="{{ $kycConfig->id }}"
                                                                            title="{{ __('Edit') }}">
                                                                        <i class="ri-edit-line"></i>
                                                                    </button>
                                                                    <button type="button"
                                                                            class="btn btn-sm btn-outline-danger deleteItem"
                                                                            data-formid="delete_row_form_{{ $kycConfig->id }}"
                                                                            title="{{ __('Delete') }}">
                                                                        <i class="ri-delete-bin-line"></i>
                                                                    </button>
                                                                    <form action="{{ route('owner.setting.document-config.delete', [$kycConfig->id]) }}"
                                                                          method="POST"
                                                                          id="delete_row_form_{{ $kycConfig->id }}"
                                                                          class="d-none">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
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
    </div>

    {{-- ===================== Add Modal ===================== --}}
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">{{ __('Add Document Config') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <form class="ajax" action="{{ route('owner.setting.document-config.store') }}" method="POST"
                      data-handler="getShowMessage" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">

                            {{-- Tenant --}}
                            <div class="col-12">
                                <label class="form-label fw-medium">{{ __('Tenant') }}</label>
                                <select name="tenant_id" class="form-select">
                                    <option value="">{{ __('All') }}</option>
                                    @foreach ($tenants as $tenant)
                                        <option value="{{ $tenant->id }}">
                                            {{ $tenant->first_name }} {{ $tenant->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Name --}}
                            <div class="col-12">
                                <label class="form-label fw-medium">{{ __('Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                       placeholder="{{ __('Name') }}" required>
                            </div>

                            {{-- Details --}}
                            <div class="col-12">
                                <label class="form-label fw-medium">{{ __('Details') }}</label>
                                <textarea name="details" class="form-control" rows="3"
                                          placeholder="{{ __('Details') }}"></textarea>
                            </div>

                            {{-- Demo File --}}
                            <div class="col-12">
                                <label class="form-label fw-medium">
                                    {{ __('Demo File') }} <span class="text-muted small">({{ __('Optional') }})</span>
                                </label>
                                <input type="file" name="demo" class="form-control">
                            </div>

                            {{-- Status --}}
                            <div class="col-12">
                                <label class="form-label fw-medium">{{ __('Status') }}</label>
                                <select name="status" class="form-select">
                                    <option value="1">{{ __('Active') }}</option>
                                    <option value="0">{{ __('Deactive') }}</option>
                                </select>
                            </div>

                            {{-- Both Side --}}
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="isBoth" name="is_both">
                                    <label class="form-check-label fw-medium" for="isBoth">
                                        {{ __('Both Side') }}
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            {{ __('Submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===================== Edit Modal ===================== --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">{{ __('Edit Document Config') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <form class="ajax" action="{{ route('owner.setting.document-config.store') }}" method="POST"
                      data-handler="getShowMessage" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row g-3">

                            {{-- Tenant --}}
                            <div class="col-12">
                                <label class="form-label fw-medium">{{ __('Tenant') }}</label>
                                <select name="tenant_id" class="form-select tenant_id">
                                    <option value="">{{ __('All') }}</option>
                                    @foreach ($tenants as $tenant)
                                        <option value="{{ $tenant->id }}">
                                            {{ $tenant->first_name }} {{ $tenant->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Name --}}
                            <div class="col-12">
                                <label class="form-label fw-medium">{{ __('Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control name"
                                       placeholder="{{ __('Name') }}" required>
                            </div>

                            {{-- Details --}}
                            <div class="col-12">
                                <label class="form-label fw-medium">{{ __('Details') }}</label>
                                <textarea name="details" class="form-control details" rows="3"
                                          placeholder="{{ __('Details') }}"></textarea>
                            </div>

                            {{-- Demo File --}}
                            <div class="col-12">
                                <label class="form-label fw-medium">
                                    {{ __('Demo File') }} <span class="text-muted small">({{ __('Optional') }})</span>
                                </label>
                                <input type="file" name="demo" class="form-control">
                            </div>

                            {{-- Status --}}
                            <div class="col-12">
                                <label class="form-label fw-medium">{{ __('Status') }}</label>
                                <select name="status" class="form-select status">
                                    <option value="1">{{ __('Active') }}</option>
                                    <option value="0">{{ __('Deactive') }}</option>
                                </select>
                            </div>

                            {{-- Both Side --}}
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="isBothEdit" name="is_both">
                                    <label class="form-check-label fw-medium" for="isBothEdit">
                                        {{ __('Both Side') }}
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            {{ __('Update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- JS hook for AJAX get-info route --}}
    <input type="hidden" id="getInfoRoute" value="{{ route('owner.setting.document-config.get.info') }}">
@endsection

@push('style')
    @include('common.layouts.datatable-style')
@endpush

@push('script')
    @include('common.layouts.datatable-script')
    <script src="{{ asset('/') }}assets/js/pages/alldatatables.init.js"></script>
    <script src="{{ asset('assets/js/custom/document-config.js') }}"></script>
@endpush
