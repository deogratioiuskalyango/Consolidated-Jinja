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
                        <button type="button" class="theme-btn" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="ri-add-line me-1"></i> {{ __('Add Shareholder') }}
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="billing-center-area bg-off-white theme-border radius-4 p-25">
                        <table id="shareholderDataTable" class="table theme-border p-20" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width:40px">{{ __('SL') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Share Class') }}</th>
                                    <th>{{ __('Shares Held') }}</th>
                                    <th>{{ __('Ownership %') }}</th>
                                    <th>{{ __('Roles') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                <input type="hidden" id="shareholderDataRoute"       value="{{ route('admin.shareholders.data') }}">
                <input type="hidden" id="shareholderSuspendRoute"    value="{{ route('admin.shareholders.suspend',    '__ID__') }}">
                <input type="hidden" id="shareholderReactivateRoute" value="{{ route('admin.shareholders.reactivate', '__ID__') }}">
                <input type="hidden" id="shareholderEditRoute"       value="{{ route('admin.shareholders.edit',       '__ID__') }}">
                <input type="hidden" id="shareholderUpdateRoute"     value="{{ route('admin.shareholders.update',     '__ID__') }}">
                <input type="hidden" id="shareholderDeleteRoute"        value="{{ route('admin.shareholders.destroy',            '__ID__') }}">
                <input type="hidden" id="shareholderResendRoute"       value="{{ route('admin.shareholders.resend-credentials', '__ID__') }}">
                <input type="hidden" id="shareholderRolesRoute"        value="{{ route('admin.shareholders.roles', '__ID__') }}">
                <input type="hidden" id="shareholderRolesUpdateRoute"  value="{{ route('admin.shareholders.roles.update', '__ID__') }}">
            </div>
        </div>
    </div>
</div>

{{-- Add Shareholder Modal --}}
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addModalLabel">{{ __('Add Shareholder') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span class="iconify" data-icon="akar-icons:cross"></span>
                </button>
            </div>
            <form action="{{ route('admin.shareholders.store') }}" method="POST" class="ajax" data-handler="getShowMessage">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('First Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required placeholder="{{ __('First Name') }}">
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" required placeholder="{{ __('Last Name') }}">
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Email') }} <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required placeholder="{{ __('Email Address') }}">
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Phone') }}</label>
                            <input type="text" name="phone" class="form-control" placeholder="{{ __('Phone Number') }}">
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Share Class') }} <span class="text-danger">*</span></label>
                            <select name="share_class_id" class="form-control" required>
                                <option value="">{{ __('Select Share Class') }}</option>
                                @foreach($shareClasses as $shareClass)
                                    <option value="{{ $shareClass->id }}">{{ $shareClass->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Shares Held') }} <span class="text-danger">*</span></label>
                            <input type="number" name="shares_held" class="form-control" required min="0" step="1" placeholder="0">
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Voting Weight Per Share') }}</label>
                            <input type="number" name="fixed_voting_weight" class="form-control" min="0" step="0.0001" value="1" placeholder="1">
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Ownership %') }}</label>
                            <div class="form-control bg-light">
                                <small class="text-muted">{{ __('Auto-calculated from total shares issued') }}</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info py-2 mb-0">
                                <i class="ri-mail-send-line me-1"></i>
                                {{ __('Login credentials will be emailed to the shareholder automatically.') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-start">
                    <a href="javascript:void(0)" class="theme-btn-back me-3" data-bs-dismiss="modal">{{ __('Cancel') }}</a>
                    <button type="submit" class="theme-btn">{{ __('Add Shareholder') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Edit Shareholder Modal --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">{{ __('Edit Shareholder') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span class="iconify" data-icon="akar-icons:cross"></span>
                </button>
            </div>
            <form id="editShareholderForm" method="POST" class="ajax" data-handler="getEditMessage">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('First Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" id="edit_first_name" class="form-control" required placeholder="{{ __('First Name') }}">
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" id="edit_last_name" class="form-control" required placeholder="{{ __('Last Name') }}">
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Email') }} <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email" class="form-control" required placeholder="{{ __('Email Address') }}">
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Phone') }}</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control" placeholder="{{ __('Phone Number') }}">
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Share Class') }}</label>
                            <select name="share_class_id" id="edit_share_class_id" class="form-control">
                                <option value="">{{ __('Select Share Class') }}</option>
                                @foreach($shareClasses as $shareClass)
                                    <option value="{{ $shareClass->id }}">{{ $shareClass->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Shares Held') }} <span class="text-danger">*</span></label>
                            <input type="number" name="total_shares" id="edit_total_shares" class="form-control" required min="0" step="1">
                        </div>
                        <div class="col-md-6 mb-25">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Voting Weight Per Share') }}</label>
                            <input type="number" name="fixed_voting_weight" id="edit_fixed_voting_weight" class="form-control" min="0" step="0.0001">
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-start">
                    <a href="javascript:void(0)" class="theme-btn-back me-3" data-bs-dismiss="modal">{{ __('Cancel') }}</a>
                    <button type="submit" class="theme-btn">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Manage Shareholder Roles Modal --}}
<div class="modal fade" id="rolesModal" tabindex="-1" aria-labelledby="rolesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable shareholder-roles-dialog">
        <div class="modal-content shareholder-roles-modal">
            <form id="rolesShareholderForm" method="POST" class="shareholder-roles-form">
                @csrf
                <div class="modal-header shareholder-roles-header">
                    <div class="shareholder-roles-heading">
                        <span class="shareholder-roles-kicker">{{ __('Access Control') }}</span>
                        <h4 class="modal-title" id="rolesModalLabel">{{ __('Manage Shareholder Roles') }}</h4>
                        <p class="mb-0" id="rolesShareholderSubtitle">{{ __('Assign additional system roles to this shareholder.') }}</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span class="iconify" data-icon="akar-icons:cross"></span>
                    </button>
                </div>

                <div class="modal-body shareholder-roles-body">
                    <div class="shareholder-role-summary" role="status">
                        <span class="shareholder-role-summary-icon">
                            <i class="ri-shield-user-line"></i>
                        </span>
                        <span class="shareholder-role-summary-copy">
                            <strong>{{ __('Primary role: Shareholder') }}</strong>
                            <span>{{ __('The shareholder role stays active. Select extra roles this person can switch into after login.') }}</span>
                        </span>
                    </div>

                    <div class="shareholder-roles-section-title">
                        <div>
                            <h5>{{ __('Additional Roles') }}</h5>
                            <p>{{ __('Choose one or more roles. Unchecked roles will be removed from this shareholder.') }}</p>
                        </div>
                    </div>

                    <div class="row g-3 shareholder-role-grid">
                        @foreach($availableRoles as $slug => $role)
                            <div class="col-12 col-md-6 col-xl-4">
                                <input type="checkbox" class="btn-check shareholder-role-input" name="roles[]" id="role_{{ $slug }}" value="{{ $slug }}" autocomplete="off">
                                <label class="shareholder-role-card" for="role_{{ $slug }}">
                                    <span class="shareholder-role-icon bg-{{ $role['color'] ?? 'secondary' }}">
                                        <i class="{{ $role['icon'] ?? 'ri-user-line' }}"></i>
                                    </span>
                                    <span class="shareholder-role-copy">
                                        <span class="shareholder-role-title">{{ $role['label'] }}</span>
                                        <span class="shareholder-role-slug">{{ str_replace('_', ' ', $slug) }}</span>
                                    </span>
                                    <span class="shareholder-role-check" aria-hidden="true"><i class="ri-check-line"></i></span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="shareholder-role-notes">
                        <label class="label-text-title color-heading font-medium mb-2" for="roles_notes">{{ __('Notes') }}</label>
                        <textarea class="form-control" id="roles_notes" name="notes" rows="3" placeholder="{{ __('Optional reason or instructions for this role update') }}"></textarea>
                    </div>
                </div>

                <div class="modal-footer shareholder-roles-footer">
                    <a href="javascript:void(0)" class="theme-btn-back me-3" data-bs-dismiss="modal">{{ __('Cancel') }}</a>
                    <button type="submit" class="theme-btn">
                        <i class="ri-save-line me-1"></i>{{ __('Save Roles') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('style')
    @include('common.layouts.datatable-style')
    <style>
        .shareholder-role-badges .badge { line-height: 1.35; }
        .shareholder-roles-dialog { margin: .75rem auto; max-width: min(1120px, calc(100vw - 24px)); }
        .shareholder-roles-modal { background: var(--t-bg-surface, var(--bg-white)); border: 1px solid var(--t-border, var(--border-color)); color: var(--t-text-primary, var(--heading-color)); max-height: calc(100vh - 24px); overflow: hidden; }
        .shareholder-roles-form { display: flex; flex-direction: column; max-height: calc(100vh - 24px); min-height: 0; }
        .shareholder-roles-header, .shareholder-roles-footer { background: var(--t-bg-surface, var(--bg-white)); border-color: var(--t-border, var(--border-color)); flex: 0 0 auto; position: sticky; z-index: 2; }
        .shareholder-roles-header { align-items: flex-start; gap: 14px; padding: 20px 24px 16px; top: 0; }
        .shareholder-roles-footer { bottom: 0; justify-content: flex-start; padding: 16px 24px; }
        .shareholder-roles-heading { min-width: 0; }
        .shareholder-roles-kicker { color: var(--t-accent, var(--primary-color)); display: block; font-size: 12px; font-weight: 700; line-height: 1.2; margin-bottom: 4px; text-transform: uppercase; }
        .shareholder-roles-heading h4 { color: var(--t-text-primary, var(--heading-color)); line-height: 1.25; margin: 0; overflow-wrap: anywhere; }
        .shareholder-roles-heading p { color: var(--t-text-secondary, var(--body-font-color)); line-height: 1.45; margin-top: 6px; overflow-wrap: anywhere; }
        .shareholder-roles-body { background: var(--t-bg-card, var(--off-white)); flex: 1 1 auto; min-height: 0; overflow-y: auto; padding: 22px 24px; scrollbar-gutter: stable; }
        .shareholder-role-summary { align-items: flex-start; background: var(--t-bg-surface, var(--bg-white)); border: 1px solid var(--t-border, var(--border-color)); border-radius: var(--t-radius-sm, 8px); color: var(--t-text-secondary, var(--body-font-color)); display: flex; gap: 12px; margin-bottom: 20px; padding: 14px 16px; }
        .shareholder-role-summary-icon { align-items: center; background: var(--t-bg-active, rgba(59,130,246,.08)); border-radius: var(--t-radius-sm, 8px); color: var(--t-accent, var(--primary-color)); display: inline-flex; flex: 0 0 38px; height: 38px; justify-content: center; width: 38px; }
        .shareholder-role-summary-copy { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
        .shareholder-role-summary-copy strong { color: var(--t-text-primary, var(--heading-color)); }
        .shareholder-role-summary-copy span { overflow-wrap: anywhere; }
        .shareholder-roles-section-title { align-items: flex-end; display: flex; justify-content: space-between; margin-bottom: 12px; }
        .shareholder-roles-section-title h5 { color: var(--t-text-primary, var(--heading-color)); font-size: 16px; line-height: 1.3; margin: 0; }
        .shareholder-roles-section-title p { color: var(--t-text-secondary, var(--body-font-color)); font-size: 13px; line-height: 1.45; margin: 4px 0 0; }
        .shareholder-role-card { align-items: center; background: var(--t-bg-surface, var(--bg-white)); border: 1px solid var(--t-border, var(--border-color)); border-radius: var(--t-radius-sm, 8px); color: var(--t-text-primary, var(--heading-color)); cursor: pointer; display: flex; gap: 12px; min-height: 86px; padding: 14px; position: relative; transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease, background-color .15s ease; width: 100%; }
        .shareholder-role-card:hover { background: var(--t-bg-hover, var(--off-white)); border-color: var(--t-border-hover, var(--border-color-2)); box-shadow: var(--t-shadow-sm, 0 1px 3px rgba(0,0,0,.08)); transform: translateY(-1px); }
        .shareholder-role-input:focus + .shareholder-role-card { border-color: var(--t-border-focus, var(--primary-color)); box-shadow: 0 0 0 3px var(--t-bg-active, rgba(59,130,246,.12)); }
        .shareholder-role-icon { align-items: center; border-radius: var(--t-radius-sm, 8px); color: #fff; display: inline-flex; flex: 0 0 40px; height: 40px; justify-content: center; width: 40px; }
        .shareholder-role-copy { display: flex; flex: 1 1 auto; flex-direction: column; min-width: 0; }
        .shareholder-role-title { color: var(--t-text-primary, var(--heading-color)); font-weight: 600; line-height: 1.25; overflow-wrap: anywhere; }
        .shareholder-role-slug { color: var(--t-text-secondary, var(--body-font-color)); font-size: 12px; line-height: 1.35; margin-top: 3px; overflow-wrap: anywhere; text-transform: capitalize; }
        .shareholder-role-check { align-items: center; background: var(--t-accent-green, var(--green-color)); border-radius: 50%; color: #fff; display: none; flex: 0 0 24px; height: 24px; justify-content: center; margin-left: auto; width: 24px; }
        .shareholder-role-input:checked + .shareholder-role-card { background: var(--t-bg-active, rgba(59,130,246,.08)); border-color: var(--t-accent, var(--primary-color)); box-shadow: inset 0 0 0 1px var(--t-accent, var(--primary-color)); }
        .shareholder-role-input:checked + .shareholder-role-card .shareholder-role-check { display: inline-flex; }
        .shareholder-role-notes { margin-top: 22px; }
        .shareholder-role-notes textarea { background: var(--t-bg-input, var(--bg-white)); color: var(--t-text-primary, var(--heading-color)); min-height: 92px; resize: vertical; }
        @media (max-width: 767.98px) { .shareholder-roles-dialog { align-items: stretch; margin: 0; max-width: 100%; min-height: 100%; } .shareholder-roles-modal, .shareholder-roles-form { border-radius: 0; max-height: 100vh; min-height: 100vh; } .shareholder-roles-header { padding: 16px; } .shareholder-roles-body { padding: 16px; } .shareholder-roles-footer { padding: 14px 16px; } .shareholder-roles-footer .theme-btn, .shareholder-roles-footer .theme-btn-back { flex: 1 1 0; justify-content: center; min-width: 0; text-align: center; } .shareholder-role-card { min-height: 76px; padding: 12px; } }
    </style>
@endpush

@push('script')
    @include('common.layouts.datatable-script')
    <script src="{{ asset('assets/js/custom/shareholder-admin.js') }}?v={{ filemtime(public_path('assets/js/custom/shareholder-admin.js')) }}"></script>
@endpush
