@extends('tenant.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="ri-checkbox-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="ri-error-warning-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @php
                $pendingCount  = $invoices->where('status', INVOICE_STATUS_PENDING)->count();
                $paidCount     = $invoices->where('status', INVOICE_STATUS_PAID)->count();
                $overdueCount  = $invoices->where('status', INVOICE_STATUS_PENDING)->filter(fn($i) => $i->due_date < date('Y-m-d'))->count();
                $pendingAmount = $invoices->where('status', INVOICE_STATUS_PENDING)->sum('amount');
                $paidAmount    = $invoices->where('status', INVOICE_STATUS_PAID)->sum('amount');
            @endphp

            {{-- Hero Stats Banner --}}
            <div class="inv-hero-banner mb-4">
                <div class="inv-hero-inner">
                    <div class="inv-hero-text">
                        <h2 class="inv-hero-title">{{ __('My Invoices') }}</h2>
                        <p class="inv-hero-sub">{{ __('Track, view, and pay your rent invoices from one place.') }}</p>
                    </div>
                    <div class="inv-stats-row">
                        <div class="inv-stat-pill inv-stat-pending">
                            <div class="inv-stat-icon"><i class="ri-time-line"></i></div>
                            <div class="inv-stat-body">
                                <span class="inv-stat-label">{{ __('Pending') }}</span>
                                <span class="inv-stat-val">{{ $pendingCount }}</span>
                                <span class="inv-stat-sub">{{ currencyPrice($pendingAmount) }}</span>
                            </div>
                        </div>
                        <div class="inv-stat-pill inv-stat-paid">
                            <div class="inv-stat-icon"><i class="ri-checkbox-circle-line"></i></div>
                            <div class="inv-stat-body">
                                <span class="inv-stat-label">{{ __('Paid') }}</span>
                                <span class="inv-stat-val">{{ $paidCount }}</span>
                                <span class="inv-stat-sub">{{ currencyPrice($paidAmount) }}</span>
                            </div>
                        </div>
                        @if($overdueCount > 0)
                        <div class="inv-stat-pill inv-stat-overdue">
                            <div class="inv-stat-icon"><i class="ri-alarm-warning-line"></i></div>
                            <div class="inv-stat-body">
                                <span class="inv-stat-label">{{ __('Overdue') }}</span>
                                <span class="inv-stat-val">{{ $overdueCount }}</span>
                                <span class="inv-stat-sub">{{ __('Action needed') }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Invoice Cards --}}
            <div class="inv-cards-grid">
                @forelse($invoices as $invoice)
                    @php
                        $isOverdue = $invoice->status == INVOICE_STATUS_PENDING && $invoice->due_date < date('Y-m-d');
                        $isPaid    = $invoice->status == INVOICE_STATUS_PAID;
                        $statusClass = $isPaid ? 'paid' : ($isOverdue ? 'overdue' : 'pending');
                    @endphp
                    <div class="inv-card inv-card--{{ $statusClass }}">
                        <div class="inv-card-strip"></div>
                        <div class="inv-card-body">
                            <div class="inv-card-head">
                                <div class="inv-card-no">
                                    <span class="inv-card-label">{{ __('Invoice') }}</span>
                                    <span class="inv-card-number">{{ $invoice->invoice_no }}</span>
                                </div>
                                <div class="inv-card-badge inv-badge--{{ $statusClass }}">
                                    @if($isPaid)
                                        <i class="ri-check-line"></i> {{ __('Paid') }}
                                    @elseif($isOverdue)
                                        <i class="ri-alarm-warning-line"></i> {{ __('Overdue') }}
                                    @else
                                        <i class="ri-time-line"></i> {{ __('Pending') }}
                                    @endif
                                </div>
                            </div>

                            <div class="inv-card-desc">{{ $invoice->name }}</div>

                            <div class="inv-card-meta">
                                <div class="inv-meta-item">
                                    <i class="ri-calendar-line"></i>
                                    <span>{{ __('Issued') }} {{ $invoice->created_at->format('d M Y') }}</span>
                                </div>
                                <div class="inv-meta-item {{ $isOverdue ? 'inv-meta-overdue' : '' }}">
                                    <i class="ri-calendar-close-line"></i>
                                    <span>{{ __('Due') }} {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</span>
                                </div>
                            </div>

                            <div class="inv-card-amount-row">
                                <div class="inv-card-amount">
                                    {{ currencyPrice($invoice->amount) }}
                                    @if($isOverdue && $invoice->late_fee > 0)
                                        <span class="inv-late-fee">+ {{ currencyPrice($invoice->late_fee) }} {{ __('late fee') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="inv-card-actions">
                                <button type="button"
                                    onclick="getEditModal('{{ route('tenant.invoice.view', $invoice->id) }}', '#viewModal')"
                                    class="inv-btn inv-btn-ghost">
                                    <i class="ri-eye-line"></i> {{ __('View') }}
                                </button>
                                <a href="{{ route('tenant.invoice.print', $invoice->id) }}"
                                    target="_blank" class="inv-btn inv-btn-ghost">
                                    <i class="ri-printer-line"></i> {{ __('Print') }}
                                </a>
                                @if($invoice->status == INVOICE_STATUS_PENDING)
                                    <a href="{{ route('tenant.invoice.pay', $invoice->id) }}"
                                        class="inv-btn inv-btn-pay">
                                        <i class="ri-bank-card-line"></i> {{ __('Pay Now') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="inv-empty-state">
                        <div class="inv-empty-icon"><i class="ri-file-list-3-line"></i></div>
                        <h4>{{ __('No invoices yet') }}</h4>
                        <p>{{ __('Your invoices will appear here once they are generated.') }}</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</div>

{{-- View Invoice Modal --}}
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content inv-modal-content border-0"></div>
    </div>
</div>
@endsection

@push('style')
<style>
/* =====================================================
   INVOICE PAGE — Theme-aware (Light + Dark)
   ===================================================== */

/* ── Hero Banner ── */
.inv-hero-banner {
    background: linear-gradient(135deg, #1a1f5e 0%, #2d3482 55%, #1e3a8a 100%);
    border-radius: var(--t-radius-xl, 20px);
    padding: 32px 36px;
    box-shadow: 0 8px 32px rgba(26,31,94,0.28);
    overflow: hidden;
    position: relative;
}
.inv-hero-banner::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 220px; height: 220px;
    background: rgba(255,255,255,0.04);
    border-radius: 50%;
    pointer-events: none;
}
.inv-hero-banner::after {
    content: '';
    position: absolute;
    bottom: -40px; left: 40%;
    width: 160px; height: 160px;
    background: rgba(255,255,255,0.03);
    border-radius: 50%;
    pointer-events: none;
}
.inv-hero-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 24px;
    position: relative;
    z-index: 1;
}
.inv-hero-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #fff;
    margin: 0 0 6px;
    letter-spacing: -0.3px;
}
.inv-hero-sub { color: rgba(255,255,255,0.65); margin: 0; font-size: 0.9rem; }

.inv-stats-row { display: flex; gap: 12px; flex-wrap: wrap; }

.inv-stat-pill {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: var(--t-radius-md, 14px);
    padding: 12px 18px;
    min-width: 140px;
    transition: background 0.2s;
}
.inv-stat-pill:hover { background: rgba(255,255,255,0.16); }

.inv-stat-icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
}
.inv-stat-pending .inv-stat-icon { background: rgba(251,191,36,.22);  color: #fbbf24; }
.inv-stat-paid    .inv-stat-icon { background: rgba(52,211,153,.22);  color: #34d399; }
.inv-stat-overdue .inv-stat-icon { background: rgba(248,113,113,.22); color: #f87171; }

.inv-stat-body { display: flex; flex-direction: column; gap: 1px; }
.inv-stat-label { color: rgba(255,255,255,.55); font-size: .68rem; text-transform: uppercase; letter-spacing: .5px; }
.inv-stat-val   { color: #fff; font-size: 1.25rem; font-weight: 700; line-height: 1.1; }
.inv-stat-sub   { color: rgba(255,255,255,.6); font-size: .75rem; }

/* ── Cards Grid ── */
.inv-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 20px;
}

/* ── Invoice Card ── */
.inv-card {
    background: var(--t-bg-card);
    border: 1px solid var(--t-border);
    border-radius: var(--t-radius-lg, 16px);
    box-shadow: var(--t-shadow-sm);
    overflow: hidden;
    transition: transform .2s, box-shadow .2s, border-color .2s;
}
.inv-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--t-shadow-md);
    border-color: var(--t-border-hover);
}

/* Colored top strip */
.inv-card-strip { height: 4px; }
.inv-card--paid    .inv-card-strip { background: linear-gradient(90deg, #10b981, #34d399); }
.inv-card--pending .inv-card-strip { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.inv-card--overdue .inv-card-strip { background: linear-gradient(90deg, #ef4444, #f87171); }

.inv-card-body { padding: 22px 24px 20px; }

/* Head */
.inv-card-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}
.inv-card-label {
    display: block;
    font-size: .68rem;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: var(--t-text-muted);
    margin-bottom: 2px;
}
.inv-card-number {
    display: block;
    font-size: 1rem;
    font-weight: 700;
    color: var(--t-text-primary);
    letter-spacing: .3px;
}

/* Status badges */
.inv-card-badge {
    font-size: .72rem;
    font-weight: 600;
    padding: 4px 11px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}
.inv-badge--paid    { background: var(--inv-badge-paid-bg,    #dcfce7); color: var(--inv-badge-paid-fg,    #15803d); }
.inv-badge--pending { background: var(--inv-badge-pend-bg,    #fef9c3); color: var(--inv-badge-pend-fg,    #92400e); }
.inv-badge--overdue { background: var(--inv-badge-over-bg,    #fee2e2); color: var(--inv-badge-over-fg,    #b91c1c); }

/* Description */
.inv-card-desc {
    font-size: .88rem;
    color: var(--t-text-secondary);
    margin-bottom: 14px;
    line-height: 1.45;
}

/* Meta */
.inv-card-meta {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
    margin-bottom: 16px;
}
.inv-meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: .78rem;
    color: var(--t-text-muted);
}
.inv-meta-item i { font-size: .82rem; }
.inv-meta-overdue { color: var(--t-accent-red, #ef4444) !important; }

/* Amount */
.inv-card-amount-row {
    background: var(--t-bg-hover);
    border: 1px solid var(--t-border);
    border-radius: var(--t-radius-sm, 10px);
    padding: 12px 14px;
    margin-bottom: 18px;
}
.inv-card-amount {
    font-size: 1.45rem;
    font-weight: 800;
    color: var(--t-text-primary);
    letter-spacing: -.5px;
    display: flex;
    align-items: baseline;
    gap: 8px;
}
.inv-late-fee { font-size: .75rem; font-weight: 500; color: var(--t-accent-red, #ef4444); }

/* Actions */
.inv-card-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }

.inv-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 7px 14px;
    border-radius: var(--t-radius-sm, 8px);
    font-size: .8rem;
    font-weight: 600;
    cursor: pointer;
    border: none;
    text-decoration: none;
    transition: all .18s;
    white-space: nowrap;
}
.inv-btn-ghost {
    background: var(--t-bg-hover);
    color: var(--t-text-secondary);
    border: 1px solid var(--t-border);
}
.inv-btn-ghost:hover {
    background: var(--t-bg-active);
    color: var(--t-text-primary);
    border-color: var(--t-border-hover);
}
.inv-btn-pay {
    background: linear-gradient(135deg, #1a1f5e, #2d3482);
    color: #fff !important;
    margin-left: auto;
    box-shadow: 0 3px 10px rgba(26,31,94,.25);
    border: none;
}
.inv-btn-pay:hover {
    background: linear-gradient(135deg, #2d3482, #3949ab);
    color: #fff;
    box-shadow: 0 5px 16px rgba(26,31,94,.38);
    transform: translateY(-1px);
}

/* Empty state */
.inv-empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 80px 20px;
    background: var(--t-bg-card);
    border: 2px dashed var(--t-border);
    border-radius: var(--t-radius-lg, 16px);
}
.inv-empty-icon { font-size: 3.5rem; color: var(--t-text-faint); margin-bottom: 16px; }
.inv-empty-state h4 { color: var(--t-text-primary); font-weight: 700; margin-bottom: 8px; }
.inv-empty-state p  { color: var(--t-text-muted); font-size: .9rem; }

/* Modal shell */
.inv-modal-content { overflow: hidden; }

/* =====================================================
   DARK MODE OVERRIDES
   ===================================================== */
body[data-bs-theme="dark"] {
    /* Status badge colors adjusted for dark backgrounds */
    --inv-badge-paid-bg:  rgba(16,185,129,.18);
    --inv-badge-paid-fg:  #34d399;
    --inv-badge-pend-bg:  rgba(245,158,11,.18);
    --inv-badge-pend-fg:  #fbbf24;
    --inv-badge-over-bg:  rgba(239,68,68,.18);
    --inv-badge-over-fg:  #f87171;
}

body[data-bs-theme="dark"] .inv-card {
    box-shadow: 0 2px 16px rgba(0,0,0,.45);
}
body[data-bs-theme="dark"] .inv-card:hover {
    box-shadow: 0 8px 32px rgba(0,0,0,.55);
}

/* ── Responsive ── */
@media (max-width: 768px) {
    .inv-hero-inner { flex-direction: column; align-items: flex-start; }
    .inv-stats-row  { width: 100%; }
    .inv-stat-pill  { flex: 1; min-width: 120px; }
    .inv-cards-grid { grid-template-columns: 1fr; }
    .inv-hero-title { font-size: 1.35rem; }
}
</style>
@endpush

@push('script')
<script src="{{ asset('/') }}assets/js/pages/alldatatables.init.js"></script>
@endpush
