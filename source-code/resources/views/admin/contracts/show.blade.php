@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            {{-- Page Header --}}
            <div class="page-content-wrapper bg-white p-30 radius-20 mb-4">
                <div class="d-sm-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.contracts.index') }}" class="btn btn-sm btn-light">
                            <i class="ri-arrow-left-line"></i>
                        </a>
                        <div>
                            <h3 class="mb-0">{{ __('Contract Details') }}</h3>
                            <small class="text-muted">{{ $contract->reference_no }}</small>
                        </div>
                        @php
                            $statusMap = [
                                'draft'             => 'secondary',
                                'sent'              => 'info',
                                'reviewing'         => 'warning',
                                'disputed'          => 'danger',
                                'pending_signature' => 'primary',
                                'signed'            => 'success',
                                'active'            => 'success',
                                'terminated'        => 'dark',
                            ];
                            $badgeColor = $statusMap[$contract->status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $badgeColor }} ms-1">
                            {{ ucwords(str_replace('_', ' ', $contract->status)) }}
                        </span>
                    </div>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.contracts.index') }}">{{ __('Contracts') }}</a></li>
                        <li class="breadcrumb-item active">{{ $contract->reference_no }}</li>
                    </ol>
                </div>

                {{-- Quick Stats --}}
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-2">
                                <i class="ri-money-dollar-circle-line text-primary"></i>
                            </div>
                            <div>
                                <div class="text-muted small">{{ __('Monthly Rent') }}</div>
                                <div class="fw-medium">{{ $contract->currency ?? 'UGX' }} {{ number_format($contract->monthly_rent, 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-success bg-opacity-10 p-2">
                                <i class="ri-shield-check-line text-success"></i>
                            </div>
                            <div>
                                <div class="text-muted small">{{ __('Security Deposit') }}</div>
                                <div class="fw-medium">{{ $contract->currency ?? 'UGX' }} {{ number_format($contract->security_deposit, 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-info bg-opacity-10 p-2">
                                <i class="ri-calendar-line text-info"></i>
                            </div>
                            <div>
                                <div class="text-muted small">{{ __('From') }}</div>
                                <div class="fw-medium">{{ $contract->commencement_date ? $contract->commencement_date->format('d M Y') : '—' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-2">
                                <i class="ri-calendar-close-line text-warning"></i>
                            </div>
                            <div>
                                <div class="text-muted small">{{ __('Until') }}</div>
                                <div class="fw-medium">{{ $contract->expiry_date ? $contract->expiry_date->format('d M Y') : '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-4">

                {{-- LEFT COLUMN --}}
                <div class="col-lg-8">

                    {{-- Section A — Tenant Details --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold"><i class="ri-user-line me-2"></i>{{ __('Section A — Tenant Details') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="text-muted small">{{ __('Name') }}</div>
                                    <div class="fw-medium">{{ $contract->tenant_name ?? '—' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small">{{ __('Email') }}</div>
                                    <div class="fw-medium">{{ $contract->tenant_email ?? '—' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small">{{ __('Phone') }}</div>
                                    <div class="fw-medium">{{ $contract->tenant_phone ?? '—' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small">{{ __('Nationality') }}</div>
                                    <div class="fw-medium">{{ $contract->tenant_nationality ?? '—' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small">{{ __('ID Type') }}</div>
                                    <div class="fw-medium">{{ $contract->tenant_id_type ?? '—' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small">{{ __('ID Number') }}</div>
                                    <div class="fw-medium">{{ $contract->tenant_id_number ?? '—' }}</div>
                                </div>
                                @if($contract->next_of_kin_name)
                                <div class="col-md-4">
                                    <div class="text-muted small">{{ __('Next of Kin') }}</div>
                                    <div class="fw-medium">{{ $contract->next_of_kin_name }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small">{{ __('Next of Kin Phone') }}</div>
                                    <div class="fw-medium">{{ $contract->next_of_kin_phone ?? '—' }}</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Section B — Property Details --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold"><i class="ri-building-line me-2"></i>{{ __('Section B — Property Details') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="text-muted small">{{ __('Property Name') }}</div>
                                    <div class="fw-medium">{{ $contract->property_name ?? $contract->property?->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small">{{ __('Unit') }}</div>
                                    <div class="fw-medium">{{ $contract->unit_name ?? $contract->unit?->unit_name ?? '—' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small">{{ __('Address') }}</div>
                                    <div class="fw-medium">{{ $contract->property_address ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section C — Payment --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold"><i class="ri-money-dollar-circle-line me-2"></i>{{ __('Section C — Payment Terms') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="text-muted small">{{ __('Monthly Rent') }}</div>
                                    <div class="fw-medium">{{ $contract->currency ?? 'UGX' }} {{ number_format($contract->monthly_rent, 0) }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted small">{{ __('Security Deposit') }}</div>
                                    <div class="fw-medium">{{ $contract->currency ?? 'UGX' }} {{ number_format($contract->security_deposit, 0) }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted small">{{ __('Payment Due Day') }}</div>
                                    <div class="fw-medium">{{ __('Day') }} {{ $contract->payment_due_day ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted small">{{ __('Payment Method') }}</div>
                                    <div class="fw-medium">{{ ucwords(str_replace('_', ' ', $contract->payment_method ?? '—')) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section D — Bank Details --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold"><i class="ri-bank-line me-2"></i>{{ __('Section D — Bank Details') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="text-muted small">{{ __('Bank Name') }}</div>
                                    <div class="fw-medium">{{ $contract->bank_name ?? '—' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small">{{ __('Account Name') }}</div>
                                    <div class="fw-medium">{{ $contract->bank_account_name ?? '—' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small">{{ __('Account Number') }}</div>
                                    <div class="fw-medium">{{ $contract->bank_account_number ?? '—' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small">{{ __('Branch') }}</div>
                                    <div class="fw-medium">{{ $contract->bank_branch ?? '—' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small">{{ __('Mobile Money') }}</div>
                                    <div class="fw-medium">{{ $contract->mobile_money_number ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section E — Period --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold"><i class="ri-calendar-line me-2"></i>{{ __('Section E — Tenancy Period') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="text-muted small">{{ __('Commencement Date') }}</div>
                                    <div class="fw-medium">{{ $contract->commencement_date ? $contract->commencement_date->format('d M Y') : '—' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small">{{ __('Expiry Date') }}</div>
                                    <div class="fw-medium">{{ $contract->expiry_date ? $contract->expiry_date->format('d M Y') : '—' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted small">{{ __('Notice Period') }}</div>
                                    <div class="fw-medium">{{ $contract->notice_period_days ?? 30 }} {{ __('days') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Special Conditions --}}
                    @if($contract->special_conditions)
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold"><i class="ri-sticky-note-line me-2"></i>{{ __('Special Conditions') }}</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0" style="white-space:pre-wrap">{{ $contract->special_conditions }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Clause Reviews --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold"><i class="ri-list-check me-2"></i>{{ __('Clause Reviews') }}</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Clause') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Tenant Comment') }}</th>
                                            <th>{{ __('Reviewed At') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($contract->clauses as $clause)
                                            <tr>
                                                <td class="fw-medium">
                                                    {{ \App\Models\ContractClauseReview::CLAUSES[$clause->clause_key] ?? $clause->clause_key }}
                                                </td>
                                                <td>
                                                    @if($clause->status === 'agreed')
                                                        <span class="badge bg-success">{{ __('Agreed') }}</span>
                                                    @elseif($clause->status === 'disputed')
                                                        <span class="badge bg-danger">{{ __('Disputed') }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ __('Pending') }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-muted small">{{ $clause->tenant_comment ?? '—' }}</td>
                                                <td class="text-muted small">
                                                    {{ $clause->reviewed_at ? $clause->reviewed_at->format('d M Y H:i') : '—' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">{{ __('No clause reviews.') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- RIGHT COLUMN --}}
                <div class="col-lg-4">

                    {{-- Actions --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold"><i class="ri-settings-line me-2"></i>{{ __('Actions') }}</h6>
                        </div>
                        <div class="card-body d-flex flex-column gap-2">
                            {{-- Send to Tenant --}}
                            @if($contract->status === 'draft')
                                <form action="{{ route('admin.contracts.send', $contract->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100"
                                            onclick="return confirm('{{ __('Send this contract to the tenant?') }}')">
                                        <i class="ri-send-plane-line me-1"></i>{{ __('Send to Tenant') }}
                                    </button>
                                </form>
                            @endif

                            {{-- Delete --}}
                            <form action="{{ route('admin.contracts.destroy', $contract->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100"
                                        onclick="return confirm('{{ __('Delete this contract permanently?') }}')">
                                    <i class="ri-delete-bin-line me-1"></i>{{ __('Delete Contract') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Status Timeline --}}
                    @if(!in_array($contract->status, ['draft']))
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold"><i class="ri-time-line me-2"></i>{{ __('Contract Status') }}</h6>
                        </div>
                        <div class="card-body">
                            @php
                                $allStatuses = ['draft','sent','reviewing','pending_signature','signed','active'];
                                $currentIdx  = array_search($contract->status, $allStatuses);
                            @endphp
                            <ul class="list-unstyled mb-0">
                                @foreach($allStatuses as $idx => $st)
                                    @php
                                        $done    = $idx <= $currentIdx;
                                        $current = $idx === $currentIdx;
                                    @endphp
                                    <li class="d-flex align-items-center gap-2 mb-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                                             style="width:28px;height:28px;min-width:28px;
                                                    background:{{ $done ? '#0d6efd' : '#e9ecef' }};
                                                    color:{{ $done ? '#fff' : '#adb5bd' }}">
                                            @if($done)
                                                <i class="ri-check-line" style="font-size:14px"></i>
                                            @else
                                                <i class="ri-circle-line" style="font-size:14px"></i>
                                            @endif
                                        </div>
                                        <span class="{{ $current ? 'fw-semibold' : ($done ? 'text-muted' : 'text-muted') }}">
                                            {{ ucwords(str_replace('_', ' ', $st)) }}
                                            @if($current)
                                                <span class="badge bg-primary ms-1 small">{{ __('Current') }}</span>
                                            @endif
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif

                    {{-- LC1 & Witnesses --}}
                    @if($contract->lc1_name || $contract->witness1_name || $contract->witness2_name)
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold"><i class="ri-group-line me-2"></i>{{ __('LC1 & Witnesses') }}</h6>
                        </div>
                        <div class="card-body">
                            @if($contract->lc1_name)
                            <div class="mb-2">
                                <div class="text-muted small">{{ __('LC1 Chairperson') }}</div>
                                <div class="fw-medium">{{ $contract->lc1_name }}</div>
                                @if($contract->lc1_phone)<small class="text-muted">{{ $contract->lc1_phone }}</small>@endif
                            </div>
                            @endif
                            @if($contract->witness1_name)
                            <div class="mb-2">
                                <div class="text-muted small">{{ __('Witness 1') }}</div>
                                <div class="fw-medium">{{ $contract->witness1_name }}</div>
                            </div>
                            @endif
                            @if($contract->witness2_name)
                            <div class="mb-2">
                                <div class="text-muted small">{{ __('Witness 2') }}</div>
                                <div class="fw-medium">{{ $contract->witness2_name }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Signatures --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold"><i class="ri-pen-nib-line me-2"></i>{{ __('Signatures') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-0">

                                {{-- ── OWNER'S SIDE (admin signs here) ── --}}
                                <div class="col-md-6 pe-md-4 border-end">
                                    <p class="text-uppercase fw-semibold text-muted small mb-3 border-bottom pb-2">
                                        {{ __("Owner's Side") }}
                                    </p>

                                    {{-- LC1 Chairperson --}}
                                    <div class="mb-4">
                                        <div class="text-muted small mb-1 fw-semibold">
                                            {{ __('LC1 Chairperson') }}
                                            @if($contract->lc1_name) — <span class="fw-medium text-dark">{{ $contract->lc1_name }}</span>@endif
                                        </div>
                                        @if($contract->lc1_signature)
                                            <img src="{{ $contract->lc1_signature }}" alt="{{ __('LC1 Signature') }}"
                                                 style="max-height:80px;border:1px solid #dee2e6;border-radius:4px;padding:4px;background:#fff;display:block">
                                            @if($contract->lc1_stamp)
                                                <img src="{{ Storage::url($contract->lc1_stamp) }}" alt="{{ __('LC1 Stamp') }}"
                                                     style="max-height:50px;border:1px solid #dee2e6;border-radius:4px;padding:2px;background:#fff;display:block;margin-top:4px">
                                            @endif
                                            <div class="text-muted small mt-1">{{ $contract->lc1_signed_at?->format('d M Y H:i') }}</div>
                                        @else
                                            @if(!$contract->lc1_name)
                                                <div class="row g-2 mb-2">
                                                    <div class="col-6">
                                                        <input type="text" id="lc1NameInput" class="form-control form-control-sm" placeholder="{{ __('LC1 Name') }}">
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="text" id="lc1PhoneInput" class="form-control form-control-sm" placeholder="{{ __('LC1 Phone') }}">
                                                    </div>
                                                </div>
                                            @endif
                                            <canvas id="lc1SigCanvas" width="260" height="90"
                                                    style="border:1px solid #ced4da;border-radius:4px;cursor:crosshair;touch-action:none;background:#fff;display:block"></canvas>
                                            <div class="mb-2 mt-2">
                                                <label class="form-label small mb-1">{{ __('Official Stamp (optional)') }}</label>
                                                <input type="file" class="form-control form-control-sm" id="lc1StampFile" accept="image/jpg,image/jpeg,image/png">
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" id="clearLc1Sig">
                                                    <i class="ri-eraser-line me-1"></i>{{ __('Clear') }}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-primary" id="saveLc1Sig">
                                                    <i class="ri-save-line me-1"></i>{{ __('Save LC1 Signature') }}
                                                </button>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Landlord --}}
                                    <div class="mb-4">
                                        <div class="text-muted small mb-1 fw-semibold">{{ __('Landlord / Owner') }}</div>
                                        @if($contract->landlord_signature)
                                            <img src="{{ $contract->landlord_signature }}" alt="{{ __('Landlord Signature') }}"
                                                 style="max-height:80px;border:1px solid #dee2e6;border-radius:4px;padding:4px;background:#fff;display:block">
                                            <div class="text-muted small mt-1">{{ $contract->landlord_signed_at?->format('d M Y H:i') }}</div>
                                        @else
                                            <canvas id="landlordSigCanvas" width="260" height="90"
                                                    style="border:1px solid #ced4da;border-radius:4px;cursor:crosshair;touch-action:none;background:#fff;display:block"></canvas>
                                            <div class="d-flex gap-2 mt-2">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" id="clearLandlordSig">
                                                    <i class="ri-eraser-line me-1"></i>{{ __('Clear') }}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-success" id="saveLandlordSig">
                                                    <i class="ri-save-line me-1"></i>{{ __('Sign as Landlord') }}
                                                </button>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Witness 1 --}}
                                    <div>
                                        <div class="text-muted small mb-1 fw-semibold">
                                            {{ __('Witness 1') }}
                                            @if($contract->witness1_name) — <span class="fw-medium text-dark">{{ $contract->witness1_name }}</span>@endif
                                        </div>
                                        @if($contract->witness1_signature)
                                            <img src="{{ $contract->witness1_signature }}" alt="{{ __('Witness 1 Signature') }}"
                                                 style="max-height:80px;border:1px solid #dee2e6;border-radius:4px;padding:4px;background:#fff;display:block">
                                            <div class="text-muted small mt-1">{{ $contract->witness1_signed_at?->format('d M Y H:i') }}</div>
                                        @else
                                            @if(!$contract->witness1_name)
                                                <input type="text" id="witness1NameInput" class="form-control form-control-sm mb-2" placeholder="{{ __('Witness 1 Name') }}">
                                            @endif
                                            <canvas id="witness1SigCanvas" width="260" height="90"
                                                    style="border:1px solid #ced4da;border-radius:4px;cursor:crosshair;touch-action:none;background:#fff;display:block"></canvas>
                                            <div class="d-flex gap-2 mt-2">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" id="clearWitness1Sig">
                                                    <i class="ri-eraser-line me-1"></i>{{ __('Clear') }}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-primary" id="saveWitness1Sig">
                                                    <i class="ri-save-line me-1"></i>{{ __('Save Witness 1 Signature') }}
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- ── TENANT'S SIDE (read-only here, signed on tenant panel) ── --}}
                                <div class="col-md-6 ps-md-4 mt-4 mt-md-0">
                                    <p class="text-uppercase fw-semibold text-muted small mb-3 border-bottom pb-2">
                                        {{ __("Tenant's Side") }}
                                    </p>

                                    {{-- Tenant (read-only) --}}
                                    <div class="mb-4">
                                        <div class="text-muted small mb-1 fw-semibold">{{ __('Tenant') }}</div>
                                        @if($contract->tenant_signature)
                                            <img src="{{ $contract->tenant_signature }}" alt="{{ __('Tenant Signature') }}"
                                                 style="max-height:80px;border:1px solid #dee2e6;border-radius:4px;padding:4px;background:#fff;display:block">
                                            <div class="text-muted small mt-1">{{ $contract->tenant_signed_at?->format('d M Y H:i') }}</div>
                                        @else
                                            <span class="badge bg-secondary">{{ __('Awaiting tenant signature') }}</span>
                                            <div class="text-muted small mt-1">{{ __('Tenant signs from their portal') }}</div>
                                        @endif
                                    </div>

                                    {{-- Witness 2 (read-only) --}}
                                    <div>
                                        <div class="text-muted small mb-1 fw-semibold">
                                            {{ __('Witness 2') }}
                                            @if($contract->witness2_name) — <span class="fw-medium text-dark">{{ $contract->witness2_name }}</span>@endif
                                        </div>
                                        @if($contract->witness2_signature)
                                            <img src="{{ $contract->witness2_signature }}" alt="{{ __('Witness 2 Signature') }}"
                                                 style="max-height:80px;border:1px solid #dee2e6;border-radius:4px;padding:4px;background:#fff;display:block">
                                            <div class="text-muted small mt-1">{{ $contract->witness2_signed_at?->format('d M Y H:i') }}</div>
                                        @else
                                            <span class="badge bg-secondary">{{ __('Not signed yet') }}</span>
                                        @endif
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
@endsection

@push('style')
<style>
    #landlordSigCanvas, #lc1SigCanvas, #witness1SigCanvas {
        display: block;
    }
    #landlordSigCanvas:hover, #lc1SigCanvas:hover, #witness1SigCanvas:hover {
        border-color: #0d6efd !important;
    }
</style>
@endpush

@push('script')
<script>
// ── Reusable canvas signature pad factory ──
function makeSignaturePad(canvasId, clearBtnId) {
    var canvas = document.getElementById(canvasId);
    if (!canvas) return null;
    var ctx = canvas.getContext('2d');
    var drawing = false;
    ctx.strokeStyle = '#000';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';

    function pos(e) {
        var r = canvas.getBoundingClientRect();
        var src = e.touches ? e.touches[0] : e;
        return { x: src.clientX - r.left, y: src.clientY - r.top };
    }
    canvas.addEventListener('mousedown',  function(e){ drawing=true; ctx.beginPath(); var p=pos(e); ctx.moveTo(p.x,p.y); });
    canvas.addEventListener('mousemove',  function(e){ if(!drawing) return; var p=pos(e); ctx.lineTo(p.x,p.y); ctx.stroke(); });
    canvas.addEventListener('mouseup',    function(){ drawing=false; });
    canvas.addEventListener('mouseleave', function(){ drawing=false; });
    canvas.addEventListener('touchstart', function(e){ e.preventDefault(); drawing=true; ctx.beginPath(); var p=pos(e); ctx.moveTo(p.x,p.y); }, {passive:false});
    canvas.addEventListener('touchmove',  function(e){ e.preventDefault(); if(!drawing) return; var p=pos(e); ctx.lineTo(p.x,p.y); ctx.stroke(); }, {passive:false});
    canvas.addEventListener('touchend',   function(){ drawing=false; });

    document.getElementById(clearBtnId)?.addEventListener('click', function(){
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    });

    return canvas;
}

// ── JSON POST helper ──
function sigPost(url, body, btn, originalLabel) {
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>{{ __("Saving...") }}';
    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(body)
    })
    .then(function(r){ return r.json(); })
    .then(function(d){
        if (d.status === 200) {
            toastr && toastr.success(d.message || '{{ __("Saved successfully.") }}');
            setTimeout(function(){ location.reload(); }, 1000);
        } else {
            toastr && toastr.error(d.message || '{{ __("An error occurred.") }}');
            btn.disabled = false;
            btn.innerHTML = originalLabel;
        }
    })
    .catch(function(){
        toastr && toastr.error('{{ __("Request failed.") }}');
        btn.disabled = false;
        btn.innerHTML = originalLabel;
    });
}

// ── FormData POST helper (for file uploads) ──
function sigFormPost(url, formData, btn, originalLabel) {
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>{{ __("Saving...") }}';
    fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData
    })
    .then(function(r){ return r.json(); })
    .then(function(d){
        if (d.status === 200) {
            toastr && toastr.success(d.message || '{{ __("Saved successfully.") }}');
            setTimeout(function(){ location.reload(); }, 1000);
        } else {
            toastr && toastr.error(d.message || '{{ __("An error occurred.") }}');
            btn.disabled = false;
            btn.innerHTML = originalLabel;
        }
    })
    .catch(function(){
        toastr && toastr.error('{{ __("Request failed.") }}');
        btn.disabled = false;
        btn.innerHTML = originalLabel;
    });
}

// ── LC1 Chairperson ──
var lc1Canvas = makeSignaturePad('lc1SigCanvas', 'clearLc1Sig');
document.getElementById('saveLc1Sig')?.addEventListener('click', function(){
    if (!lc1Canvas) return;
    var fd = new FormData();
    fd.append('lc1_signature', lc1Canvas.toDataURL('image/png'));
    var nameInput  = document.getElementById('lc1NameInput');
    var phoneInput = document.getElementById('lc1PhoneInput');
    var stampFile  = document.getElementById('lc1StampFile');
    if (nameInput  && nameInput.value.trim())  fd.append('lc1_name',  nameInput.value.trim());
    if (phoneInput && phoneInput.value.trim()) fd.append('lc1_phone', phoneInput.value.trim());
    if (stampFile  && stampFile.files[0])      fd.append('lc1_stamp', stampFile.files[0]);
    sigFormPost('{{ route("admin.contracts.lc1-sign", $contract->id) }}', fd, this,
        '<i class="ri-save-line me-1"></i>{{ __("Save LC1 Signature") }}');
});

// ── Landlord ──
var landlordCanvas = makeSignaturePad('landlordSigCanvas', 'clearLandlordSig');
document.getElementById('saveLandlordSig')?.addEventListener('click', function(){
    if (!landlordCanvas) return;
    sigPost('{{ route("admin.contracts.admin-sign", $contract->id) }}',
        { landlord_signature: landlordCanvas.toDataURL('image/png') },
        this, '<i class="ri-save-line me-1"></i>{{ __("Sign as Landlord") }}');
});

// ── Witness 1 ──
var witness1Canvas = makeSignaturePad('witness1SigCanvas', 'clearWitness1Sig');
document.getElementById('saveWitness1Sig')?.addEventListener('click', function(){
    if (!witness1Canvas) return;
    var body = { witness1_signature: witness1Canvas.toDataURL('image/png') };
    var nameInput = document.getElementById('witness1NameInput');
    if (nameInput && nameInput.value.trim()) body.witness1_name = nameInput.value.trim();
    sigPost('{{ route("admin.contracts.witness1-sign", $contract->id) }}', body, this,
        '<i class="ri-save-line me-1"></i>{{ __("Save Witness 1 Signature") }}');
});
</script>
@endpush
