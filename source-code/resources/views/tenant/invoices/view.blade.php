{{-- ============================================================
     INVOICE DETAIL MODAL — Theme-aware (Light + Dark)
     ============================================================ --}}

@php
    $isPaid    = $invoice->status == INVOICE_STATUS_PAID;
    $isOverdue = !$isPaid && $invoice->due_date < date('Y-m-d');
    $subtotal  = $items->sum('amount');
    $totalTax  = $items->sum('tax_amount');
    $lateFee   = $invoice->late_fee ?? 0;
    $grandTotal = $invoice->amount + $lateFee;
@endphp

{{-- Modal Header (always deep navy — brand anchor) --}}
<div class="inv-modal-hdr">
    <div class="inv-modal-hdr-left">
        <span class="inv-modal-label">{{ __('Invoice') }}</span>
        <h4 class="inv-modal-invno">{{ $invoice->invoice_no }}</h4>
        <span class="inv-modal-date">{{ $invoice->created_at->format('d F Y') }}</span>
    </div>
    <div class="inv-modal-hdr-right">
        <span class="inv-modal-status inv-mstatus--{{ $isPaid ? 'paid' : ($isOverdue ? 'overdue' : 'pending') }}">
            @if($isPaid)    <i class="ri-check-double-line"></i> {{ __('Paid') }}
            @elseif($isOverdue) <i class="ri-alarm-warning-line"></i> {{ __('Overdue') }}
            @else           <i class="ri-time-line"></i> {{ __('Pending') }}
            @endif
        </span>
        <div class="inv-modal-hdr-actions">
            <a href="{{ route('tenant.invoice.print', $invoice->id) }}"
               target="_blank" class="inv-hdr-btn inv-hdr-btn--print">
                <i class="ri-printer-line"></i> {{ __('Print') }}
            </a>
            @if(!$isPaid)
            <a href="{{ route('tenant.invoice.pay', $invoice->id) }}"
               class="inv-hdr-btn inv-hdr-btn--pay">
                <i class="ri-bank-card-line"></i> {{ __('Pay Now') }}
            </a>
            @endif
            <button type="button" class="inv-hdr-close" data-bs-dismiss="modal">
                <i class="ri-close-line"></i>
            </button>
        </div>
    </div>
</div>

