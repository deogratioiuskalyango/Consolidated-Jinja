@extends('tenant.layouts.app')

@push('style')
@include('common.layouts.datatable-style')
<style>
/* =====================================================
   MAINTENANCE REQUEST WIZARD — Responsive + Theme-aware
   ===================================================== */

/* ── Page wrapper theme ── */
.page-content-wrapper { background-color: var(--t-bg-card, #fff) !important; }
.page-title-box       { border-bottom-color: var(--t-border, #e2e8f0) !important; }

/* ── Modal shell ── */
#maintenanceWizard .modal-dialog {
    max-width: 700px;
    margin: 0.75rem auto;
}
#maintenanceWizard .modal-content {
    border: none;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,.22);
    display: flex;
    flex-direction: column;
    max-height: calc(100vh - 1.5rem);
}
#maintenanceWizard form {
    display: flex;
    flex-direction: column;
    flex: 1 1 auto;
    min-height: 0;
    overflow: hidden;
}

/* ── Header ── */
.mwiz-header {
    background: linear-gradient(135deg, #0f4c81 0%, #1a73c8 100%);
    padding: 22px 24px 0;
    flex-shrink: 0;
}
.mwiz-header-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    gap: 10px;
}
.mwiz-header-top h5 {
    color: #fff;
    font-weight: 700;
    font-size: .95rem;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mwiz-header-top .btn-close { filter: invert(1); opacity: .7; flex-shrink: 0; }

/* Step pills */
.mwiz-steps { display: flex; }
.mwiz-step-pill {
    flex: 1;
    text-align: center;
    padding: 8px 4px 12px;
    position: relative;
    cursor: default;
    min-width: 0;
}
.mwiz-step-pill::after {
    content: '';
    position: absolute;
    bottom: 0; left: 50%;
    transform: translateX(-50%);
    width: 0; height: 3px;
    background: #fff;
    border-radius: 3px 3px 0 0;
    transition: width .25s;
}
.mwiz-step-pill.active::after { width: 70%; }
.mwiz-step-dot {
    width: 26px; height: 26px;
    border-radius: 50%;
    background: rgba(255,255,255,.2);
    color: rgba(255,255,255,.6);
    font-size: .72rem; font-weight: 700;
    display: inline-flex; align-items: center; justify-content: center;
    margin-bottom: 4px;
    transition: all .2s;
}
.mwiz-step-pill.active .mwiz-step-dot { background: #fff; color: #0f4c81; }
.mwiz-step-pill.done   .mwiz-step-dot { background: rgba(52,211,153,.9); color: #fff; }
.mwiz-step-label {
    font-size: .63rem;
    color: rgba(255,255,255,.5);
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mwiz-step-pill.active .mwiz-step-label { color: rgba(255,255,255,.9); font-weight: 600; }

/* ── Body ── */
.mwiz-body {
    padding: 22px 24px 14px;
    background: var(--t-bg-card, #fff);
    flex: 1 1 auto;
    overflow-y: auto;
    min-height: 0;
    -webkit-overflow-scrolling: touch;
}
.mwiz-body::-webkit-scrollbar { width: 4px; }
.mwiz-body::-webkit-scrollbar-track { background: transparent; }
.mwiz-body::-webkit-scrollbar-thumb { background: var(--t-border, #e2e8f0); border-radius: 4px; }
.mwiz-panel { display: none; }
.mwiz-panel.active { display: block; }

.mwiz-step-hint {
    font-size: .82rem;
    color: var(--t-text-muted, #94a3b8);
    margin-bottom: 16px;
    line-height: 1.5;
}

/* ── Step 1: Category grid ── */
.mwiz-cat-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 4px;
}
.mwiz-cat-card {
    border: 2px solid var(--t-border, #e2e8f0);
    border-radius: 12px;
    padding: 14px 10px 12px;
    text-align: center;
    cursor: pointer;
    transition: border-color .15s, box-shadow .15s, transform .15s, background .15s;
    background: var(--t-bg-card, #fff);
    position: relative;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
}
.mwiz-cat-card:hover { border-color: #1a73c8; box-shadow: 0 4px 16px rgba(26,115,200,.12); transform: translateY(-1px); }
.mwiz-cat-card:active { transform: scale(.97); }
.mwiz-cat-card.selected { border-color: #1a73c8; background: rgba(26,115,200,.06); }
.mwiz-cat-card.selected::after {
    content: '✓';
    position: absolute;
    top: 7px; right: 8px;
    width: 17px; height: 17px;
    border-radius: 50%;
    background: #1a73c8;
    color: #fff;
    font-size: .58rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    line-height: 1;
}
.mwiz-cat-icon { font-size: 1.6rem; margin-bottom: 5px; display: block; line-height: 1; }
.mwiz-cat-name {
    font-size: .74rem;
    font-weight: 600;
    color: var(--t-text-primary, #1e293b);
    line-height: 1.3;
}
.mwiz-cat-count { font-size: .63rem; color: var(--t-text-muted, #94a3b8); margin-top: 2px; }

/* ── Step 2: Issue grid ── */
.mwiz-back-cat {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: .8rem;
    color: #1a73c8;
    cursor: pointer;
    margin-bottom: 14px;
    font-weight: 600;
    background: none;
    border: none;
    padding: 0;
}
.mwiz-back-cat:hover { text-decoration: underline; }
.mwiz-cat-heading {
    font-size: .9rem;
    font-weight: 700;
    color: var(--t-text-primary, #1e293b);
    margin-bottom: 12px;
}
.mwiz-issue-search { position: relative; margin-bottom: 12px; }
.mwiz-issue-search input {
    padding: 0 14px 0 36px;
    border-radius: 10px;
    border: 1px solid var(--t-border, #e2e8f0);
    background: var(--t-bg-hover, #f8fafc);
    color: var(--t-text-primary, #1e293b);
    font-size: .88rem;
    height: 40px;
    width: 100%;
    transition: border-color .15s, box-shadow .15s;
}
.mwiz-issue-search input:focus {
    outline: none;
    border-color: #1a73c8;
    box-shadow: 0 0 0 3px rgba(26,115,200,.1);
    background: var(--t-bg-card, #fff);
}
.mwiz-issue-search .search-icon {
    position: absolute;
    left: 12px; top: 50%;
    transform: translateY(-50%);
    color: var(--t-text-muted, #94a3b8);
    font-size: .88rem;
    pointer-events: none;
}
.mwiz-issue-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.mwiz-issue-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 11px 14px;
    border-radius: 10px;
    border: 1.5px solid var(--t-border, #e2e8f0);
    background: var(--t-bg-card, #fff);
    cursor: pointer;
    transition: border-color .15s, background .15s, box-shadow .15s;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
}
.mwiz-issue-item:hover { border-color: #1a73c8; background: rgba(26,115,200,.04); }
.mwiz-issue-item.selected {
    border-color: #1a73c8;
    background: rgba(26,115,200,.08);
    box-shadow: 0 0 0 3px rgba(26,115,200,.1);
}
.mwiz-issue-radio {
    width: 18px; height: 18px;
    border-radius: 50%;
    border: 2px solid var(--t-border, #cbd5e1);
    background: var(--t-bg-card, #fff);
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    transition: all .15s;
}
.mwiz-issue-item.selected .mwiz-issue-radio {
    border-color: #1a73c8;
    background: #1a73c8;
}
.mwiz-issue-item.selected .mwiz-issue-radio::after {
    content: '';
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #fff;
}
.mwiz-issue-label {
    font-size: .85rem;
    font-weight: 500;
    color: var(--t-text-primary, #1e293b);
    flex: 1;
    line-height: 1.3;
}
.mwiz-issue-empty {
    text-align: center;
    padding: 24px;
    color: var(--t-text-muted, #94a3b8);
    font-size: .84rem;
}

/* ── Step 3: Details ── */
.mwiz-selected-issue-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(26,115,200,.1);
    border: 1px solid rgba(26,115,200,.25);
    border-radius: 20px;
    padding: 5px 14px;
    font-size: .8rem;
    font-weight: 600;
    color: #1a73c8;
    margin-bottom: 18px;
}
.mwiz-selected-issue-chip button {
    background: none; border: none;
    color: #1a73c8; font-size: .9rem;
    padding: 0; line-height: 1;
    cursor: pointer; opacity: .7;
}
.mwiz-selected-issue-chip button:hover { opacity: 1; }

/* Priority buttons */
.mwiz-priority-row {
    display: flex;
    gap: 8px;
    margin-bottom: 18px;
}
.mwiz-priority-btn {
    flex: 1;
    border: 2px solid var(--t-border, #e2e8f0);
    border-radius: 10px;
    padding: 10px 4px 8px;
    text-align: center;
    cursor: pointer;
    transition: all .15s;
    background: var(--t-bg-card, #fff);
    user-select: none;
    min-height: 58px;
    display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 3px;
}
.mwiz-priority-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.08); }
.mwiz-priority-btn .p-icon { font-size: 1.1rem; line-height: 1; }
.mwiz-priority-btn .p-name { font-size: .68rem; font-weight: 600; color: var(--t-text-secondary, #475569); }
/* selected states */
.mwiz-priority-btn[data-p="1"].selected { border-color: #64748b; background: rgba(100,116,139,.10); }
.mwiz-priority-btn[data-p="1"].selected .p-name { color: #64748b; }
.mwiz-priority-btn[data-p="2"].selected { border-color: #1a73c8; background: rgba(26,115,200,.10); }
.mwiz-priority-btn[data-p="2"].selected .p-name { color: #1a73c8; }
.mwiz-priority-btn[data-p="3"].selected { border-color: #f59e0b; background: rgba(245,158,11,.10); }
.mwiz-priority-btn[data-p="3"].selected .p-name { color: #d97706; }
.mwiz-priority-btn[data-p="4"].selected { border-color: #ef4444; background: rgba(239,68,68,.10); }
.mwiz-priority-btn[data-p="4"].selected .p-name { color: #dc2626; }
/* dark mode priority */
body[data-bs-theme="dark"] .mwiz-priority-btn[data-p="1"].selected { background: rgba(100,116,139,.18); }
body[data-bs-theme="dark"] .mwiz-priority-btn[data-p="2"].selected { background: rgba(26,115,200,.18); }
body[data-bs-theme="dark"] .mwiz-priority-btn[data-p="3"].selected { background: rgba(245,158,11,.18); }
body[data-bs-theme="dark"] .mwiz-priority-btn[data-p="4"].selected { background: rgba(239,68,68,.18); }

/* Details textarea */
.mwiz-details-label {
    font-size: .82rem;
    font-weight: 600;
    color: var(--t-text-primary, #1e293b);
    margin-bottom: 6px;
    display: block;
}
.mwiz-textarea {
    width: 100%;
    border-radius: 10px;
    border: 1.5px solid var(--t-border, #e2e8f0);
    background: var(--t-bg-hover, #f8fafc);
    color: var(--t-text-primary, #1e293b);
    padding: 12px 14px;
    font-size: .88rem;
    resize: vertical;
    min-height: 90px;
    transition: border-color .15s, box-shadow .15s;
    line-height: 1.5;
}
.mwiz-textarea:focus {
    outline: none;
    border-color: #1a73c8;
    box-shadow: 0 0 0 3px rgba(26,115,200,.1);
    background: var(--t-bg-card, #fff);
}
.mwiz-textarea::placeholder { color: var(--t-text-muted, #94a3b8); }

/* File upload zone */
.mwiz-upload {
    border: 2px dashed var(--t-border, #e2e8f0);
    border-radius: 10px;
    padding: 18px;
    text-align: center;
    cursor: pointer;
    transition: border-color .15s, background .15s;
    background: var(--t-bg-hover, #f8fafc);
    margin-top: 14px;
}
.mwiz-upload:hover { border-color: #1a73c8; background: rgba(26,115,200,.04); }
.mwiz-upload input[type=file] { display: none; }
.mwiz-upload-icon { font-size: 1.6rem; display: block; margin-bottom: 6px; color: var(--t-text-muted, #94a3b8); }
.mwiz-upload-text { font-size: .8rem; color: var(--t-text-muted, #94a3b8); }
.mwiz-upload-text strong { color: #1a73c8; }
.mwiz-file-name {
    font-size: .78rem;
    color: var(--t-text-secondary, #475569);
    margin-top: 6px;
    font-weight: 500;
}

/* ── Footer ── */
.mwiz-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 24px;
    background: var(--t-bg-card, #fff);
    border-top: 1px solid var(--t-border, #e2e8f0);
    flex-shrink: 0;
    gap: 10px;
}
.mwiz-btn {
    padding: 10px 22px;
    border-radius: 10px;
    font-size: .88rem;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: all .15s;
    display: inline-flex; align-items: center; gap: 6px;
}
.mwiz-btn-back {
    background: var(--t-bg-hover, #f1f5f9);
    color: var(--t-text-secondary, #475569);
    border: 1.5px solid var(--t-border, #e2e8f0);
}
.mwiz-btn-back:hover { background: var(--t-border, #e2e8f0); }
.mwiz-btn-next {
    background: linear-gradient(135deg, #0f4c81, #1a73c8);
    color: #fff;
    box-shadow: 0 4px 14px rgba(26,115,200,.35);
}
.mwiz-btn-next:hover { box-shadow: 0 6px 20px rgba(26,115,200,.45); transform: translateY(-1px); }
.mwiz-btn-next:disabled { opacity: .5; cursor: not-allowed; transform: none; box-shadow: none; }

/* ── Edit modal ── */
#editModal .modal-header { background: linear-gradient(135deg, #0f4c81, #1a73c8); }
#editModal .modal-title { color: #fff; }
#editModal .btn-close { filter: invert(1); }
#editModal .form-control,
#editModal .form-select {
    background: var(--t-bg-hover, #f8fafc);
    border-color: var(--t-border, #e2e8f0);
    color: var(--t-text-primary, #1e293b);
}
#editModal .form-control:focus,
#editModal .form-select:focus {
    background: var(--t-bg-card, #fff);
    border-color: #1a73c8;
    box-shadow: 0 0 0 3px rgba(26,115,200,.1);
}
#editModal .modal-body { background: var(--t-bg-card, #fff); }
#editModal .modal-footer { background: var(--t-bg-card, #fff); border-top-color: var(--t-border, #e2e8f0); }
#editModal label { color: var(--t-text-primary, #1e293b); }

/* ── Search bar ── */
.property-search-inner-bg {
    background-color: var(--t-bg-hover, #f8fafc) !important;
    border-color: var(--t-border, #e2e8f0) !important;
}

/* ── DataTable ── */
.all-maintainer-table-area .bg-off-white {
    background-color: var(--t-bg-hover, #f8fafc) !important;
    border-color: var(--t-border, #e2e8f0) !important;
}

/* ── Responsive ── */
@media (max-width: 576px) {
    .mwiz-cat-grid { grid-template-columns: repeat(2, 1fr); }
    #maintenanceWizard .modal-dialog {
        margin: 0;
        align-items: flex-end;
        display: flex;
        min-height: 100vh;
    }
    #maintenanceWizard .modal-content {
        border-radius: 18px 18px 0 0;
        max-height: 92vh;
        width: 100%;
    }
    .mwiz-footer { flex-wrap: wrap; gap: 8px; }
    .mwiz-btn { flex: 1; min-width: 110px; }
    .mwiz-priority-row { gap: 6px; }
    .mwiz-priority-btn { padding: 8px 2px 6px; min-height: 52px; }
    .mwiz-priority-btn .p-name { font-size: .62rem; }
    .mwiz-body { padding: 18px 16px 12px; }
    .mwiz-footer { padding: 14px 16px; }
}
@media (min-width: 577px) and (max-width: 768px) {
    .mwiz-cat-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (min-width: 769px) {
    .mwiz-cat-grid { grid-template-columns: repeat(4, 1fr); }
}
</style>
@endpush

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
                                    <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}" title="{{ __('Dashboard') }}">{{ __('Dashboard') }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="property-top-search-bar">
                        <div class="property-search-inner-bg bg-off-white theme-border radius-4 p-25 pb-0 mb-25">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <div class="property-top-search-bar-right text-end">
                                        <button type="button" class="theme-btn mb-25" id="add"
                                            data-bs-toggle="modal" data-bs-target="#maintenanceWizard"
                                            title="{{ __('Add Maintenance Request') }}">
                                            <i class="ri-add-line me-1"></i>{{ __('New Request') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="all-maintainer-table-area">
                        <div class="bg-off-white theme-border radius-4 p-25">
                            <h5 class="mb-20" style="color:var(--t-text-primary,#1e293b)">{{ __('My Maintenance Requests') }}</h5>
                            <table id="allMaintenanceRequestDataTable" class="table bg-off-white theme-border dt-responsive w-100">
                                <thead>
                                    <tr>
                                        <th>{{ __('Request ID') }}</th>
                                        <th>{{ __('Issue') }}</th>
                                        <th>{{ __('Details') }}</th>
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

{{-- ─────────────────────────────────────────────────────
     ADD WIZARD MODAL
─────────────────────────────────────────────────────── --}}
<div class="modal fade" id="maintenanceWizard" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {{-- Header --}}
            <div class="mwiz-header">
                <div class="mwiz-header-top">
                    <h5>🔧 {{ __('New Maintenance Request') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="mwiz-steps">
                    <div class="mwiz-step-pill active" id="mwiz-pill-1">
                        <div class="mwiz-step-dot">1</div>
                        <span class="mwiz-step-label">{{ __('Category') }}</span>
                    </div>
                    <div class="mwiz-step-pill" id="mwiz-pill-2">
                        <div class="mwiz-step-dot">2</div>
                        <span class="mwiz-step-label">{{ __('Issue') }}</span>
                    </div>
                    <div class="mwiz-step-pill" id="mwiz-pill-3">
                        <div class="mwiz-step-dot">3</div>
                        <span class="mwiz-step-label">{{ __('Details') }}</span>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form id="mwizForm" class="ajax" action="{{ route('tenant.maintenance-request.store') }}" method="POST" data-handler="getShowMessage">
                <input type="hidden" name="property_id" value="{{ $tenant->property_id }}">
                <input type="hidden" name="unit_id"     value="{{ $tenant->unit_id }}">
                <input type="hidden" name="issue_id"    id="mwiz_issue_id">
                <input type="hidden" name="priority"    id="mwiz_priority" value="2">

                {{-- Scrollable body --}}
                <div class="mwiz-body">

                    {{-- ── STEP 1: Category ── --}}
                    <div class="mwiz-panel active" id="mwiz-step-1">
                        <p class="mwiz-step-hint">{{ __('Select the area your issue is related to.') }}</p>
                        <div class="mwiz-cat-grid" id="mwizCatGrid">
                            @php
                                $catIcons = [
                                    'Plumbing'          => '🚿',
                                    'Electrical'        => '⚡',
                                    'HVAC & Climate'    => '❄️',
                                    'Structural'        => '🏗️',
                                    'Appliances'        => '🔧',
                                    'Pest Control'      => '🪲',
                                    'Security'          => '🔒',
                                    'Flooring'          => '🪵',
                                    'Painting & Walls'  => '🎨',
                                    'Common Areas'      => '🏢',
                                    'Outdoor & Garden'  => '🌿',
                                    'General'           => '📋',
                                ];
                            @endphp
                            @foreach ($issuesByCategory as $category => $catIssues)
                                <div class="mwiz-cat-card" data-cat="{{ $category }}" tabindex="0">
                                    <span class="mwiz-cat-icon">{{ $catIcons[$category] ?? '🔧' }}</span>
                                    <div class="mwiz-cat-name">{{ $category }}</div>
                                    <div class="mwiz-cat-count">{{ $catIssues->count() }} {{ __('issues') }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ── STEP 2: Issue picker ── --}}
                    <div class="mwiz-panel" id="mwiz-step-2">
                        <button type="button" class="mwiz-back-cat" id="mwizBackCat">
                            ← {{ __('Back to categories') }}
                        </button>
                        <div class="mwiz-cat-heading" id="mwizCatTitle"></div>
                        <div class="mwiz-issue-search">
                            <span class="search-icon">🔍</span>
                            <input type="text" id="mwizIssueSearch" placeholder="{{ __('Search issues...') }}" autocomplete="off">
                        </div>
                        <div class="mwiz-issue-list" id="mwizIssueList"></div>
                    </div>

                    {{-- ── STEP 3: Details ── --}}
                    <div class="mwiz-panel" id="mwiz-step-3">
                        <div class="mwiz-selected-issue-chip" id="mwizSelectedChip">
                            <span id="mwizChipText"></span>
                            <button type="button" id="mwizChangeIssue" title="{{ __('Change') }}">✕</button>
                        </div>

                        <label class="mwiz-details-label">{{ __('Urgency Level') }}</label>
                        <div class="mwiz-priority-row mb-20">
                            <button type="button" class="mwiz-priority-btn" data-p="1">
                                <span class="p-icon">🟢</span>
                                <span class="p-name">{{ __('Low') }}</span>
                            </button>
                            <button type="button" class="mwiz-priority-btn selected" data-p="2">
                                <span class="p-icon">🔵</span>
                                <span class="p-name">{{ __('Normal') }}</span>
                            </button>
                            <button type="button" class="mwiz-priority-btn" data-p="3">
                                <span class="p-icon">🟡</span>
                                <span class="p-name">{{ __('High') }}</span>
                            </button>
                            <button type="button" class="mwiz-priority-btn" data-p="4">
                                <span class="p-icon">🔴</span>
                                <span class="p-name">{{ __('Urgent') }}</span>
                            </button>
                        </div>

                        <label class="mwiz-details-label" for="mwiz_details">{{ __('Describe the issue') }} <span style="color:#ef4444">*</span></label>
                        <textarea class="mwiz-textarea" id="mwiz_details" name="details" rows="4"
                            placeholder="{{ __('Location, when it started, anything you have already tried...') }}"></textarea>

                        <div class="mwiz-upload" id="mwizUploadZone">
                            <input type="file" name="attach" id="mwiz_attach" accept="image/*,.pdf,.doc,.docx">
                            <span class="mwiz-upload-icon">📎</span>
                            <div class="mwiz-upload-text">{{ __('Click to attach a photo or file') }} <strong>({{ __('optional') }})</strong></div>
                            <div class="mwiz-file-name" id="mwizFileName"></div>
                        </div>
                    </div>

                </div>{{-- /mwiz-body --}}

                {{-- Footer --}}
                <div class="mwiz-footer">
                    <button type="button" class="mwiz-btn mwiz-btn-back" id="mwizBtnBack" style="visibility:hidden">
                        ← {{ __('Back') }}
                    </button>
                    <button type="button" class="mwiz-btn mwiz-btn-next" id="mwizBtnNext" disabled>
                        {{ __('Choose Issue') }} →
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ─────────────────────────────────────────────────────
     EDIT MODAL
─────────────────────────────────────────────────────── --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('Edit Maintenance Request') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="ajax" action="{{ route('tenant.maintenance-request.store') }}" method="POST" data-handler="getShowMessage">
                <input type="hidden" name="property_id" value="{{ $tenant->property_id }}">
                <input type="hidden" name="unit_id"     value="{{ $tenant->unit_id }}">
                <input type="hidden" name="id"          class="edit-id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Issue') }}</label>
                        <select class="form-select edit-issue" name="issue_id">
                            <option value="">-- {{ __('Select Issue') }} --</option>
                            @foreach ($issuesByCategory as $cat => $catIssues)
                                <optgroup label="{{ $cat }}">
                                    @foreach ($catIssues as $issue)
                                        <option value="{{ $issue->id }}">{{ $issue->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Urgency') }}</label>
                        <select class="form-select edit-priority" name="priority">
                            <option value="1">🟢 {{ __('Low') }}</option>
                            <option value="2" selected>🔵 {{ __('Normal') }}</option>
                            <option value="3">🟡 {{ __('High') }}</option>
                            <option value="4">🔴 {{ __('Urgent') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Details') }}</label>
                        <textarea class="form-control edit-details" name="details" rows="4" placeholder="{{ __('Details') }}"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Attach Photo / File') }}</label>
                        <input type="file" class="form-control" name="attach">
                    </div>
                </div>
                <div class="modal-footer justify-content-start">
                    <button type="button" class="theme-btn-back me-2" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="theme-btn">{{ __('Update Request') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- View Modal --}}
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content"></div>
    </div>
</div>

<input type="hidden" id="maintenanceIndexRoute" value="{{ route('tenant.maintenance-request.index') }}">
<input type="hidden" id="getInfoRoute"          value="{{ route('tenant.maintenance-request.get.info') }}">

{{-- Issue data for wizard --}}
<script>
window.mwizIssues = @json($issuesByCategory->map(fn($items) => $items->map(fn($i) => ['id' => $i->id, 'name' => $i->name])));
</script>
@endsection

@push('script')
@include('common.layouts.datatable-script')
<script src="{{ asset('assets/js/custom/tenant-maintenance-request.js') }}"></script>
<script>
(function () {
    'use strict';

    /* ── State ── */
    var state = { step: 1, cat: null, issueId: null, issueName: null };

    /* ── DOM refs ── */
    var $modal   = document.getElementById('maintenanceWizard');
    var $form    = document.getElementById('mwizForm');
    var $body    = $form.querySelector('.mwiz-body');
    var $back    = document.getElementById('mwizBtnBack');
    var $next    = document.getElementById('mwizBtnNext');
    var $issueInput = document.getElementById('mwiz_issue_id');
    var $prioInput  = document.getElementById('mwiz_priority');
    var $details    = document.getElementById('mwiz_details');
    var $fileInput  = document.getElementById('mwiz_attach');
    var $fileName   = document.getElementById('mwizFileName');
    var $chipText   = document.getElementById('mwizChipText');
    var $catTitle   = document.getElementById('mwizCatTitle');
    var $issueList  = document.getElementById('mwizIssueList');
    var $issueSearch= document.getElementById('mwizIssueSearch');

    /* ── Helpers ── */
    function showStep(n) {
        state.step = n;
        [1, 2, 3].forEach(function (i) {
            document.getElementById('mwiz-step-' + i).classList.toggle('active', i === n);
            var pill = document.getElementById('mwiz-pill-' + i);
            pill.classList.toggle('active', i === n);
            pill.classList.toggle('done',   i < n);
        });
        /* Back button */
        $back.style.visibility = n > 1 ? 'visible' : 'hidden';
        /* Next / Submit button */
        if (n === 1) {
            $next.textContent = 'Choose Issue →';
            $next.type = 'button';
            $next.disabled = !state.cat;
        } else if (n === 2) {
            $next.textContent = 'Continue →';
            $next.type = 'button';
            $next.disabled = !state.issueId;
        } else {
            $next.textContent = '✔ Submit Request';
            $next.type = 'submit';
            $next.disabled = false;
        }
    }

    function renderIssues(cat, filter) {
        filter = (filter || '').toLowerCase();
        var items = (window.mwizIssues && window.mwizIssues[cat]) ? window.mwizIssues[cat] : [];
        var html = '';
        items.forEach(function (item) {
            if (filter && item.name.toLowerCase().indexOf(filter) === -1) return;
            var sel = item.id == state.issueId ? 'selected' : '';
            html += '<div class="mwiz-issue-item ' + sel + '" data-id="' + item.id + '" data-name="' + item.name.replace(/"/g, '&quot;') + '">'
                  + '<div class="mwiz-issue-radio"></div>'
                  + '<span class="mwiz-issue-label">' + item.name + '</span>'
                  + '</div>';
        });
        if (!html) html = '<div class="mwiz-issue-empty">No issues match your search.</div>';
        $issueList.innerHTML = html;
    }

    function selectCat(cat) {
        state.cat = cat;
        /* highlight card */
        document.querySelectorAll('.mwiz-cat-card').forEach(function (c) {
            c.classList.toggle('selected', c.dataset.cat === cat);
        });
        showStep(1);
    }

    function selectIssue(id, name) {
        state.issueId   = id;
        state.issueName = name;
        $issueInput.value = id;
        /* highlight in list */
        document.querySelectorAll('.mwiz-issue-item').forEach(function (el) {
            el.classList.toggle('selected', el.dataset.id == id);
        });
        showStep(2);
    }

    function goToStep2() {
        $catTitle.textContent = state.cat;
        $issueSearch.value = '';
        renderIssues(state.cat, '');
        showStep(2);
    }

    function goToStep3() {
        $chipText.textContent = state.issueName;
        showStep(3);
    }

    function resetWizard() {
        state = { step: 1, cat: null, issueId: null, issueName: null };
        $issueInput.value = '';
        $prioInput.value  = '2';
        $details.value    = '';
        if ($fileInput) $fileInput.value = '';
        $fileName.textContent = '';
        /* reset priority buttons */
        document.querySelectorAll('.mwiz-priority-btn').forEach(function (b) {
            b.classList.toggle('selected', b.dataset.p === '2');
        });
        /* reset cat cards */
        document.querySelectorAll('.mwiz-cat-card').forEach(function (c) { c.classList.remove('selected'); });
        showStep(1);
    }

    /* ── Events ── */

    /* Category cards */
    document.getElementById('mwizCatGrid').addEventListener('click', function (e) {
        var card = e.target.closest('.mwiz-cat-card');
        if (!card) return;
        selectCat(card.dataset.cat);
    });
    document.getElementById('mwizCatGrid').addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            var card = e.target.closest('.mwiz-cat-card');
            if (card) { e.preventDefault(); selectCat(card.dataset.cat); }
        }
    });

    /* Back to categories */
    document.getElementById('mwizBackCat').addEventListener('click', function () {
        showStep(1);
    });

    /* Issue list */
    $issueList.addEventListener('click', function (e) {
        var item = e.target.closest('.mwiz-issue-item');
        if (!item) return;
        selectIssue(item.dataset.id, item.dataset.name);
    });

    /* Issue search */
    $issueSearch.addEventListener('input', function () {
        renderIssues(state.cat, this.value);
    });

    /* Priority buttons */
    document.querySelectorAll('.mwiz-priority-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.mwiz-priority-btn').forEach(function (b) { b.classList.remove('selected'); });
            btn.classList.add('selected');
            $prioInput.value = btn.dataset.p;
        });
    });

    /* File upload zone */
    document.getElementById('mwizUploadZone').addEventListener('click', function () { $fileInput.click(); });
    $fileInput.addEventListener('change', function () {
        $fileName.textContent = this.files.length ? this.files[0].name : '';
    });

    /* Change issue chip → go back to step 2 */
    document.getElementById('mwizChangeIssue').addEventListener('click', function () {
        state.issueId = null;
        state.issueName = null;
        $issueInput.value = '';
        goToStep2();
    });

    /* Back button */
    $back.addEventListener('click', function () {
        if (state.step === 2) showStep(1);
        else if (state.step === 3) goToStep2();
    });

    /* Next button */
    $next.addEventListener('click', function () {
        if (state.step === 1 && state.cat) goToStep2();
        else if (state.step === 2 && state.issueId) goToStep3();
    });

    /* Modal reset on hide */
    $modal.addEventListener('hidden.bs.modal', resetWizard);

    /* ── Client-side validation on submit ── */
    $form.addEventListener('submit', function (e) {
        if (state.step !== 3) { e.preventDefault(); e.stopImmediatePropagation(); return; }
        if (!$details.value.trim()) {
            e.preventDefault();
            e.stopImmediatePropagation();
            $details.style.borderColor = '#ef4444';
            $details.style.boxShadow   = '0 0 0 3px rgba(239,68,68,.15)';
            $details.focus();
            return;
        }
        $details.style.borderColor = '';
        $details.style.boxShadow   = '';
    });
    $details.addEventListener('input', function () {
        this.style.borderColor = '';
        this.style.boxShadow   = '';
    });

    /* ── Edit modal pre-fill ── */
    $(document).on('click', '.edit', function () {
        var id = $(this).data('id');
        commonAjax('GET', $('#getInfoRoute').val(), function (resp) {
            var d = resp.data;
            var $em = $('#editModal');
            $em.find('.edit-id').val(d.id);
            $em.find('.edit-issue').val(d.issue_id);
            $em.find('.edit-priority').val(d.priority || 2);
            $em.find('.edit-details').val(d.details);
            $em.modal('show');
        }, function () {}, { id: id });
    });

    /* DataTable already initialised in tenant-maintenance-request.js */
})();
</script>
@endpush
