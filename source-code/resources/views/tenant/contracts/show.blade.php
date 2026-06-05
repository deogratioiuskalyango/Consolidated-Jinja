@extends('tenant.layouts.app')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <div class="page-content-wrapper bg-white p-30 radius-20">
                    {{-- Page header --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between border-bottom mb-20">
                                <div class="page-title-left">
                                    <h3 class="mb-sm-0">{{ $pageTitle }}</h3>
                                </div>
                                <div class="page-title-right">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('tenant.dashboard') }}">{{ __('Dashboard') }}</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('tenant.contracts.index') }}">{{ __('My Contracts') }}</a>
                                        </li>
                                        <li class="breadcrumb-item active">{{ $contract->reference_no }}</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        $statusConfig = [
                            'draft'             => ['bg-secondary', 'Draft'],
                            'sent'              => ['bg-info text-dark', 'Sent'],
                            'reviewing'         => ['bg-warning text-dark', 'Reviewing'],
                            'disputed'          => ['bg-danger', 'Disputed'],
                            'pending_signature' => ['bg-primary', 'Pending Signature'],
                            'signed'            => ['bg-success', 'Signed'],
                            'active'            => ['bg-success', 'Active'],
                            'terminated'        => ['bg-dark', 'Terminated'],
                        ];
                        [$badgeClass, $statusLabel] = $statusConfig[$contract->status] ?? ['bg-secondary', ucfirst($contract->status)];

                        $reviewableStatuses = ['sent', 'reviewing', 'pending_signature', 'disputed'];

                        $clauseTexts = [
                            'term_of_tenancy'      => 'The tenancy shall commence on the commencement date and continue for the agreed period. Either party may terminate with the required notice period.',
                            'rent_payment'         => "The Tenant shall pay monthly rent in advance on or before the due day of each month. Payments to be made via the specified payment method to the Landlord's account.",
                            'security_deposit'     => 'The Tenant shall pay a security deposit before taking occupation. The deposit is refundable at the end of the tenancy less any deductions for damage, unpaid rent, or breach of agreement.',
                            'landlord_obligations' => 'The Landlord shall maintain the property in good habitable condition, ensure peaceful enjoyment, provide essential services as agreed, and effect necessary repairs within reasonable time.',
                            'tenant_obligations'   => 'The Tenant shall keep the property clean, report defects promptly, not sublet without written consent, use the property for residential purposes only, and comply with estate rules.',
                            'utilities'            => 'The Tenant shall be responsible for payment of electricity, water, and other utilities consumed during the tenancy period unless otherwise stated.',
                            'termination'          => 'Either party may terminate this agreement by giving the required notice in writing. The Landlord may terminate immediately for non-payment of rent or material breach.',
                            'lock_in_period'       => 'The Tenant agrees to a lock-in period from the commencement date. Early termination within this period shall attract a penalty equivalent to rent for the remaining lock-in months.',
                            'alterations'          => 'The Tenant shall not make any structural alterations or additions to the property without prior written consent from the Landlord.',
                            'dispute_resolution'   => 'Any dispute arising from this agreement shall first be resolved amicably. Failing that, the Local Council 1 Chairperson may mediate. If unresolved, the matter shall be referred to a court of competent jurisdiction in Uganda.',
                        ];
                    @endphp

                    {{-- ─── Card 1: Contract Summary ─── --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light d-flex align-items-center justify-content-between py-3">
                            <h5 class="mb-0">
                                <i class="ri-file-list-3-line me-2 text-primary"></i>{{ __('Contract Summary') }}
                            </h5>
                            <span class="badge {{ $badgeClass }} fs-6 px-3 py-2">{{ __($statusLabel) }}</span>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <th class="text-muted fw-normal" style="width:45%">{{ __('Reference') }}</th>
                                            <td><strong>{{ $contract->reference_no }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted fw-normal">{{ __('Property') }}</th>
                                            <td>{{ $contract->property_name }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted fw-normal">{{ __('Unit') }}</th>
                                            <td>{{ $contract->unit_name ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted fw-normal">{{ __('Monthly Rent') }}</th>
                                            <td><strong>{{ $contract->currency }} {{ number_format($contract->monthly_rent, 0) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted fw-normal">{{ __('Security Deposit') }}</th>
                                            <td>{{ $contract->currency }} {{ number_format($contract->security_deposit, 0) }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <th class="text-muted fw-normal" style="width:45%">{{ __('Payment Method') }}</th>
                                            <td>{{ $contract->payment_method ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted fw-normal">{{ __('Currency') }}</th>
                                            <td>{{ $contract->currency }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted fw-normal">{{ __('Start Date') }}</th>
                                            <td>{{ $contract->commencement_date ? $contract->commencement_date->format('d M Y') : '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted fw-normal">{{ __('End Date') }}</th>
                                            <td>{{ $contract->expiry_date ? $contract->expiry_date->format('d M Y') : '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted fw-normal">{{ __('Notice Period') }}</th>
                                            <td>{{ $contract->notice_period_days ? $contract->notice_period_days . ' ' . __('days') : '—' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ─── Card 2: Clause Review ─── --}}
                    @if(in_array($contract->status, $reviewableStatuses))
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-light py-3">
                                <h5 class="mb-0">
                                    <i class="ri-check-double-line me-2 text-primary"></i>{{ __('Review Contract Clauses') }}
                                </h5>
                                <small class="text-muted">{{ __('Please review each clause carefully. You may agree or dispute any clause.') }}</small>
                            </div>
                            <div class="card-body p-0">
                                @php
                                    $clauseLabels = \App\Models\ContractClauseReview::CLAUSES;
                                @endphp
                                @foreach($clauseLabels as $key => $label)
                                    @php
                                        $clause = $clauseMap->get($key);
                                        $clauseStatus = $clause ? $clause->status : 'pending';
                                        $clauseBadge = match($clauseStatus) {
                                            'agreed'   => 'bg-success',
                                            'disputed' => 'bg-danger',
                                            default    => 'bg-secondary',
                                        };
                                        $canReview = in_array($clauseStatus, ['pending', 'disputed']);
                                    @endphp
                                    <div class="clause-item border-bottom p-4" id="clause-block-{{ $key }}">
                                        <div class="d-flex align-items-start justify-content-between gap-3 mb-2">
                                            <h6 class="mb-0 fw-semibold">{{ __($label) }}</h6>
                                            <span class="badge {{ $clauseBadge }} clause-badge-{{ $key }} flex-shrink-0">
                                                {{ __(ucfirst($clauseStatus)) }}
                                            </span>
                                        </div>

                                        <p class="text-muted mb-3" style="font-size:0.9rem; line-height:1.6">
                                            {{ $clauseTexts[$key] ?? '' }}
                                        </p>

                                        @if($clause && $clause->tenant_comment)
                                            <div class="alert alert-warning py-2 mb-3 small">
                                                <strong>{{ __('Your comment:') }}</strong> {{ $clause->tenant_comment }}
                                            </div>
                                        @endif

                                        @if($canReview)
                                            <div class="clause-actions d-flex align-items-center gap-2 flex-wrap">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-success clause-agree-btn"
                                                    data-key="{{ $key }}"
                                                    onclick="setClauseAction('{{ $key }}', 'agreed')">
                                                    <i class="ri-check-line me-1"></i>{{ __('Agree') }}
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger clause-dispute-btn"
                                                    data-key="{{ $key }}"
                                                    onclick="setClauseAction('{{ $key }}', 'disputed')">
                                                    <i class="ri-close-line me-1"></i>{{ __('Dispute') }}
                                                </button>
                                            </div>

                                            <div class="clause-form mt-3" id="clause-form-{{ $key }}" style="display:none;">
                                                <input type="hidden" id="clause-status-{{ $key }}" value="">
                                                <div class="mb-2 dispute-comment-wrap" id="dispute-comment-{{ $key }}" style="display:none;">
                                                    <textarea class="form-control form-control-sm"
                                                        id="clause-comment-{{ $key }}"
                                                        rows="2"
                                                        placeholder="{{ __('Describe your dispute or concern...') }}"></textarea>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button type="button"
                                                        class="btn btn-sm btn-primary"
                                                        onclick="submitClauseReview('{{ $key }}', {{ $contract->id }})">
                                                        <i class="ri-save-line me-1"></i>{{ __('Submit Review') }}
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-secondary"
                                                        onclick="cancelClauseAction('{{ $key }}')">
                                                        {{ __('Cancel') }}
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted small">
                                                <i class="ri-check-line"></i>
                                                @if($clauseStatus === 'agreed')
                                                    {{ __('You have agreed to this clause.') }}
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ─── Card 3: Tenant Signature ─── --}}
                    @if($contract->status === 'pending_signature' && !$contract->tenant_signature)
                        <div class="card border-0 shadow-sm mb-4 border-start border-4 border-primary">
                            <div class="card-header bg-light py-3">
                                <h5 class="mb-0">
                                    <i class="ri-pen-nib-line me-2 text-primary"></i>{{ __('Your Signature') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info py-2 mb-3">
                                    <i class="ri-information-line me-1"></i>
                                    {{ __('All clauses reviewed. Please sign below to finalise the contract.') }}
                                </div>
                                <p class="text-muted small mb-2">{{ __('Draw your signature in the box below:') }}</p>
                                <div class="border rounded d-inline-block bg-white mb-3" style="cursor:crosshair;">
                                    <canvas id="tenantSigCanvas" width="400" height="150"
                                        style="display:block; border-radius:4px;"></canvas>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" id="clearTenantSig" class="btn btn-sm btn-outline-secondary">
                                        <i class="ri-eraser-line me-1"></i>{{ __('Clear') }}
                                    </button>
                                    <button type="button" id="saveTenantSig" class="btn btn-sm btn-success">
                                        <i class="ri-check-line me-1"></i>{{ __('Sign Contract') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ─── Card 4: LC1 Chairperson (read-only — signed on admin panel) ─── --}}
                    @if($contract->lc1_name)
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-light py-3">
                                <h5 class="mb-0">
                                    <i class="ri-government-line me-2 text-primary"></i>{{ __('LC1 Chairperson') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4 text-muted">{{ __('Name') }}</div>
                                    <div class="col-sm-8 fw-medium">{{ $contract->lc1_name }}</div>
                                </div>
                                @if($contract->lc1_phone)
                                    <div class="row mb-2">
                                        <div class="col-sm-4 text-muted">{{ __('Phone') }}</div>
                                        <div class="col-sm-8">{{ $contract->lc1_phone }}</div>
                                    </div>
                                @endif
                                @if($contract->lc1_signature)
                                    <div class="d-flex align-items-center gap-2 text-success mt-2">
                                        <i class="ri-checkbox-circle-line fs-5"></i>
                                        <strong>{{ __('Signed') }}</strong>
                                        @if($contract->lc1_signed_at)
                                            <span class="text-muted small">— {{ $contract->lc1_signed_at->format('d M Y') }}</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="alert alert-info py-2 mt-2 mb-0 small">
                                        <i class="ri-information-line me-1"></i>
                                        {{ __('Pending LC1 signature — this is completed by the admin/owner.') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- ─── Card 5: Signatures Summary ─── --}}
                    @if($contract->tenant_signature || $contract->landlord_signature || $contract->lc1_signature || $contract->witness1_signature || $contract->witness2_signature)
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-light py-3">
                                <h5 class="mb-0">
                                    <i class="ri-pen-nib-line me-2 text-primary"></i>{{ __('Signatures') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-0">

                                    {{-- Owner's Side --}}
                                    <div class="col-md-6 pe-md-3 border-end">
                                        <p class="text-uppercase fw-semibold text-muted small mb-3 border-bottom pb-1">{{ __("Owner's Side") }}</p>
                                        <div class="d-flex flex-wrap gap-3">
                                            @if($contract->lc1_signature)
                                                <div class="text-center">
                                                    <div class="border rounded p-2 bg-light">
                                                        <img src="{{ $contract->lc1_signature }}" alt="LC1 Signature"
                                                             style="max-height:55px; max-width:120px;">
                                                        @if($contract->lc1_stamp)
                                                            <div class="mt-1">
                                                                <img src="{{ Storage::url($contract->lc1_stamp) }}" alt="LC1 Stamp"
                                                                     style="max-height:35px; max-width:100px;">
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <small class="text-muted d-block mt-1">{{ __('LC1 Chairperson') }}{{ $contract->lc1_name ? ' — ' . $contract->lc1_name : '' }}</small>
                                                </div>
                                            @endif
                                            @if($contract->landlord_signature)
                                                <div class="text-center">
                                                    <div class="border rounded p-2 bg-light">
                                                        <img src="{{ $contract->landlord_signature }}" alt="Landlord Signature"
                                                             style="max-height:55px; max-width:120px;">
                                                    </div>
                                                    <small class="text-muted d-block mt-1">{{ __('Landlord / Owner') }}</small>
                                                </div>
                                            @endif
                                            @if($contract->witness1_signature)
                                                <div class="text-center">
                                                    <div class="border rounded p-2 bg-light">
                                                        <img src="{{ $contract->witness1_signature }}" alt="Witness 1 Signature"
                                                             style="max-height:55px; max-width:120px;">
                                                    </div>
                                                    <small class="text-muted d-block mt-1">{{ __('Witness 1') }}{{ $contract->witness1_name ? ' — ' . $contract->witness1_name : '' }}</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Tenant's Side --}}
                                    <div class="col-md-6 ps-md-3 mt-4 mt-md-0">
                                        <p class="text-uppercase fw-semibold text-muted small mb-3 border-bottom pb-1">{{ __("Tenant's Side") }}</p>
                                        <div class="d-flex flex-wrap gap-3">
                                            @if($contract->tenant_signature)
                                                <div class="text-center">
                                                    <div class="border rounded p-2 bg-light">
                                                        <img src="{{ $contract->tenant_signature }}" alt="Tenant Signature"
                                                             style="max-height:55px; max-width:120px;">
                                                    </div>
                                                    <small class="text-muted d-block mt-1">{{ __('Tenant') }}</small>
                                                </div>
                                            @endif
                                            @if($contract->witness2_signature)
                                                <div class="text-center">
                                                    <div class="border rounded p-2 bg-light">
                                                        <img src="{{ $contract->witness2_signature }}" alt="Witness 2 Signature"
                                                             style="max-height:55px; max-width:120px;">
                                                    </div>
                                                    <small class="text-muted d-block mt-1">{{ __('Witness 2') }}{{ $contract->witness2_name ? ' — ' . $contract->witness2_name : '' }}</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ─── Print Button ─── --}}
                    @if(in_array($contract->status, ['signed', 'active', 'pending_signature']))
                        <div class="d-flex justify-content-end mt-2">
                            <a href="{{ route('tenant.contracts.print', $contract->id) }}"
                               target="_blank"
                               class="btn btn-outline-secondary">
                                <i class="ri-printer-line me-1"></i>{{ __('Print / Save as PDF') }}
                            </a>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
// ─── Clause Review ───────────────────────────────────────────────────────────

function setClauseAction(key, action) {
    document.getElementById('clause-status-' + key).value = action;
    document.getElementById('clause-form-' + key).style.display = 'block';

    var disputeWrap = document.getElementById('dispute-comment-' + key);
    if (action === 'disputed') {
        disputeWrap.style.display = 'block';
    } else {
        disputeWrap.style.display = 'none';
    }
}

function cancelClauseAction(key) {
    document.getElementById('clause-form-' + key).style.display = 'none';
    document.getElementById('clause-status-' + key).value = '';
}

function submitClauseReview(key, contractId) {
    var status  = document.getElementById('clause-status-' + key).value;
    var comment = document.getElementById('clause-comment-' + key)
                    ? document.getElementById('clause-comment-' + key).value : '';

    if (!status) {
        toastr.warning('{{ __("Please select Agree or Dispute first.") }}');
        return;
    }

    $.ajax({
        url: '{{ route("tenant.contracts.review-clause", ":id") }}'.replace(':id', contractId),
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            clause_key: key,
            status: status,
            tenant_comment: comment
        },
        success: function(res) {
            if (res.status === 200) {
                toastr.success(res.message || '{{ __("Saved.") }}');

                // Update badge
                var badge = document.querySelector('.clause-badge-' + key);
                if (badge) {
                    badge.textContent = res.clause_status.charAt(0).toUpperCase() + res.clause_status.slice(1);
                    badge.className = 'badge flex-shrink-0 clause-badge-' + key + ' ' + (
                        res.clause_status === 'agreed'   ? 'bg-success' :
                        res.clause_status === 'disputed' ? 'bg-danger'  : 'bg-secondary'
                    );
                }

                // Hide form & action buttons if agreed
                if (res.clause_status === 'agreed') {
                    var block = document.getElementById('clause-block-' + key);
                    var actions = block ? block.querySelector('.clause-actions') : null;
                    var form    = document.getElementById('clause-form-' + key);
                    if (actions) actions.style.display = 'none';
                    if (form)    form.style.display    = 'none';
                }

                // Reload page if contract status changed to pending_signature
                if (res.contract_status === 'pending_signature' || res.contract_status === 'disputed') {
                    setTimeout(function() { location.reload(); }, 1200);
                }
            }
        },
        error: function(xhr) {
            var msg = xhr.responseJSON && xhr.responseJSON.message
                ? xhr.responseJSON.message
                : '{{ __("An error occurred.") }}';
            toastr.error(msg);
        }
    });
}

// ─── Canvas Signature Utility ────────────────────────────────────────────────

function makeSignaturePad(canvasId) {
    var canvas  = document.getElementById(canvasId);
    if (!canvas) return null;
    var ctx     = canvas.getContext('2d');
    var drawing = false;

    ctx.strokeStyle = '#1a1a2e';
    ctx.lineWidth   = 2;
    ctx.lineCap     = 'round';

    function getPos(e) {
        var rect = canvas.getBoundingClientRect();
        if (e.touches) {
            return { x: e.touches[0].clientX - rect.left, y: e.touches[0].clientY - rect.top };
        }
        return { x: e.clientX - rect.left, y: e.clientY - rect.top };
    }

    canvas.addEventListener('mousedown',  function(e) { drawing = true; ctx.beginPath(); var p = getPos(e); ctx.moveTo(p.x, p.y); });
    canvas.addEventListener('mousemove',  function(e) { if (!drawing) return; var p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
    canvas.addEventListener('mouseup',    function()  { drawing = false; });
    canvas.addEventListener('mouseleave', function()  { drawing = false; });
    canvas.addEventListener('touchstart', function(e) { e.preventDefault(); drawing = true; ctx.beginPath(); var p = getPos(e); ctx.moveTo(p.x, p.y); });
    canvas.addEventListener('touchmove',  function(e) { e.preventDefault(); if (!drawing) return; var p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
    canvas.addEventListener('touchend',   function()  { drawing = false; });

    return {
        clear: function() { ctx.clearRect(0, 0, canvas.width, canvas.height); },
        isEmpty: function() {
            var data = ctx.getImageData(0, 0, canvas.width, canvas.height).data;
            for (var i = 3; i < data.length; i += 4) { if (data[i] !== 0) return false; }
            return true;
        },
        toDataURL: function() { return canvas.toDataURL('image/png'); }
    };
}

// ─── Tenant Signature ────────────────────────────────────────────────────────

@if($contract->status === 'pending_signature' && !$contract->tenant_signature)
var tenantPad = makeSignaturePad('tenantSigCanvas');

document.getElementById('clearTenantSig').addEventListener('click', function() {
    tenantPad.clear();
});

document.getElementById('saveTenantSig').addEventListener('click', function() {
    if (!tenantPad || tenantPad.isEmpty()) {
        toastr.warning('{{ __("Please draw your signature first.") }}');
        return;
    }

    var btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>{{ __("Saving...") }}';

    $.ajax({
        url: '{{ route("tenant.contracts.sign", $contract->id) }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            tenant_signature: tenantPad.toDataURL()
        },
        success: function(res) {
            if (res.status === 200) {
                toastr.success(res.message || '{{ __("Signed successfully.") }}');
                setTimeout(function() { location.reload(); }, 1200);
            }
        },
        error: function(xhr) {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-check-line me-1"></i>{{ __("Sign Contract") }}';
            var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : '{{ __("An error occurred.") }}';
            toastr.error(msg);
        }
    });
});
@endif

// ─── LC1 Signature ───────────────────────────────────────────────────────────

@if($contract->status === 'pending_signature' && !$contract->lc1_signature)
var lc1Pad = makeSignaturePad('lc1SigCanvas');

document.getElementById('clearLc1Sig') && document.getElementById('clearLc1Sig').addEventListener('click', function() {
    lc1Pad.clear();
});

document.getElementById('saveLc1Sig') && document.getElementById('saveLc1Sig').addEventListener('click', function() {
    if (!lc1Pad || lc1Pad.isEmpty()) {
        toastr.warning('{{ __("Please draw the LC1 signature first.") }}');
        return;
    }

    var btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>{{ __("Saving...") }}';

    var formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('lc1_signature', lc1Pad.toDataURL());

    var nameInput  = document.getElementById('lc1NameInput');
    var phoneInput = document.getElementById('lc1PhoneInput');
    var stampFile  = document.getElementById('lc1StampFile');

    if (nameInput  && nameInput.value)  formData.append('lc1_name',  nameInput.value);
    if (phoneInput && phoneInput.value) formData.append('lc1_phone', phoneInput.value);
    if (stampFile  && stampFile.files.length > 0) formData.append('lc1_stamp', stampFile.files[0]);

    $.ajax({
        url: '{{ route("tenant.contracts.lc1-sign", $contract->id) }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            if (res.status === 200) {
                toastr.success(res.message || '{{ __("LC1 signature saved.") }}');
                setTimeout(function() { location.reload(); }, 1200);
            }
        },
        error: function(xhr) {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-check-line me-1"></i>{{ __("Save LC1 Signature") }}';
            var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : '{{ __("An error occurred.") }}';
            toastr.error(msg);
        }
    });
});
@endif
</script>
@endpush
