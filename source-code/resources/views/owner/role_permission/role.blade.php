@extends('owner.layouts.app')

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
                                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}" title="{{ __('Dashboard') }}">{{ __('Dashboard') }}</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tab Navigation --}}
                    <ul class="nav nav-tabs mb-25" id="rolePermissionTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="system-roles-tab" data-bs-toggle="tab" data-bs-target="#system-roles" type="button" role="tab" aria-controls="system-roles" aria-selected="true">
                                <i class="ri-shield-line me-1"></i> {{ __('System Roles') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="user-assignments-tab" data-bs-toggle="tab" data-bs-target="#user-assignments" type="button" role="tab" aria-controls="user-assignments" aria-selected="false">
                                <i class="ri-user-settings-line me-1"></i> {{ __('User Role Assignments') }}
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="rolePermissionTabContent">

                        {{-- Tab 1: System Roles --}}
                        <div class="tab-pane fade show active" id="system-roles" role="tabpanel" aria-labelledby="system-roles-tab">
                            <div class="row">
                                <div class="property-top-search-bar">
                                    <div class="row align-items-center">
                                        <div class="col-md-12">
                                            <div class="property-top-search-bar-right text-end">
                                                <button type="button" class="theme-btn mb-25" id="add" title="{{ __('Add Role') }}">{{ __('Add Role') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="billing-center-area bg-off-white theme-border radius-4 p-25">
                                    <table id="roleListDataTable" class="table responsive theme-border p-20">
                                        <thead>
                                            <th>{{ __('Id') }}</th>
                                            <th>{{ __('Name') }}</th>
                                            <th class="text-end">{{ __('Status') }}</th>
                                            <th class="text-end">{{ __('Action') }}</th>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                            {{-- System Role Reference Card --}}
                            <div class="row mt-30">
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light d-flex align-items-center gap-2 py-3">
                                            <i class="ri-information-line fs-5 text-primary"></i>
                                            <h5 class="mb-0">{{ __('System Role Reference') }}</h5>
                                            <span class="ms-auto badge bg-secondary">Read-Only</span>
                                        </div>
                                        <div class="card-body p-25">
                                            <p class="text-muted small mb-20">These are the built-in system roles available for assignment. They are pre-defined and cannot be edited here.</p>
                                            <div class="row g-3">
                                                @php
                                                    $systemRoles = [
                                                        ['slug' => 'admin',              'label' => 'System Administrator', 'icon' => 'ri-shield-star-line',      'color' => 'danger',    'desc' => 'Full system access with all privileges.'],
                                                        ['slug' => 'property_manager',   'label' => 'Property Manager',     'icon' => 'ri-building-2-line',        'color' => 'primary',   'desc' => 'Manages property listings and details.'],
                                                        ['slug' => 'accountant',         'label' => 'Accountant',           'icon' => 'ri-calculator-line',        'color' => 'success',   'desc' => 'Handles financial records and transactions.'],
                                                        ['slug' => 'shareholder',        'label' => 'Shareholder',          'icon' => 'ri-stock-line',             'color' => 'warning',   'desc' => 'Holds shares and views financial reports.'],
                                                        ['slug' => 'director',           'label' => 'Director',             'icon' => 'ri-user-star-line',         'color' => 'dark',      'desc' => 'Executive-level oversight and approvals.'],
                                                        ['slug' => 'landlord',           'label' => 'Landlord',             'icon' => 'ri-home-gear-line',         'color' => 'info',      'desc' => 'Owns properties and manages tenants.'],
                                                        ['slug' => 'tenant_manager',     'label' => 'Tenant Manager',       'icon' => 'ri-group-line',             'color' => 'primary',   'desc' => 'Manages tenant relationships and agreements.'],
                                                        ['slug' => 'maintenance_tech',   'label' => 'Maintenance Tech',     'icon' => 'ri-tools-line',             'color' => 'secondary', 'desc' => 'Handles maintenance requests and repairs.'],
                                                        ['slug' => 'compliance_officer', 'label' => 'Compliance Officer',   'icon' => 'ri-file-shield-2-line',     'color' => 'warning',   'desc' => 'Ensures regulatory and legal compliance.'],
                                                        ['slug' => 'secretary',          'label' => 'Secretary',            'icon' => 'ri-quill-pen-line',         'color' => 'info',      'desc' => 'Manages scheduling, documents, and communications.'],
                                                        ['slug' => 'auditor',            'label' => 'Auditor',              'icon' => 'ri-search-eye-line',        'color' => 'dark',      'desc' => 'Reviews and audits accounts and operations.'],
                                                        ['slug' => 'finance_manager',    'label' => 'Finance Manager',      'icon' => 'ri-bank-line',              'color' => 'success',   'desc' => 'Oversees budgets, forecasts, and financial planning.'],
                                                    ];
                                                @endphp
                                                @foreach($systemRoles as $sr)
                                                    <div class="col-md-3 col-sm-6">
                                                        <div class="d-flex align-items-start gap-3 p-15 border radius-8 h-100 bg-light-subtle">
                                                            <div class="flex-shrink-0">
                                                                <span class="badge bg-{{ $sr['color'] }} p-2 fs-5 lh-1">
                                                                    <i class="{{ $sr['icon'] }}"></i>
                                                                </span>
                                                            </div>
                                                            <div class="flex-grow-1 min-width-0">
                                                                <div class="fw-semibold text-truncate">{{ $sr['label'] }}</div>
                                                                <div class="text-muted small mt-1">{{ $sr['desc'] }}</div>
                                                                <code class="small text-secondary">{{ $sr['slug'] }}</code>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 2: User Role Assignments --}}
                        <div class="tab-pane fade" id="user-assignments" role="tabpanel" aria-labelledby="user-assignments-tab">
                            {{-- Summary Cards --}}
                            <div class="row g-3 mb-25">
                                <div class="col-md-4">
                                    <div class="card border-start border-primary border-3 shadow-sm h-100">
                                        <div class="card-body d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                                <i class="ri-group-line fs-4 text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fs-4 fw-bold" id="statTotalAssignments">—</div>
                                                <div class="text-muted small">Total Assignments</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-start border-success border-3 shadow-sm h-100">
                                        <div class="card-body d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                                <i class="ri-checkbox-circle-line fs-4 text-success"></i>
                                            </div>
                                            <div>
                                                <div class="fs-4 fw-bold" id="statActiveAssignments">—</div>
                                                <div class="text-muted small">Active Assignments</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-start border-warning border-3 shadow-sm h-100">
                                        <div class="card-body d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                                                <i class="ri-user-star-line fs-4 text-warning"></i>
                                            </div>
                                            <div>
                                                <div class="fs-4 fw-bold" id="statMultiRole">—</div>
                                                <div class="text-muted small">Users with Multiple Roles</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="property-top-search-bar mb-20">
                                <div class="row align-items-center">
                                    <div class="col-md-12">
                                        <div class="property-top-search-bar-right text-end">
                                            <button type="button" class="theme-btn" id="openAssignRoleModal" data-bs-toggle="modal" data-bs-target="#assignRoleModal" title="{{ __('Assign Role') }}">
                                                <i class="ri-user-add-line me-1"></i>{{ __('Assign Role') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="billing-center-area bg-off-white theme-border radius-4 p-25">
                                <table id="assignmentsDataTable" class="table responsive theme-border p-20">
                                    <thead>
                                        <th>#</th>
                                        <th>{{ __('User') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Role') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Assigned Date') }}</th>
                                        <th class="text-end">{{ __('Action') }}</th>
                                    </thead>
                                </table>
                            </div>
                        </div>

                    </div>{{-- end tab-content --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Add Role Modal --}}
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addModalLabel"><span class="modalTitle">{{ __('Add Role') }}</span></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span class="iconify" data-icon="akar-icons:cross"></span></button>
                </div>
                <form class="ajax" action="{{ route('owner.role-permission.store') }}" method="post" enctype="multipart/form-data" data-handler="getShowMessage">
                    <div class="modal-body">
                        <div class="modal-inner-form-box">
                            <div class="row">
                                <div class="col-md-12 mb-25">
                                    <label class="label-text-title color-heading font-medium mb-2">{{ __('Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="{{ __('Name') }}">
                                </div>
                            </div>
                            <div class="col-md-12 mb-25">
                                <label class="label-text-title color-heading font-medium mb-2">{{ __('Status') }} <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1">{{ __('Active') }}</option>
                                    <option value="0">{{ __('Deactivate') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-start">
                        <a href="javascript:void(0)" class="theme-btn-back me-3" data-bs-dismiss="modal" title="{{ __('Back') }}">{{ __('Back') }}</a>
                        <button type="submit" class="theme-btn me-3" title="{{ __('Add Role') }}">{{ __('Add Role') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Role Modal --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel"><span class="modalTitle">{{ __('Edit Package') }}</span></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span class="iconify" data-icon="akar-icons:cross"></span></button>
                </div>
                <form class="ajax" action="{{ route('owner.role-permission.store') }}" method="post" enctype="multipart/form-data" data-handler="getShowMessage">
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="modal-inner-form-box">
                            <div class="row">
                                <div class="col-md-12 mb-25">
                                    <label class="label-text-title color-heading font-medium mb-2">{{ __('Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="{{ __('Name') }}">
                                </div>
                            </div>
                            <div class="col-md-12 mb-25">
                                <label class="label-text-title color-heading font-medium mb-2">{{ __('Status') }} <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1">{{ __('Active') }}</option>
                                    <option value="0">{{ __('Deactivate') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-start">
                        <a href="javascript:void(0)" class="theme-btn-back me-3" data-bs-dismiss="modal" title="{{ __('Back') }}">{{ __('Back') }}</a>
                        <button type="submit" class="theme-btn me-3" title="{{ __('Add Role') }}">{{ __('Update Role') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Permission Modal --}}
    <div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            </div>
        </div>
    </div>

    {{-- Assign Role Modal --}}
    <div class="modal fade" id="assignRoleModal" tabindex="-1" aria-labelledby="assignRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="assignRoleModalLabel">{{ __('Assign Role to User') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span class="iconify" data-icon="akar-icons:cross"></span></button>
                </div>
                <form class="ajax" action="{{ route('owner.role-permission.assign-role') }}" method="post" data-handler="assignRoleShowMessage">
                    @csrf
                    <div class="modal-body">
                        <div class="modal-inner-form-box">
                            <div class="row">
                                <div class="col-md-12 mb-25">
                                    <label class="label-text-title color-heading font-medium mb-2">{{ __('User') }} <span class="text-danger">*</span></label>
                                    <select name="user_id" id="assign_user_id" class="form-control">
                                        <option value="">{{ __('Loading users…') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-25">
                                    <label class="label-text-title color-heading font-medium mb-2">{{ __('Role') }} <span class="text-danger">*</span></label>
                                    <select name="role_slug" id="assign_role_slug" class="form-control">
                                        <option value="">{{ __('Select Role') }}</option>
                                        <option value="admin">System Administrator</option>
                                        <option value="property_manager">Property Manager</option>
                                        <option value="accountant">Accountant</option>
                                        <option value="shareholder">Shareholder</option>
                                        <option value="director">Director</option>
                                        <option value="landlord">Landlord</option>
                                        <option value="tenant_manager">Tenant Manager</option>
                                        <option value="maintenance_tech">Maintenance Tech</option>
                                        <option value="compliance_officer">Compliance Officer</option>
                                        <option value="secretary">Secretary</option>
                                        <option value="auditor">Auditor</option>
                                        <option value="finance_manager">Finance Manager</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-25">
                                    <label class="label-text-title color-heading font-medium mb-2">{{ __('Notes') }}</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="{{ __('Optional notes about this assignment…') }}" maxlength="500"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-start">
                        <a href="javascript:void(0)" class="theme-btn-back me-3" data-bs-dismiss="modal" title="{{ __('Back') }}">{{ __('Back') }}</a>
                        <button type="submit" class="theme-btn me-3" title="{{ __('Assign Role') }}">{{ __('Assign Role') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Hidden route inputs --}}
    <input type="hidden" id="roleIndexRoute"        value="{{ route('owner.role-permission.role-list') }}">
    <input type="hidden" id="roleInfoRoute"         value="{{ route('owner.role-permission.get-info') }}">
    <input type="hidden" id="assignmentsRoute"      value="{{ route('owner.role-permission.user-assignments') }}">
    <input type="hidden" id="usersForAssignRoute"   value="{{ route('owner.role-permission.users-for-assignment') }}">
    <input type="hidden" id="revokeRoleRoute"       value="{{ route('owner.role-permission.revoke-role') }}">
    <input type="hidden" id="assignRoleRoute"       value="{{ route('owner.role-permission.assign-role') }}">

@endsection

@push('style')
    @include('common.layouts.datatable-style')
@endpush

@push('script')
    @include('common.layouts.datatable-script')
    <script src="{{ asset('assets/js/custom/role-permission.js') }}"></script>
    <script>
        $(document).ready(function () {

            // ── Assignments DataTable ──────────────────────────────────────────
            var assignmentsTable = $('#assignmentsDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: $('#assignmentsRoute').val(),
                columns: [
                    { data: 'DT_RowIndex',   orderable: false, searchable: false },
                    { data: 'user_name' },
                    { data: 'user_email' },
                    { data: 'role_label' },
                    { data: 'status_badge',  searchable: false },
                    { data: 'assigned_date' },
                    { data: 'action',        orderable: false, searchable: false, className: 'text-end' }
                ],
                drawCallback: function () {
                    updateSummaryStats();
                }
            });

            // ── Summary stats (computed from datatable data) ───────────────────
            function updateSummaryStats() {
                $.getJSON($('#assignmentsRoute').val(), { draw: 1, start: 0, length: 10000, search: { value: '' } }, function (res) {
                    var rows       = res.data || [];
                    var total      = rows.length;
                    var active     = rows.filter(function (r) { return r.status_badge && r.status_badge.indexOf('bg-success') !== -1; }).length;
                    var userCounts = {};
                    rows.forEach(function (r) {
                        if (r.status_badge && r.status_badge.indexOf('bg-success') !== -1) {
                            userCounts[r.user_email] = (userCounts[r.user_email] || 0) + 1;
                        }
                    });
                    var multiRole = Object.values(userCounts).filter(function (c) { return c > 1; }).length;
                    $('#statTotalAssignments').text(total);
                    $('#statActiveAssignments').text(active);
                    $('#statMultiRole').text(multiRole);
                });
            }

            // ── Load users into Assign Role modal ──────────────────────────────
            $('#assignRoleModal').on('show.bs.modal', function () {
                if ($('#assign_user_id option').length <= 1) {
                    $.getJSON($('#usersForAssignRoute').val(), function (res) {
                        var opts = '<option value="">{{ __('Select User') }}</option>';
                        var users = (res.data && res.data.users) ? res.data.users : [];
                        users.forEach(function (u) {
                            opts += '<option value="' + u.id + '">' + u.name + ' (' + u.email + ')</option>';
                        });
                        $('#assign_user_id').html(opts);
                    });
                }
            });

            // ── Revoke role ────────────────────────────────────────────────────
            $(document).on('click', '.revoke-role-btn', function () {
                var userId = $(this).data('user-id');
                var slug   = $(this).data('slug');
                if (!confirm('{{ __('Revoke this role from the user?') }}')) return;
                $.post(
                    $('#revokeRoleRoute').val(),
                    { _token: $('meta[name="csrf-token"]').attr('content'), user_id: userId, role_slug: slug },
                    function (res) {
                        if (res && res.status === true) {
                            toastr.success(res.message);
                            assignmentsTable.ajax.reload();
                        } else {
                            commonHandler(res);
                        }
                    }
                );
            });

            // ── Assign role callback (called by ajax form handler) ─────────────
            window.assignRoleShowMessage = function (response) {
                if (response && response.status === true) {
                    toastr.success(response.message || '{{ __('Role assigned.') }}');
                    $('#assignRoleModal').modal('hide');
                    $('#assignRoleModal form')[0].reset();
                    // Reset user dropdown so it reloads fresh next open
                    $('#assign_user_id').html('<option value="">{{ __('Loading users…') }}</option>');
                    assignmentsTable.ajax.reload();
                } else {
                    if (typeof commonHandler === 'function') {
                        commonHandler(response);
                    } else {
                        toastr.error(response.message || '{{ __('An error occurred.') }}');
                    }
                }
            };

        });
    </script>
@endpush
