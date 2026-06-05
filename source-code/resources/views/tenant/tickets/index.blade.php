@extends('tenant.layouts.app')

@push('style')
<style>
/* =====================================================
   TICKET WIZARD — Fully Responsive + Theme-aware
   ===================================================== */

/* ── Modal shell ── */
#createTicketWizard .modal-dialog {
    max-width: 680px;
    margin: 0.75rem auto;
}
#createTicketWizard .modal-content {
    border: none;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,.2);
    /* Flex column so header/footer stay fixed, body scrolls */
    display: flex;
    flex-direction: column;
    max-height: calc(100vh - 1.5rem);
}
/* Form must be a flex column too so wiz-body can flex-grow */
#createTicketWizard form {
    display: flex;
    flex-direction: column;
    flex: 1 1 auto;
    min-height: 0;
    overflow: hidden;
}

/* ── Header ── */
.wiz-header {
    background: linear-gradient(135deg, #1a1f5e 0%, #2d3482 100%);
    padding: 22px 24px 0;
    flex-shrink: 0;
}
.wiz-header-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    gap: 10px;
}
.wiz-header-top h5 {
    color: #fff;
    font-weight: 700;
    font-size: .95rem;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.wiz-header-top .btn-close { filter: invert(1); opacity: .7; flex-shrink: 0; }

/* Step pills */
.wiz-steps { display: flex; }
.wiz-step-pill {
    flex: 1;
    text-align: center;
    padding: 8px 4px 12px;
    position: relative;
    cursor: default;
    min-width: 0;
}
.wiz-step-pill::after {
    content: '';
    position: absolute;
    bottom: 0; left: 50%;
    transform: translateX(-50%);
    width: 0; height: 3px;
    background: #fff;
    border-radius: 3px 3px 0 0;
    transition: width .25s;
}
.wiz-step-pill.active::after { width: 70%; }
.wiz-step-dot {
    width: 26px; height: 26px;
    border-radius: 50%;
    background: rgba(255,255,255,.2);
    color: rgba(255,255,255,.6);
    font-size: .72rem; font-weight: 700;
    display: inline-flex; align-items: center; justify-content: center;
    margin-bottom: 4px;
    transition: all .2s;
}
.wiz-step-pill.active .wiz-step-dot { background: #fff; color: #1a1f5e; }
.wiz-step-pill.done   .wiz-step-dot { background: rgba(52,211,153,.9); color: #fff; }
.wiz-step-label {
    font-size: .63rem;
    color: rgba(255,255,255,.5);
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.wiz-step-pill.active .wiz-step-label { color: rgba(255,255,255,.9); font-weight: 600; }

/* ── Body ── */
.wiz-body {
    padding: 22px 24px 14px;
    background: var(--t-bg-card, #fff);
    flex: 1 1 auto;
    overflow-y: auto;
    min-height: 0;
    -webkit-overflow-scrolling: touch;
}
.wiz-body::-webkit-scrollbar { width: 4px; }
.wiz-body::-webkit-scrollbar-track { background: transparent; }
.wiz-body::-webkit-scrollbar-thumb { background: var(--t-border, #e2e8f0); border-radius: 4px; }
.wiz-panel { display: none; }
.wiz-panel.active { display: block; }

.wiz-step-hint {
    font-size: .82rem;
    color: var(--t-text-muted, #94a3b8);
    margin-bottom: 16px;
    line-height: 1.5;
}

/* ── Step 1: Category grid ── */
.wiz-cat-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    margin-bottom: 4px;
}
.wiz-cat-card {
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
.wiz-cat-card:hover, .wiz-cat-card:focus-visible {
    border-color: #2d3482;
    box-shadow: 0 4px 16px rgba(45,52,130,.12);
    transform: translateY(-1px);
}
.wiz-cat-card:active { transform: scale(.97); }
.wiz-cat-card.selected {
    border-color: #2d3482;
    background: rgba(45,52,130,.06);
}
.wiz-cat-card.selected::after {
    content: '✓';
    position: absolute;
    top: 7px; right: 8px;
    width: 17px; height: 17px;
    border-radius: 50%;
    background: #2d3482;
    color: #fff;
    font-size: .58rem;
    font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    line-height: 1;
}
.wiz-cat-icon { font-size: 1.5rem; margin-bottom: 5px; display: block; line-height: 1; }
.wiz-cat-name {
    font-size: .74rem;
    font-weight: 600;
    color: var(--t-text-primary, #1e293b);
    line-height: 1.3;
}
.wiz-cat-count { font-size: .63rem; color: var(--t-text-muted, #94a3b8); margin-top: 2px; }

/* ── Step 2: Topic list + priority ── */
.wiz-topic-search { position: relative; margin-bottom: 12px; }
.wiz-topic-search input {
    padding: 0 14px 0 36px;
    border-radius: 10px;
    border: 1px solid var(--t-border, #e2e8f0);
    background: var(--t-bg-hover, #f8fafc);
    color: var(--t-text-primary, #1e293b);
    font-size: .88rem;
    height: 42px;
    width: 100%;
    transition: border-color .15s, box-shadow .15s;
}
.wiz-topic-search input:focus {
    outline: none;
    border-color: #2d3482;
    box-shadow: 0 0 0 3px rgba(45,52,130,.1);
    background: var(--t-bg-card, #fff);
}
.wiz-topic-search .search-icon {
    position: absolute;
    left: 12px; top: 50%;
    transform: translateY(-50%);
    color: var(--t-text-muted, #94a3b8);
    font-size: .9rem;
    pointer-events: none;
}
.wiz-topic-list {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid var(--t-border, #e2e8f0);
    border-radius: 12px;
    margin-bottom: 18px;
    background: var(--t-bg-card, #fff);
    -webkit-overflow-scrolling: touch;
}
.wiz-topic-list::-webkit-scrollbar { width: 4px; }
.wiz-topic-list::-webkit-scrollbar-track { background: transparent; }
.wiz-topic-list::-webkit-scrollbar-thumb { background: var(--t-border, #e2e8f0); border-radius: 10px; }
.wiz-topic-item {
    padding: 11px 14px;
    cursor: pointer;
    font-size: .84rem;
    color: var(--t-text-secondary, #475569);
    border-bottom: 1px solid var(--t-border, #e2e8f0);
    transition: background .12s, color .12s;
    display: flex;
    align-items: center;
    gap: 8px;
    -webkit-tap-highlight-color: transparent;
    min-height: 44px; /* touch target */
}
.wiz-topic-item:last-child { border-bottom: none; }
.wiz-topic-item:hover    { background: var(--t-bg-hover, #f8fafc); color: var(--t-text-primary, #1e293b); }
.wiz-topic-item:active   { background: rgba(45,52,130,.06); }
.wiz-topic-item.selected { background: rgba(45,52,130,.07); color: #2d3482; font-weight: 600; }
.wiz-topic-item.selected::before {
    content: '✓';
    flex-shrink: 0;
    width: 18px; height: 18px;
    border-radius: 50%;
    background: #2d3482;
    color: #fff;
    font-size: .58rem;
    font-weight: 700;
    display: inline-flex; align-items: center; justify-content: center;
}
.wiz-topic-item.hidden { display: none; }
.wiz-topic-empty {
    padding: 20px;
    text-align: center;
    font-size: .82rem;
    color: var(--t-text-muted, #94a3b8);
}

/* Priority */
.wiz-section-label {
    font-size: .72rem;
    font-weight: 700;
    color: var(--t-text-secondary, #475569);
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 10px;
}
.wiz-priority-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
}
.wiz-priority-btn {
    border: 2px solid var(--t-border, #e2e8f0);
    border-radius: 10px;
    padding: 10px 4px 8px;
    text-align: center;
    cursor: pointer;
    transition: border-color .15s, background .15s, transform .12s;
    background: var(--t-bg-card, #fff);
    user-select: none;
    -webkit-tap-highlight-color: transparent;
    min-height: 58px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 3px;
}
.wiz-priority-btn:hover  { transform: translateY(-1px); }
.wiz-priority-btn:active { transform: scale(.96); }
.wiz-priority-btn .p-icon { font-size: 1.1rem; line-height: 1; }
.wiz-priority-btn .p-name { font-size: .7rem; font-weight: 700; }
.wiz-priority-btn[data-p="1"] .p-name  { color: #64748b; }
.wiz-priority-btn[data-p="2"] .p-name  { color: #3b82f6; }
.wiz-priority-btn[data-p="3"] .p-name  { color: #f59e0b; }
.wiz-priority-btn[data-p="4"] .p-name  { color: #ef4444; }
.wiz-priority-btn[data-p="1"].selected { border-color: #64748b; background: rgba(100,116,139,.10); }
.wiz-priority-btn[data-p="2"].selected { border-color: #3b82f6; background: rgba(59,130,246,.10); }
.wiz-priority-btn[data-p="3"].selected { border-color: #f59e0b; background: rgba(245,158,11,.10); }
.wiz-priority-btn[data-p="4"].selected { border-color: #ef4444; background: rgba(239,68,68,.10); }

/* ── Step 3: Details ── */
.wiz-chips-row {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 16px;
}
.wiz-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(45,52,130,.08);
    color: #2d3482;
    font-size: .75rem;
    font-weight: 600;
    padding: 4px 11px;
    border-radius: 20px;
    border: 1px solid rgba(45,52,130,.18);
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.wiz-form-label {
    font-size: .72rem;
    font-weight: 700;
    color: var(--t-text-secondary, #475569);
    text-transform: uppercase;
    letter-spacing: .4px;
    margin-bottom: 6px;
    display: block;
}
.wiz-form-control {
    display: block;
    width: 100%;
    border: 1px solid var(--t-border, #e2e8f0);
    border-radius: 10px;
    padding: 10px 14px;
    font-size: .88rem;
    color: var(--t-text-primary, #1e293b);
    background: var(--t-bg-hover, #f8fafc);
    transition: border-color .15s, box-shadow .15s, background .15s;
    resize: vertical;
    font-family: inherit;
    -webkit-appearance: none;
}
.wiz-form-control:focus {
    outline: none;
    border-color: #2d3482;
    box-shadow: 0 0 0 3px rgba(45,52,130,.1);
    background: var(--t-bg-card, #fff);
}
.wiz-char-counter { text-align: right; font-size: .68rem; color: var(--t-text-muted, #94a3b8); margin-top: 3px; }
.wiz-char-counter.warn { color: #f59e0b; }
.wiz-char-counter.over { color: #ef4444; }
.wiz-field { margin-bottom: 14px; }

/* Drop zone */
.wiz-drop-zone {
    border: 2px dashed var(--t-border, #e2e8f0);
    border-radius: 12px;
    padding: 18px 16px;
    text-align: center;
    cursor: pointer;
    transition: border-color .15s, background .15s;
    background: var(--t-bg-hover, #f8fafc);
    position: relative;
    -webkit-tap-highlight-color: transparent;
}
.wiz-drop-zone:hover, .wiz-drop-zone.drag-over {
    border-color: #2d3482;
    background: rgba(45,52,130,.04);
}
.wiz-drop-zone input[type="file"] {
    position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
}
.wiz-drop-icon { font-size: 1.4rem; color: var(--t-text-muted, #94a3b8); margin-bottom: 5px; }
.wiz-drop-text { font-size: .8rem; color: var(--t-text-muted, #94a3b8); }
.wiz-drop-text strong { color: #2d3482; }
.wiz-drop-hint { font-size: .68rem; color: var(--t-text-muted, #94a3b8); margin-top: 3px; }
.wiz-file-list { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
.wiz-file-chip {
    display: inline-flex; align-items: center; gap: 5px;
    background: rgba(45,52,130,.08); color: #2d3482;
    padding: 4px 10px; border-radius: 20px; font-size: .74rem; font-weight: 600;
    border: 1px solid rgba(45,52,130,.18);
    max-width: 100%;
}
.wiz-file-chip span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 140px; }
.wiz-file-chip .rm { cursor: pointer; font-size: .8rem; opacity: .55; flex-shrink: 0; }
.wiz-file-chip .rm:hover { opacity: 1; }

/* ── Footer ── */
.wiz-footer {
    padding: 14px 24px 20px;
    background: var(--t-bg-card, #fff);
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top: 1px solid var(--t-border, #e2e8f0);
    gap: 10px;
    flex-shrink: 0;
}
.wiz-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px 20px;
    border-radius: 10px;
    font-size: .84rem;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: all .18s;
    white-space: nowrap;
    min-height: 42px;
    -webkit-tap-highlight-color: transparent;
}
.wiz-btn-primary {
    background: linear-gradient(135deg, #1a1f5e, #2d3482);
    color: #fff;
    box-shadow: 0 3px 12px rgba(26,31,94,.22);
}
.wiz-btn-primary:hover:not(:disabled) { opacity: .9; transform: translateY(-1px); }
.wiz-btn-primary:active:not(:disabled) { transform: scale(.97); }
.wiz-btn-primary:disabled { opacity: .45; cursor: not-allowed; }
.wiz-btn-ghost {
    background: var(--t-bg-hover, #f8fafc);
    color: var(--t-text-secondary, #475569);
    border: 1px solid var(--t-border, #e2e8f0);
}
.wiz-btn-ghost:hover { border-color: #2d3482; color: #2d3482; background: rgba(45,52,130,.04); }

/* ── Dark mode overrides ── */
body[data-bs-theme="dark"] .wiz-cat-card         { background: var(--t-bg-card); }
body[data-bs-theme="dark"] .wiz-cat-card.selected { background: rgba(93,100,200,.15); border-color: #6366f1; }
body[data-bs-theme="dark"] .wiz-cat-card.selected::after { background: #6366f1; }
body[data-bs-theme="dark"] .wiz-cat-name          { color: var(--t-text-primary, #e2e8f0); }
body[data-bs-theme="dark"] .wiz-topic-item.selected { background: rgba(93,100,200,.2); color: #93c5fd; }
body[data-bs-theme="dark"] .wiz-topic-item.selected::before { background: #3b82f6; }
body[data-bs-theme="dark"] .wiz-chip              { background: rgba(93,100,200,.15); color: #93c5fd; border-color: rgba(99,102,241,.3); }
body[data-bs-theme="dark"] .wiz-priority-btn      { background: var(--t-bg-card); }
body[data-bs-theme="dark"] .wiz-priority-btn[data-p="1"].selected { background: rgba(100,116,139,.22); }
body[data-bs-theme="dark"] .wiz-priority-btn[data-p="2"].selected { background: rgba(59,130,246,.22); }
body[data-bs-theme="dark"] .wiz-priority-btn[data-p="3"].selected { background: rgba(245,158,11,.22); }
body[data-bs-theme="dark"] .wiz-priority-btn[data-p="4"].selected { background: rgba(239,68,68,.22); }
body[data-bs-theme="dark"] .wiz-form-control      { background: rgba(255,255,255,.05); color: #e2e8f0; border-color: rgba(255,255,255,.1); }
body[data-bs-theme="dark"] .wiz-form-control:focus { background: rgba(255,255,255,.08); }
body[data-bs-theme="dark"] .wiz-topic-search input { background: rgba(255,255,255,.05); color: #e2e8f0; border-color: rgba(255,255,255,.1); }
body[data-bs-theme="dark"] .wiz-topic-search input:focus { background: rgba(255,255,255,.08); }
body[data-bs-theme="dark"] .wiz-drop-zone         { background: rgba(255,255,255,.03); border-color: rgba(255,255,255,.12); }
body[data-bs-theme="dark"] .wiz-file-chip         { background: rgba(93,100,200,.15); color: #93c5fd; border-color: rgba(99,102,241,.3); }
body[data-bs-theme="dark"] .wiz-btn-ghost         { background: rgba(255,255,255,.05); border-color: rgba(255,255,255,.1); color: #94a3b8; }

/* ── Responsive ── */

/* 480px+: 3-col category grid */
@media (min-width: 480px) {
    .wiz-cat-grid { grid-template-columns: repeat(3, 1fr); gap: 12px; }
}

/* 576px+: wider modal, more padding */
@media (min-width: 576px) {
    .wiz-header  { padding: 26px 32px 0; }
    .wiz-body    { padding: 26px 32px 10px; }
    .wiz-footer  { padding: 16px 32px 24px; }
    .wiz-header-top h5 { font-size: 1.05rem; }
    .wiz-topic-list { max-height: 220px; }
}

/* xs (< 480px): slide up from bottom */
@media (max-width: 479px) {
    #createTicketWizard .modal-dialog {
        margin: 0;
        max-width: 100%;
        align-items: flex-end;
        min-height: 100%;
        display: flex;
    }
    #createTicketWizard .modal-content {
        border-radius: 18px 18px 0 0;
        max-height: 92vh;
        width: 100%;
    }
    .wiz-footer { flex-wrap: wrap; gap: 8px; }
    .wiz-btn { flex: 1; min-width: 110px; }
    .wiz-priority-row { gap: 6px; }
    .wiz-priority-btn { padding: 8px 2px 6px; min-height: 52px; }
    .wiz-priority-btn .p-name { font-size: .65rem; }
}

/* =====================================================
   TICKET PAGE — Theme token overrides
   Replaces hardcoded bg-white / bg-off-white / theme-border
   ===================================================== */
.page-content-wrapper {
    background-color: var(--t-bg-card, #fff) !important;
}
.page-title-box {
    border-bottom-color: var(--t-border, #e2e8f0) !important;
}

/* ── Ticket card ── */
.ticket-item {
    background-color: var(--t-bg-card, #fff) !important;
    border-color: var(--t-border, #e2e8f0) !important;
}
.ticket-item .ticket-item-top-bar {
    border-bottom-color: var(--t-border, #e2e8f0) !important;
}
.ticket-item .attachment-ticket-item-content-box.border-bottom {
    border-bottom-color: var(--t-border, #e2e8f0) !important;
}
.ticket-item h4, .ticket-item h5, .ticket-item h6 {
    color: var(--t-text-primary, #1e293b) !important;
}
.ticket-item p:not(.status-btn) {
    color: var(--t-text-secondary, #475569) !important;
}
.ticket-item .ri-more-2-fill {
    color: var(--t-text-muted, #94a3b8);
}

/* ── Ticket card dropdown ── */
.ticket-item .dropdown-menu {
    background: var(--t-bg-card, #fff);
    border-color: var(--t-border, #e2e8f0);
}
.ticket-item .dropdown-item {
    color: var(--t-text-primary, #1e293b);
}
.ticket-item .dropdown-item:hover,
.ticket-item .dropdown-item:focus {
    background: var(--t-bg-hover, #f8fafc);
    color: var(--t-text-primary, #1e293b);
}

/* ── Ticket search / filter toolbar ── */
.ticket-search-toolbar,
.ticket-toolbar-form .form-control,
.ticket-toolbar-form .form-select {
    background: var(--t-bg-card, #fff);
    border-color: var(--t-border, #e2e8f0);
    color: var(--t-text-primary, #1e293b);
}
.ticket-toolbar-form .form-control::placeholder {
    color: var(--t-text-muted, #94a3b8);
}

/* ── Empty state text ── */
.empty-properties-box h3 {
    color: var(--t-text-primary, #1e293b);
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
                                    <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                    <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="property-top-search-bar">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6 col-lg-6 col-xl-4 mb-25">
                                        <select class="form-select flex-shrink-0 statusSearch">
                                            <option value="" selected>{{ __('Search Status') }}</option>
                                            <option value="1">{{ __('Open') }}</option>
                                            <option value="2">{{ __('Inprogress') }}</option>
                                            <option value="3">{{ __('Close') }}</option>
                                            <option value="4">{{ __('Reopen') }}</option>
                                            <option value="5">{{ __('Re Solved') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-25">
                                        <div class="page-inner-search position-relative">
                                            <input type="text" class="form-control textSearch" placeholder="{{ __('Search') }}">
                                            <span class="ri-search-line"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="property-top-search-bar-right text-end">
                                    <button type="button" class="theme-btn mb-25"
                                        data-bs-toggle="modal" data-bs-target="#createTicketWizard">
                                        <i class="ri-add-line me-1"></i>{{ __('Create Ticket') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tickets-item-wrap">
                        <div class="row" id="ticketAppend">
                            @include('tenant.tickets.single-view')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     CREATE TICKET — 3-Step Wizard Modal
════════════════════════════════════════════════ --}}
<div class="modal fade" id="createTicketWizard" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            {{-- Wizard header with progress steps --}}
            <div class="wiz-header">
                <div class="wiz-header-top">
                    <h5><i class="ri-customer-service-2-line me-2"></i>{{ __('New Support Ticket') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="wiz-steps">
                    <div class="wiz-step-pill active" data-step="1">
                        <div class="wiz-step-dot">1</div>
                        <span class="wiz-step-label">{{ __('Category') }}</span>
                    </div>
                    <div class="wiz-step-pill" data-step="2">
                        <div class="wiz-step-dot">2</div>
                        <span class="wiz-step-label">{{ __('Topic') }}</span>
                    </div>
                    <div class="wiz-step-pill" data-step="3">
                        <div class="wiz-step-dot">3</div>
                        <span class="wiz-step-label">{{ __('Details') }}</span>
                    </div>
                </div>
            </div>

            <form id="wizTicketForm" class="ajax" action="{{ route('tenant.ticket.store') }}"
                method="POST" data-handler="getShowMessage">
                @csrf
                <input type="hidden" name="topic_id" id="wiz_topic_id">
                <input type="hidden" name="priority" id="wiz_priority" value="2">

                <div class="wiz-body">

                    {{-- STEP 1 — Category --}}
                    <div class="wiz-panel active" id="wizStep1">
                        <p class="wiz-step-hint">{{ __('What best describes your issue? Pick a category to continue.') }}</p>
                        <div class="wiz-cat-grid">
                            @php
                                $catMeta = [
                                    'Maintenance & Repairs'  => ['icon' => '🔧', 'label' => 'Maintenance'],
                                    'Utilities'              => ['icon' => '⚡', 'label' => 'Utilities'],
                                    'Payments & Billing'     => ['icon' => '💳', 'label' => 'Payments'],
                                    'Lease & Tenancy'        => ['icon' => '📋', 'label' => 'Lease'],
                                    'Security & Safety'      => ['icon' => '🔒', 'label' => 'Security'],
                                    'Common Areas & Building'=> ['icon' => '🏢', 'label' => 'Common Areas'],
                                    'Noise & Neighbours'     => ['icon' => '🔊', 'label' => 'Noise'],
                                    'Unit Requests'          => ['icon' => '🏠', 'label' => 'Unit'],
                                    'General & Admin'        => ['icon' => 'ℹ️', 'label' => 'General'],
                                ];
                            @endphp
                            @foreach($topicsByCategory as $cat => $catTopics)
                                @php $meta = $catMeta[$cat] ?? ['icon' => '📁', 'label' => $cat]; @endphp
                                <div class="wiz-cat-card" data-cat="{{ $cat }}" tabindex="0" role="button" aria-pressed="false">
                                    <span class="wiz-cat-icon">{{ $meta['icon'] }}</span>
                                    <div class="wiz-cat-name">{{ $meta['label'] }}</div>
                                    <div class="wiz-cat-count">{{ $catTopics->count() }} {{ __('topics') }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- STEP 2 — Topic & Priority --}}
                    <div class="wiz-panel" id="wizStep2">
                        <div class="wiz-chips-row" style="margin-bottom:14px;">
                            <span class="wiz-chip" id="wiz2CatChip"></span>
                        </div>

                        <div class="wiz-section-label">{{ __('Select Topic') }}</div>
                        <div class="wiz-topic-search">
                            <i class="ri-search-line search-icon"></i>
                            <input type="text" id="wizTopicSearch" placeholder="{{ __('Search topics…') }}" autocomplete="off">
                        </div>
                        <div class="wiz-topic-list" id="wizTopicList">
                            @foreach($topicsByCategory as $cat => $catTopics)
                                @foreach($catTopics as $topic)
                                    <div class="wiz-topic-item" data-cat="{{ $cat }}"
                                         data-id="{{ $topic->id }}" data-name="{{ $topic->name }}"
                                         role="option" tabindex="-1">
                                        {{ $topic->name }}
                                    </div>
                                @endforeach
                            @endforeach
                        </div>

                        <div class="wiz-section-label mt-2">{{ __('Priority') }}</div>
                        <div class="wiz-priority-row">
                            <div class="wiz-priority-btn" data-p="1" role="button" tabindex="0">
                                <span class="p-icon">🟢</span>
                                <span class="p-name">{{ __('Low') }}</span>
                            </div>
                            <div class="wiz-priority-btn selected" data-p="2" role="button" tabindex="0">
                                <span class="p-icon">🔵</span>
                                <span class="p-name">{{ __('Medium') }}</span>
                            </div>
                            <div class="wiz-priority-btn" data-p="3" role="button" tabindex="0">
                                <span class="p-icon">🟡</span>
                                <span class="p-name">{{ __('High') }}</span>
                            </div>
                            <div class="wiz-priority-btn" data-p="4" role="button" tabindex="0">
                                <span class="p-icon">🔴</span>
                                <span class="p-name">{{ __('Urgent') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- STEP 3 — Details & Attachments --}}
                    <div class="wiz-panel" id="wizStep3">
                        <div class="wiz-chips-row" id="wiz3Chips"></div>

                        <div class="wiz-field">
                            <label class="wiz-form-label" for="wiz_title">{{ __('Title') }}</label>
                            <input type="text" name="title" id="wiz_title"
                                class="wiz-form-control"
                                placeholder="{{ __('Brief summary of your issue…') }}"
                                maxlength="120" autocomplete="off">
                            <div class="wiz-char-counter"><span id="titleCounter">0</span>/120</div>
                        </div>

                        <div class="wiz-field">
                            <label class="wiz-form-label" for="wiz_details">{{ __('Description') }}</label>
                            <textarea name="details" id="wiz_details"
                                class="wiz-form-control"
                                rows="4"
                                placeholder="{{ __('Describe the issue: location, when it started, and any steps already taken...') }}"
                                maxlength="2000"></textarea>
                            <div class="wiz-char-counter"><span id="detailsCounter">0</span>/2000</div>
                        </div>

                        <div class="wiz-field mb-1">
                            <label class="wiz-form-label">
                                {{ __('Attachments') }}
                                <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--t-text-muted,#94a3b8);font-size:.7rem;">
                                    ({{ __('optional') }})
                                </span>
                            </label>
                            <div class="wiz-drop-zone" id="wizDropZone">
                                <input type="file" name="attachments[]" id="wizFileInput" multiple
                                    accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt">
                                <div class="wiz-drop-icon"><i class="ri-upload-cloud-2-line"></i></div>
                                <div class="wiz-drop-text">{{ __('Drag & drop or') }} <strong>{{ __('browse files') }}</strong></div>
                                <div class="wiz-drop-hint">{{ __('Images, PDF, DOC, XLS · Max 5 files') }}</div>
                            </div>
                            <div class="wiz-file-list" id="wizFileList"></div>
                        </div>
                    </div>

                </div>{{-- /wiz-body --}}

                {{-- Footer navigation --}}
                <div class="wiz-footer">
                    <button type="button" class="wiz-btn wiz-btn-ghost" id="wizBtnBack"
                            style="visibility:hidden;">
                        <i class="ri-arrow-left-line"></i> {{ __('Back') }}
                    </button>
                    <button type="button" class="wiz-btn wiz-btn-primary" id="wizBtnNext" disabled>
                        {{ __('Next') }} <i class="ri-arrow-right-line"></i>
                    </button>
                    <button type="submit" class="wiz-btn wiz-btn-primary" id="wizBtnSubmit"
                            style="display:none;" disabled>
                        <i class="ri-send-plane-line"></i> {{ __('Submit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Ticket Modal (unchanged functionality, light polish) --}}
<div class="modal fade" id="editTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('Edit Ticket') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="ajax" action="{{ route('tenant.ticket.store') }}" method="POST" data-handler="getShowMessage">
                @csrf
                <input type="hidden" class="id" name="id">
                <div class="modal-body">
                    <div class="modal-inner-form-box">
                        <div class="mb-3">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Title') }}</label>
                            <input type="text" class="form-control title" name="title" placeholder="{{ __('Title') }}">
                        </div>
                        <div class="mb-3">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Details') }}</label>
                            <textarea class="form-control details" name="details" rows="4" placeholder="{{ __('Details') }}"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="label-text-title color-heading font-medium mb-2">{{ __('Topic') }}</label>
                            <select class="form-select topic" name="topic_id">
                                <option value="">--{{ __('Select Topic') }}--</option>
                                @foreach ($topics as $topic)
                                    <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-start">
                    <button type="button" class="theme-btn-back me-3" data-bs-dismiss="modal">{{ __('Back') }}</button>
                    <button type="submit" class="theme-btn">{{ __('Update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<input type="hidden" id="getInfoRoute" value="{{ route('tenant.ticket.get.info') }}">
<input type="hidden" id="searchRoute"  value="{{ route('tenant.ticket.search') }}">
@endsection

@push('script')
<script src="{{ asset('assets/js/custom/ticket.js') }}"></script>
<script src="{{ asset('assets/js/custom/ticket-search.js') }}"></script>
<script>
(function () {
    'use strict';

    /* ── State ── */
    var step     = 1;
    var selCat   = null;
    var selTopic = null;   // { id, name }
    var selPri   = 2;

    var catMeta = {
        'Maintenance & Repairs':  { icon: '🔧' },
        'Utilities':              { icon: '⚡' },
        'Payments & Billing':     { icon: '💳' },
        'Lease & Tenancy':        { icon: '📋' },
        'Security & Safety':      { icon: '🔒' },
        'Common Areas & Building':{ icon: '🏢' },
        'Noise & Neighbours':     { icon: '🔊' },
        'Unit Requests':          { icon: '🏠' },
        'General & Admin':        { icon: 'ℹ️' },
    };
    var priMeta = {
        1: { icon: '🟢', name: 'Low' },
        2: { icon: '🔵', name: 'Medium' },
        3: { icon: '🟡', name: 'High' },
        4: { icon: '🔴', name: 'Urgent' },
    };

    /* ── DOM refs ── */
    var btnBack   = document.getElementById('wizBtnBack');
    var btnNext   = document.getElementById('wizBtnNext');
    var btnSubmit = document.getElementById('wizBtnSubmit');
    var fileInput = document.getElementById('wizFileInput');
    var dropZone  = document.getElementById('wizDropZone');
    var fileList  = document.getElementById('wizFileList');
    var selFiles  = [];

    /* ── Step management ── */
    function goStep(n) {
        step = n;

        document.querySelectorAll('.wiz-panel').forEach(function (p, i) {
            p.classList.toggle('active', i + 1 === n);
        });
        document.querySelectorAll('.wiz-step-pill').forEach(function (p) {
            var s = parseInt(p.dataset.step);
            p.classList.toggle('active', s === n);
            p.classList.toggle('done',   s < n);
        });

        /* Back: use visibility so footer width stays stable */
        btnBack.style.visibility = n === 1 ? 'hidden' : 'visible';

        /* Next vs Submit */
        if (n < 3) {
            btnNext.style.display   = '';
            btnSubmit.style.display = 'none';
        } else {
            btnNext.style.display   = 'none';
            btnSubmit.style.display = '';
        }
        validate();
    }

    function validate() {
        if (step === 1) {
            btnNext.disabled = !selCat;
        } else if (step === 2) {
            btnNext.disabled = !selTopic;
        } else {
            var t = document.getElementById('wiz_title').value.trim();
            var d = document.getElementById('wiz_details').value.trim();
            btnSubmit.disabled = t.length < 3 || d.length < 10;
        }
    }

    /* ── Step 3 summary chips ── */
    function refreshChips() {
        var row = document.getElementById('wiz3Chips');
        if (!row) return;
        row.innerHTML = '';
        if (selCat) {
            var ic = (catMeta[selCat] || {}).icon || '📁';
            addChip(row, ic + ' ' + selCat);
        }
        if (selTopic) addChip(row, '🏷 ' + selTopic.name);
        var pm = priMeta[selPri];
        if (pm) addChip(row, pm.icon + ' ' + pm.name);
    }
    function addChip(parent, text) {
        var c = document.createElement('span');
        c.className = 'wiz-chip';
        c.textContent = text;
        parent.appendChild(c);
    }

    /* ── Topic filter ── */
    function filterTopics() {
        var q = document.getElementById('wizTopicSearch').value.toLowerCase().trim();
        var visible = 0;
        document.querySelectorAll('.wiz-topic-item').forEach(function (el) {
            var inCat    = el.dataset.cat === selCat;
            var inSearch = !q || el.dataset.name.toLowerCase().indexOf(q) !== -1;
            var show = inCat && inSearch;
            el.classList.toggle('hidden', !show);
            if (show) visible++;
        });
        /* empty-state */
        var empty = document.getElementById('wizTopicEmpty');
        if (empty) empty.style.display = visible ? 'none' : 'block';
    }

    /* ── Step 1 — Category cards ── */
    document.querySelectorAll('.wiz-cat-card').forEach(function (card) {
        function select() {
            document.querySelectorAll('.wiz-cat-card').forEach(function (c) {
                c.classList.remove('selected');
                c.setAttribute('aria-pressed', 'false');
            });
            card.classList.add('selected');
            card.setAttribute('aria-pressed', 'true');
            selCat   = card.dataset.cat;
            selTopic = null;
            document.querySelectorAll('.wiz-topic-item').forEach(function (el) { el.classList.remove('selected'); });
            document.getElementById('wiz_topic_id').value = '';
            /* update step-2 chip */
            var chip = document.getElementById('wiz2CatChip');
            if (chip) chip.textContent = ((catMeta[selCat] || {}).icon || '📁') + ' ' + selCat;
            validate();
        }
        card.addEventListener('click', select);
        card.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); select(); } });
    });

    /* ── Step 2 — Topic list ── */
    document.querySelectorAll('.wiz-topic-item').forEach(function (item) {
        item.addEventListener('click', function () {
            document.querySelectorAll('.wiz-topic-item').forEach(function (el) { el.classList.remove('selected'); });
            item.classList.add('selected');
            selTopic = { id: item.dataset.id, name: item.dataset.name };
            document.getElementById('wiz_topic_id').value = selTopic.id;
            validate();
        });
    });

    document.getElementById('wizTopicSearch').addEventListener('input', filterTopics);

    /* ── Step 2 — Priority ── */
    document.querySelectorAll('.wiz-priority-btn').forEach(function (btn) {
        function pick() {
            document.querySelectorAll('.wiz-priority-btn').forEach(function (b) { b.classList.remove('selected'); });
            btn.classList.add('selected');
            selPri = parseInt(btn.dataset.p);
            document.getElementById('wiz_priority').value = selPri;
        }
        btn.addEventListener('click', pick);
        btn.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); pick(); } });
    });

    /* ── Step 3 — Character counters ── */
    document.getElementById('wiz_title').addEventListener('input', function () {
        var n = this.value.length;
        document.getElementById('titleCounter').textContent = n;
        this.nextElementSibling.className = 'wiz-char-counter' + (n > 100 ? ' warn' : '');
        validate();
    });
    document.getElementById('wiz_details').addEventListener('input', function () {
        var n = this.value.length;
        document.getElementById('detailsCounter').textContent = n;
        this.nextElementSibling.className = 'wiz-char-counter' + (n > 1800 ? ' warn' : '') + (n > 2000 ? ' over' : '');
        validate();
    });

    /* ── Step 3 — File drop zone ── */
    function renderFiles() {
        fileList.innerHTML = '';
        selFiles.forEach(function (f, i) {
            var chip = document.createElement('span');
            chip.className = 'wiz-file-chip';
            chip.innerHTML = '<i class="ri-file-line"></i><span title="' + f.name + '">' + f.name + '</span>'
                + '<span class="rm" data-i="' + i + '" title="Remove">×</span>';
            fileList.appendChild(chip);
        });
        /* sync FileList */
        try {
            var dt = new DataTransfer();
            selFiles.forEach(function (f) { dt.items.add(f); });
            fileInput.files = dt.files;
        } catch(e) {}
        fileList.querySelectorAll('.rm').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                selFiles.splice(parseInt(this.dataset.i), 1);
                renderFiles();
            });
        });
    }

    fileInput.addEventListener('change', function () {
        Array.from(this.files).forEach(function (f) { if (selFiles.length < 5) selFiles.push(f); });
        renderFiles();
    });
    ['dragover','dragenter'].forEach(function (ev) {
        dropZone.addEventListener(ev, function (e) { e.preventDefault(); dropZone.classList.add('drag-over'); });
    });
    ['dragleave','drop'].forEach(function (ev) {
        dropZone.addEventListener(ev, function (e) {
            e.preventDefault(); dropZone.classList.remove('drag-over');
            if (ev === 'drop') {
                Array.from(e.dataTransfer.files).forEach(function (f) { if (selFiles.length < 5) selFiles.push(f); });
                renderFiles();
            }
        });
    });

    /* ── Navigation ── */
    btnNext.addEventListener('click', function () {
        if (step === 1) filterTopics();        /* pre-filter for new category */
        if (step === 2) refreshChips();
        goStep(step + 1);
    });
    btnBack.addEventListener('click', function () { goStep(step - 1); });

    /* ── Reset on modal close ── */
    document.getElementById('createTicketWizard').addEventListener('hidden.bs.modal', function () {
        selCat  = null; selTopic = null; selPri = 2;
        document.querySelectorAll('.wiz-cat-card').forEach(function (c) {
            c.classList.remove('selected'); c.setAttribute('aria-pressed', 'false');
        });
        document.querySelectorAll('.wiz-topic-item').forEach(function (el) { el.classList.remove('selected', 'hidden'); });
        document.querySelectorAll('.wiz-priority-btn').forEach(function (b) { b.classList.toggle('selected', b.dataset.p === '2'); });
        document.getElementById('wiz_topic_id').value  = '';
        document.getElementById('wiz_priority').value  = '2';
        document.getElementById('wiz_title').value     = '';
        document.getElementById('wiz_details').value   = '';
        document.getElementById('titleCounter').textContent   = '0';
        document.getElementById('detailsCounter').textContent = '0';
        document.getElementById('wizTopicSearch').value = '';
        selFiles = []; fileList.innerHTML = '';
        var chip2 = document.getElementById('wiz2CatChip'); if (chip2) chip2.textContent = '';
        var chips3 = document.getElementById('wiz3Chips'); if (chips3) chips3.innerHTML = '';
        goStep(1);
    });

    /* ── Init ── */
    goStep(1);
})();
</script>
@endpush