{{-- Modal Body --}}
<div class="inv-modal-body">

    {{-- Logo + Parties --}}
    <div class="inv-logo-block">
        @if ($owner->print_name)
            <img src="{{ assetUrl($owner->folder_name . '/' . $owner->file_name) }}" alt="Logo" class="inv-company-logo">
        @else
            <img src="{{ getSettingImage('app_logo') }}" alt="Logo" class="inv-company-logo">
        @endif
    </div>

    <div class="inv-parties-cols">
        <div class="inv-party-block">
            <div class="inv-party-label"><i class="ri-user-line"></i> {{ __('Invoice To') }}</div>
            <div class="inv-party-name">{{ $tenant->first_name }} {{ $tenant->last_name }}</div>
            <div class="inv-party-detail">{{ $tenant->email }}</div>
            <div class="inv-party-detail">{{ $tenant->property_name }}</div>
            <div class="inv-party-detail">{{ $tenant->unit_name }}</div>
        </div>

        <div class="inv-party-block">
            <div class="inv-party-label"><i class="ri-building-2-line"></i> {{ __('Pay To') }}</div>
            @if ($owner->print_name)
                <div class="inv-party-name">{{ $owner->print_name }}</div>
                <div class="inv-party-detail">{{ $owner->print_address }}</div>
                <div class="inv-party-detail">{{ $owner->print_contact }}</div>
            @else
                <div class="inv-party-name">{{ getOption('app_name') }}</div>
                <div class="inv-party-detail">{{ getOption('app_location') }}</div>
                <div class="inv-party-detail">{{ getOption('app_contact_number') }}</div>
            @endif
        </div>

        <div class="inv-party-block">
            <div class="inv-party-label"><i class="ri-file-info-line"></i> {{ __('Details') }}</div>
            <div class="inv-meta-row"><span>{{ __('Period') }}</span><strong>{{ $invoice->month }}</strong></div>
            <div class="inv-meta-row"><span>{{ __('Issue Date') }}</span><strong>{{ $invoice->created_at->format('d M Y') }}</strong></div>
            <div class="inv-meta-row">
                <span>{{ __('Due Date') }}</span>
                <strong class="{{ $isOverdue ? 'inv-text-danger' : '' }}">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</strong>
            </div>
        </div>
    </div>

    <div class="inv-divider"></div>

    {{-- Items --}}
    <div class="inv-section-label"><i class="ri-list-check-2"></i> {{ __('Invoice Items') }}</div>
    <div class="inv-items-table-wrap">
        <table class="inv-items-table">
            <thead>
                <tr>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th class="text-end">{{ __('Amount') }}</th>
                    <th class="text-end">{{ __('Tax') }}</th>
                    <th class="text-end">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                <tr>
                    <td><span class="inv-type-pill">{{ $item->invoiceType?->name ?? '—' }}</span></td>
                    <td class="inv-item-desc">{{ $item->description }}</td>
                    <td class="inv-item-date">{{ $item->created_at->format('d M Y') }}</td>
                    <td class="text-end">{{ currencyPrice($item->amount) }}</td>
                    <td class="text-end">
                        @if($item->tax_amount > 0)
                            <span class="inv-tax-chip">{{ currencyPrice($item->tax_amount) }}</span>
                        @else
                            <span class="inv-text-faint">—</span>
                        @endif
                    </td>
                    <td class="text-end inv-fw-semi">{{ currencyPrice($item->amount + $item->tax_amount) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Totals --}}
    <div class="inv-totals-wrap">
        <div class="inv-totals-box">
            <div class="inv-total-row">
                <span>{{ __('Subtotal') }}</span><span>{{ currencyPrice($subtotal) }}</span>
            </div>
            @if($totalTax > 0)
            <div class="inv-total-row">
                <span>{{ __('Tax') }}</span><span>{{ currencyPrice($totalTax) }}</span>
            </div>
            @endif
            @if($lateFee > 0)
            <div class="inv-total-row inv-total-fee">
                <span><i class="ri-error-warning-line me-1"></i>{{ __('Late Fee') }}</span>
                <span class="inv-text-danger">{{ currencyPrice($lateFee) }}</span>
            </div>
            @endif
            <div class="inv-total-row inv-total-grand">
                <span>{{ __('Total Due') }}</span><span>{{ currencyPrice($grandTotal) }}</span>
            </div>
        </div>
    </div>

    {{-- Transaction (paid) --}}
    @if($isPaid)
    <div class="inv-divider"></div>
    <div class="inv-section-label"><i class="ri-shield-check-line"></i> {{ __('Payment Confirmed') }}</div>
    @isset($order)
    <div class="inv-txn-card">
        <div class="inv-txn-icon"><i class="ri-checkbox-circle-fill"></i></div>
        <div class="inv-txn-details">
            <div class="inv-txn-row"><span>{{ __('Gateway') }}</span><strong>{{ $order?->gatewayTitle ?? __('Cash') }}</strong></div>
            <div class="inv-txn-row"><span>{{ __('Transaction ID') }}</span><strong class="inv-txn-id">{{ $order?->payment_id ?: $order?->transaction_id }}</strong></div>
            <div class="inv-txn-row"><span>{{ __('Date') }}</span><strong>{{ $order?->created_at->format('d M Y, H:i') }}</strong></div>
            <div class="inv-txn-row"><span>{{ __('Amount Paid') }}</span><strong class="inv-text-green">{{ currencyPrice($order?->total) }}</strong></div>
        </div>
    </div>
    @endisset
    @endif

    <div class="inv-modal-footer-note">
        <i class="ri-information-line me-1"></i>
        {{ __('For billing queries, please contact your property manager.') }}
    </div>

</div>

<style>
/* =====================================================
   INVOICE MODAL — Theme-aware (Light + Dark)
   ===================================================== */

