@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">

                {{-- Page Header --}}
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between border-bottom mb-20">
                            <div class="page-title-left d-flex align-items-center gap-2">
                                <a href="{{ route('admin.share-classes.index') }}" class="btn btn-sm btn-light">
                                    <i class="ri-arrow-left-line"></i>
                                </a>
                                <h3 class="mb-0">{{ $pageTitle }}</h3>
                                @php
                                    $code = strtoupper($shareClass->class_code ?? 'A');
                                    $badgeColors = ['A'=>'bg-purple','B'=>'bg-primary','C'=>'bg-success','D'=>'bg-secondary','E'=>'bg-warning text-dark','F'=>'bg-danger'];
                                    $badgeCls = $badgeColors[$code] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $badgeCls }} fs-6 px-3 py-1">{{ $code }}</span>
                            </div>
                            <div class="page-title-right">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('admin.share-classes.index') }}">{{ __('Share Classes') }}</a></li>
                                    <li class="breadcrumb-item active">{{ $shareClass->class_code }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">

                    {{-- LEFT PANEL: Details --}}
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-header bg-light d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-semibold"><i class="ri-file-list-3-line me-2"></i>{{ __('Class Details') }}</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.share-classes.update', $shareClass) }}" method="POST" class="ajax" data-handler="getShowMessage">
                                    @csrf
                                    <div class="row g-3">

                                        <div class="col-12">
                                            <label class="form-label fw-medium">{{ __('Class Name') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" value="{{ $shareClass->name }}" required>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-medium">{{ __('Class Code') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="class_code" class="form-control" value="{{ $shareClass->class_code }}" maxlength="5" required style="text-transform:uppercase">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-medium">{{ __('Description') }}</label>
                                            <textarea name="description" class="form-control" rows="3">{{ $shareClass->description }}</textarea>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-medium">{{ __('Par Value') }}</label>
                                            <input type="number" name="par_value" class="form-control" value="{{ $shareClass->par_value }}" step="0.0001" min="0">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-medium">{{ __('Voting Multiplier') }} <span class="text-danger">*</span></label>
                                            <input type="number" name="voting_multiplier" class="form-control" value="{{ $shareClass->voting_multiplier ?? 1 }}" step="0.01" min="0" required>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-medium">{{ __('Dividend Priority') }} <span class="text-danger">*</span></label>
                                            <input type="number" name="dividend_priority" class="form-control" value="{{ $shareClass->dividend_priority ?? 1 }}" min="1" max="100" required>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-medium">{{ __('Governance Level') }} <span class="text-danger">*</span></label>
                                            <select name="governance_level" class="form-select" required>
                                                <option value="1" {{ ($shareClass->governance_level ?? 1) == 1 ? 'selected' : '' }}>{{ __('1 — Standard') }}</option>
                                                <option value="2" {{ ($shareClass->governance_level ?? 1) == 2 ? 'selected' : '' }}>{{ __('2 — Elevated') }}</option>
                                                <option value="3" {{ ($shareClass->governance_level ?? 1) == 3 ? 'selected' : '' }}>{{ __('3 — Supreme') }}</option>
                                            </select>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-medium">{{ __('Financial Approval Limit') }}</label>
                                            <input type="number" name="financial_approval_limit" class="form-control" value="{{ $shareClass->financial_approval_limit }}" step="0.01" min="0" placeholder="{{ __('Unlimited') }}">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-medium">{{ __('Max Ownership Cap (%)') }}</label>
                                            <input type="number" name="max_ownership_cap" class="form-control" value="{{ $shareClass->max_ownership_cap }}" step="0.01" min="0" max="100" placeholder="{{ __('No cap') }}">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-medium">{{ __('Fixed Dividend Rate (%)') }}</label>
                                            <input type="number" name="fixed_dividend_rate" class="form-control" value="{{ $shareClass->fixed_dividend_rate }}" step="0.01" min="0" max="100" placeholder="{{ __('Variable') }}">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-medium mb-2">{{ __('Boolean Flags') }}</label>
                                            <div class="d-flex flex-column gap-2">
                                                @foreach([
                                                    'voting_rights'                       => 'Voting Rights',
                                                    'dividend_rights'                     => 'Dividend Rights',
                                                    'is_transferable'                     => 'Transferable',
                                                    'transfer_requires_board_approval'    => 'Transfer → Board Approval',
                                                    'transfer_requires_compliance_review' => 'Transfer → Compliance Review',
                                                    'can_be_diluted_without_approval'     => 'Can Dilute Without Approval',
                                                    'has_vesting'                         => 'Has Vesting',
                                                    'is_founder_class'                    => 'Founder Class',
                                                    'dividend_cumulative'                 => 'Dividend Cumulative',
                                                ] as $field => $label)
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="{{ $field }}" id="flag_{{ $field }}" value="1"
                                                        {{ $shareClass->{$field} ? 'checked' : '' }}>
                                                    <label class="form-check-label small" for="flag_{{ $field }}">{{ __($label) }}</label>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-medium">{{ __('Governance Notes') }}</label>
                                            <textarea name="governance_notes" class="form-control" rows="3">{{ $shareClass->governance_notes }}</textarea>
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="ri-save-line me-1"></i>{{ __('Save Changes') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT PANEL: Permissions + Shareholders --}}
                    <div class="col-md-8">

                        {{-- Permission Matrix --}}
                        <div class="card mb-4">
                            <div class="card-header bg-light d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-semibold"><i class="ri-shield-keyhole-line me-2"></i>{{ __('Permission Matrix') }}</h6>
                                <span class="badge bg-primary">
                                    {{ $shareClass->permissions->where('is_allowed', true)->count() }} / {{ count($allPermissions) }} {{ __('allowed') }}
                                </span>
                            </div>
                            <div class="card-body p-0">
                                <form action="{{ route('admin.share-classes.permissions', $shareClass) }}" method="POST" class="ajax" data-handler="getShowMessage">
                                    @csrf
                                    @php
                                        $permMap = $shareClass->permissions->keyBy('permission_key');
                                    @endphp
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="ps-3">{{ __('Permission') }}</th>
                                                    <th class="text-center" style="width:90px">{{ __('Allowed') }}</th>
                                                    <th style="width:160px">{{ __('Limit Value') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($allPermissions as $permKey)
                                                @php
                                                    $perm       = $permMap[$permKey] ?? null;
                                                    $isAllowed  = $perm ? (bool) $perm->is_allowed : false;
                                                    $limitVal   = $perm ? $perm->limit_value : null;
                                                    $humanLabel = ucwords(str_replace(['perm_', '_'], ['', ' '], strtolower($permKey)));
                                                @endphp
                                                <tr>
                                                    <td class="ps-3">
                                                        <span class="small fw-medium">{{ $humanLabel }}</span>
                                                        <br><code class="text-muted" style="font-size:0.7rem">{{ $permKey }}</code>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check form-switch d-flex justify-content-center mb-0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="permissions[{{ $permKey }}][is_allowed]"
                                                                value="1"
                                                                {{ $isAllowed ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01"
                                                            name="permissions[{{ $permKey }}][limit_value]"
                                                            class="form-control form-control-sm"
                                                            value="{{ $limitVal }}"
                                                            placeholder="{{ __('Unlimited') }}">
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="p-3 border-top">
                                        <button type="submit" class="btn btn-success">
                                            <i class="ri-shield-check-line me-1"></i>{{ __('Save Permissions') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Shareholders Table --}}
                        <div class="card">
                            <div class="card-header bg-light d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-semibold"><i class="ri-group-line me-2"></i>{{ __('Shareholders in this Class') }}</h6>
                                <span class="badge bg-info">{{ $shareClass->shareholders->count() }}</span>
                            </div>
                            <div class="card-body p-0">
                                @if($shareClass->shareholders->isEmpty())
                                    <div class="text-center text-muted py-4">
                                        <i class="ri-user-line fs-3 d-block mb-2"></i>
                                        {{ __('No shareholders assigned to this class.') }}
                                    </div>
                                @else
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-3">{{ __('Name') }}</th>
                                                <th>{{ __('Email') }}</th>
                                                <th class="text-end">{{ __('Shares') }}</th>
                                                <th class="text-end">{{ __('Ownership %') }}</th>
                                                <th class="text-center">{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalShares = $shareClass->shareholders->sum('shares_held') ?: 1;
                                            @endphp
                                            @foreach($shareClass->shareholders as $sh)
                                            <tr>
                                                <td class="ps-3">
                                                    <span class="fw-medium">{{ $sh->user?->name ?? $sh->full_name ?? '—' }}</span>
                                                </td>
                                                <td class="text-muted small">{{ $sh->user?->email ?? '—' }}</td>
                                                <td class="text-end">{{ number_format($sh->shares_held ?? 0) }}</td>
                                                <td class="text-end">
                                                    @php $pct = $totalShares > 0 ? round(($sh->shares_held / $totalShares) * 100, 2) : 0; @endphp
                                                    {{ $pct }}%
                                                </td>
                                                <td class="text-center">
                                                    @if(($sh->status ?? 1) == SHAREHOLDER_STATUS_ACTIVE)
                                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                                    @else
                                                        <span class="badge bg-danger">{{ __('Suspended') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .bg-purple { background-color: #6f42c1 !important; color: #fff; }
    .form-switch .form-check-input { width: 2.5em; height: 1.25em; }
</style>
@endpush