/* ── Header (brand-fixed navy, same in both themes) ── */
.inv-modal-hdr {
    background: linear-gradient(135deg, #1a1f5e 0%, #2d3482 100%);
    padding: 22px 28px 20px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    flex-wrap: wrap;
}
.inv-modal-label {
    display: block;
    font-size: .65rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: rgba(255,255,255,.5);
    margin-bottom: 2px;
}
.inv-modal-invno {
    color: #fff;
    font-size: 1.25rem;
    font-weight: 800;
    margin: 0 0 4px;
    letter-spacing: -.3px;
}
.inv-modal-date { color: rgba(255,255,255,.55); font-size: .8rem; }
.inv-modal-hdr-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

.inv-modal-status {
    font-size: .78rem;
    font-weight: 700;
    padding: 5px 13px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.inv-mstatus--paid    { background: rgba(52,211,153,.22); color: #34d399; border: 1px solid rgba(52,211,153,.3); }
.inv-mstatus--pending { background: rgba(251,191,36,.22); color: #fbbf24; border: 1px solid rgba(251,191,36,.3); }
.inv-mstatus--overdue { background: rgba(248,113,113,.22); color: #f87171; border: 1px solid rgba(248,113,113,.3); }

.inv-hdr-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 16px;
    border-radius: 8px;
    font-size: .8rem;
    font-weight: 600;
    cursor: pointer;
    border: none;
    text-decoration: none;
    transition: all .18s;
}
.inv-hdr-btn--print {
    background: rgba(255,255,255,.12);
    color: rgba(255,255,255,.9);
    border: 1px solid rgba(255,255,255,.2);
}
.inv-hdr-btn--print:hover { background: rgba(255,255,255,.22); color: #fff; }
.inv-hdr-btn--pay {
    background: #10b981;
    color: #fff;
    box-shadow: 0 3px 10px rgba(16,185,129,.35);
}
.inv-hdr-btn--pay:hover { background: #059669; color: #fff; }
.inv-hdr-close {
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    color: rgba(255,255,255,.7);
    width: 34px; height: 34px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    font-size: 1.1rem;
    transition: all .15s;
}
.inv-hdr-close:hover { background: rgba(255,255,255,.22); color: #fff; }

/* ── Body ── */
.inv-modal-body {
    padding: 24px 28px 20px;
    background: var(--t-bg-card);
}
.inv-logo-block { margin-bottom: 18px; }
.inv-company-logo { max-height: 46px; max-width: 160px; object-fit: contain; }

.inv-parties-cols {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 20px;
}
@media (max-width: 580px) { .inv-parties-cols { grid-template-columns: 1fr; } }

.inv-party-label {
    font-size: .68rem;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: var(--t-text-muted);
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.inv-party-name  { font-size: .92rem; font-weight: 700; color: var(--t-text-primary); margin-bottom: 3px; }
.inv-party-detail { font-size: .8rem; color: var(--t-text-secondary); line-height: 1.55; }

.inv-meta-row {
    display: flex;
    justify-content: space-between;
    font-size: .8rem;
    color: var(--t-text-secondary);
    padding: 4px 0;
    border-bottom: 1px solid var(--t-border);
}
.inv-meta-row:last-child { border-bottom: none; }
.inv-meta-row strong { color: var(--t-text-primary); }

.inv-divider { height: 1px; background: var(--t-border); margin: 20px 0; }

.inv-section-label {
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: var(--t-text-muted);
    font-weight: 700;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* ── Items table ── */
.inv-items-table-wrap {
    border: 1px solid var(--t-border);
    border-radius: var(--t-radius-md, 12px);
    overflow: hidden;
    margin-bottom: 16px;
}
.inv-items-table { width: 100%; border-collapse: collapse; font-size: .83rem; }
.inv-items-table thead tr { background: var(--t-bg-hover); }
.inv-items-table thead th {
    padding: 10px 14px;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--t-text-muted);
    font-weight: 700;
    border-bottom: 1px solid var(--t-border);
    text-align: left;
}
.inv-items-table thead th.text-end { text-align: right; }
.inv-items-table tbody tr { border-bottom: 1px solid var(--t-border); transition: background .1s; }
.inv-items-table tbody tr:hover { background: var(--t-bg-hover); }
.inv-items-table tbody tr:last-child { border-bottom: none; }
.inv-items-table td { padding: 11px 14px; color: var(--t-text-secondary); vertical-align: middle; }
.inv-items-table td.text-end { text-align: right; }
.inv-item-desc { color: var(--t-text-primary); font-weight: 500; }
.inv-item-date { color: var(--t-text-muted); font-size: .78rem; }
.inv-fw-semi   { font-weight: 600; color: var(--t-text-primary); }

/* Type / Tax chips — light default */
.inv-type-pill {
    background: var(--inv-type-bg,  #eff6ff);
    color:      var(--inv-type-fg,  #1d4ed8);
    font-size: .7rem; font-weight: 700;
    padding: 3px 9px; border-radius: 20px; white-space: nowrap;
}
.inv-tax-chip {
    background: var(--inv-tax-bg,  #fef3c7);
    color:      var(--inv-tax-fg,  #92400e);
    font-size: .72rem; padding: 2px 7px; border-radius: 10px;
}

/* ── Totals ── */
.inv-totals-wrap { display: flex; justify-content: flex-end; margin-bottom: 4px; }
.inv-totals-box {
    min-width: 280px;
    border: 1px solid var(--t-border);
    border-radius: var(--t-radius-md, 12px);
    overflow: hidden;
}
.inv-total-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 16px;
    font-size: .85rem;
    color: var(--t-text-secondary);
    border-bottom: 1px solid var(--t-border);
}
.inv-total-row:last-child { border-bottom: none; }
.inv-total-row span:first-child { color: var(--t-text-muted); }
.inv-total-row span:last-child  { font-weight: 600; color: var(--t-text-primary); }

.inv-total-fee {
    background: var(--inv-fee-bg, rgba(239,68,68,.06));
}
.inv-total-grand {
    background: linear-gradient(135deg, #1a1f5e, #2d3482) !important;
    color: #fff !important;
    font-size: .95rem;
    font-weight: 700;
}
.inv-total-grand span { color: #fff !important; }

/* ── Transaction card ── */
.inv-txn-card {
    background: var(--inv-txn-bg, #f0fdf4);
    border: 1px solid var(--inv-txn-border, #bbf7d0);
    border-radius: var(--t-radius-md, 12px);
    padding: 16px 18px;
    display: flex;
    gap: 14px;
    align-items: flex-start;
    margin-bottom: 4px;
}
.inv-txn-icon { color: #10b981; font-size: 1.6rem; margin-top: 2px; flex-shrink: 0; }
.inv-txn-details { flex: 1; }
.inv-txn-row {
    display: flex;
    justify-content: space-between;
    font-size: .82rem;
    padding: 4px 0;
    border-bottom: 1px solid var(--inv-txn-divider, rgba(16,185,129,.12));
    color: var(--t-text-primary);
}
.inv-txn-row:last-child { border-bottom: none; }
.inv-txn-row span { color: var(--t-text-muted); }
.inv-txn-id { font-family: 'Courier New', monospace; font-size: .78rem; }

/* Semantic colour helpers */
.inv-text-danger { color: var(--t-accent-red,  #ef4444) !important; }
.inv-text-green  { color: var(--t-accent-green, #10b981) !important; }
.inv-text-faint  { color: var(--t-text-faint) !important; }

/* Footer note */
.inv-modal-footer-note {
    text-align: center;
    font-size: .75rem;
    color: var(--t-text-muted);
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid var(--t-border);
}

/* =====================================================
   DARK MODE OVERRIDES (modal body-scoped)
   ===================================================== */
body[data-bs-theme="dark"] .inv-modal-body {
    background: var(--t-bg-card);
}

/* Chip/pill colours in dark */
body[data-bs-theme="dark"] {
    --inv-type-bg:    rgba(59,130,246,.18);
    --inv-type-fg:    #93c5fd;
    --inv-tax-bg:     rgba(245,158,11,.18);
    --inv-tax-fg:     #fbbf24;
    --inv-fee-bg:     rgba(239,68,68,.10);
    --inv-txn-bg:     rgba(16,185,129,.10);
    --inv-txn-border: rgba(16,185,129,.25);
    --inv-txn-divider:rgba(16,185,129,.12);
}
</style>
