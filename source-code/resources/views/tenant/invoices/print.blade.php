<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Invoice') }} {{ $invoice->invoice_no }}</title>
    @include('common.layouts.style')

    <style>
        /* =====================================================
           PRINT INVOICE — Always light theme
           ===================================================== */
        :root {
            /* Fallback values for pages that load before theme.css */
            --t-bg-base:    #f0f4f8;
            --t-bg-card:    #ffffff;
            --t-bg-hover:   #f8fafc;
            --t-border:     #e2e8f0;
            --t-text-primary:   #1e293b;
            --t-text-secondary: #475569;
            --t-text-muted:     #94a3b8;
            --t-text-faint:     #cbd5e1;
            --t-shadow-md:  0 4px 16px rgba(0,0,0,.10);
            --t-radius-lg:  16px;
            --t-radius-md:  12px;
            --t-radius-sm:  8px;
            --t-accent-red:   #ef4444;
            --t-accent-green: #10b981;
            /* chip colours */
            --inv-type-bg: #eff6ff; --inv-type-fg: #1d4ed8;
            --inv-tax-bg:  #fef3c7; --inv-tax-fg:  #92400e;
            --inv-fee-bg:  rgba(239,68,68,.06);
            --inv-txn-bg:  #f0fdf4; --inv-txn-border: #bbf7d0;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--t-bg-base);
            margin: 0;
            padding: 30px 20px;
            color: var(--t-text-primary);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            transition: background-color .22s ease, color .22s ease;
        }

        .inv-print-page {
            max-width: 820px;
            margin: 0 auto;
            background: var(--t-bg-card);
            border: 1px solid var(--t-border);
            border-radius: var(--t-radius-lg);
            box-shadow: var(--t-shadow-md);
            overflow: hidden;
        }

        /* ── Action bar (screen-only) ── */
        .inv-print-action-bar {
            background: var(--t-bg-hover);
            border-bottom: 1px solid var(--t-border);
            padding: 13px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        .inv-print-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 20px;
            border-radius: var(--t-radius-sm);
            font-size: .84rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all .18s;
        }
        .inv-action-print {
            background: linear-gradient(135deg, #1a1f5e, #2d3482);
            color: #fff;
            box-shadow: 0 3px 10px rgba(26,31,94,.22);
        }
        .inv-action-print:hover { opacity: .9; }
        .inv-action-back {
            background: var(--t-bg-hover);
            border: 1px solid var(--t-border);
            color: var(--t-text-secondary);
        }
        .inv-action-back:hover { border-color: var(--t-border-hover); color: var(--t-text-primary); }

        /* ── Header (brand navy — same in both themes) ── */
        .inv-print-header {
            background: linear-gradient(135deg, #1a1f5e 0%, #2d3482 100%);
            padding: 36px 44px 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .inv-print-logo img {
            max-height: 52px;
            max-width: 180px;
            object-fit: contain;
            filter: brightness(0) invert(1);
            opacity: .9;
        }
        .inv-print-hdr-right { text-align: right; }
        .inv-print-inv-label {
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: rgba(255,255,255,.5);
            margin-bottom: 4px;
        }
        .inv-print-inv-no {
            font-size: 1.7rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -.5px;
            margin: 0 0 8px;
        }
        .inv-print-status {
            display: inline-block;
            padding: 5px 16px;
            border-radius: 20px;
            font-size: .8rem;
            font-weight: 700;
        }
        .inv-status-paid    { background: rgba(52,211,153,.25);  color: #34d399; border: 1px solid rgba(52,211,153,.4); }
        .inv-status-pending { background: rgba(251,191,36,.25);  color: #fbbf24; border: 1px solid rgba(251,191,36,.4); }
        .inv-status-overdue { background: rgba(248,113,113,.25); color: #f87171; border: 1px solid rgba(248,113,113,.4); }

        /* ── Body ── */
        .inv-print-body { padding: 36px 44px; background: var(--t-bg-card); }

        /* ── Overdue alert ── */
        .inv-overdue-alert {
            background: var(--inv-alert-bg,  rgba(249,115,22,.08));
            border: 1px solid var(--inv-alert-border, rgba(249,115,22,.3));
            border-left: 4px solid #f97316;
            border-radius: var(--t-radius-sm);
            padding: 10px 14px;
            margin-bottom: 24px;
            font-size: .82rem;
            color: var(--inv-alert-text, #9a3412);
        }

        /* ── Parties ── */
        .inv-print-parties {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
            margin-bottom: 34px;
        }
        .inv-print-party-label {
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: var(--t-text-muted);
            font-weight: 700;
            margin-bottom: 10px;
        }
        .inv-print-party-name { font-size: .95rem; font-weight: 700; color: var(--t-text-primary); margin-bottom: 4px; }
        .inv-print-party-detail { font-size: .8rem; color: var(--t-text-secondary); line-height: 1.6; }

        .inv-print-meta-row {
            display: flex;
            justify-content: space-between;
            font-size: .8rem;
            color: var(--t-text-secondary);
            padding: 4px 0;
            border-bottom: 1px solid var(--t-border);
        }
        .inv-print-meta-row:last-child { border-bottom: none; }
        .inv-print-meta-row strong { color: var(--t-text-primary); }

        .inv-print-divider { height: 1px; background: var(--t-border); margin: 0 0 28px; }

        .inv-print-section-label {
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: var(--t-text-muted);
            font-weight: 700;
            margin-bottom: 12px;
        }

        /* ── Items table ── */
        .inv-print-table-wrap {
            border: 1px solid var(--t-border);
            border-radius: var(--t-radius-md);
            overflow: hidden;
            margin-bottom: 20px;
        }
        .inv-print-table { width: 100%; border-collapse: collapse; font-size: .84rem; }
        .inv-print-table thead tr { background: var(--t-bg-hover); }
        .inv-print-table thead th {
            padding: 11px 14px;
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--t-text-muted);
            font-weight: 700;
            border-bottom: 1px solid var(--t-border);
            text-align: left;
        }
        .inv-print-table th.text-right,
        .inv-print-table td.text-right { text-align: right; }
        .inv-print-table tbody tr { border-bottom: 1px solid var(--t-border); }
        .inv-print-table tbody tr:last-child { border-bottom: none; }
        .inv-print-table tbody td { padding: 12px 14px; color: var(--t-text-secondary); }

        .inv-type-pill {
            background: var(--inv-type-bg);
            color: var(--inv-type-fg);
            font-size: .68rem; font-weight: 700;
            padding: 3px 9px; border-radius: 20px;
        }

        /* ── Totals ── */
        .inv-print-totals-wrap { display: flex; justify-content: flex-end; margin-bottom: 28px; }
        .inv-print-totals { width: 300px; border: 1px solid var(--t-border); border-radius: var(--t-radius-md); overflow: hidden; }
        .inv-print-total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 16px;
            font-size: .84rem;
            color: var(--t-text-muted);
            border-bottom: 1px solid var(--t-border);
        }
        .inv-print-total-row:last-child { border-bottom: none; }
        .inv-print-total-row span:last-child { font-weight: 600; color: var(--t-text-primary); }
        .inv-print-total-fee { background: var(--inv-fee-bg); }
        .inv-print-total-fee span:last-child { color: var(--t-accent-red) !important; }
        .inv-print-total-grand {
            background: linear-gradient(135deg, #1a1f5e, #2d3482) !important;
            font-size: .92rem; font-weight: 700;
        }
        .inv-print-total-grand span { color: #fff !important; }

        /* ── Transaction ── */
        .inv-print-txn {
            background: var(--inv-txn-bg);
            border: 1px solid var(--inv-txn-border);
            border-radius: var(--t-radius-md);
            padding: 16px 20px;
            margin-bottom: 28px;
        }
        .inv-print-txn-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 30px; }
        .inv-print-txn-item { font-size: .82rem; }
        .inv-print-txn-item span { color: var(--t-text-muted); display: block; font-size: .7rem; margin-bottom: 1px; }
        .inv-print-txn-item strong { color: var(--t-text-primary); }
        .inv-txn-amount { color: var(--t-accent-green) !important; }

        /* ── Footer ── */
        .inv-print-footer {
            background: var(--t-bg-hover);
            border-top: 1px solid var(--t-border);
            padding: 20px 44px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .inv-print-footer-left { font-size: .8rem; color: var(--t-text-muted); }
        .inv-print-footer-left strong { color: var(--t-text-secondary); font-weight: 700; display: block; margin-bottom: 2px; }
        .inv-print-footer-right { text-align: right; font-size: .75rem; color: var(--t-text-muted); }
        .inv-print-footer-right strong { font-size: .9rem; color: var(--t-text-primary); display: block; margin-bottom: 2px; }

        /* ── Print Media ── */
        @media print {
            :root {
                --t-bg-base:  #fff !important;
                --t-bg-card:  #fff !important;
                --t-bg-hover: #f8fafc !important;
                --t-border:   #e2e8f0 !important;
                --t-text-primary:   #1e293b !important;
                --t-text-secondary: #475569 !important;
                --t-text-muted:     #94a3b8 !important;
                --inv-type-bg: #eff6ff !important; --inv-type-fg: #1d4ed8 !important;
                --inv-fee-bg:  rgba(239,68,68,.06) !important;
                --inv-txn-bg:  #f0fdf4 !important; --inv-txn-border: #bbf7d0 !important;
                --inv-alert-bg: rgba(249,115,22,.08) !important;
                --inv-alert-text: #9a3412 !important;
            }
            body { background: #fff !important; padding: 0 !important; }
            .inv-print-page { box-shadow: none !important; border: none !important; border-radius: 0 !important; max-width: 100% !important; }
            .inv-print-action-bar { display: none !important; }
            .inv-print-footer { break-inside: avoid; }
        }
    </style>
</head>
<body>

@php
    $isPaid    = $invoice->status == INVOICE_STATUS_PAID;
    $isOverdue = !$isPaid && $invoice->due_date < date('Y-m-d');
    $subtotal  = $items->sum('amount');
    $totalTax  = $items->sum('tax_amount');
    $lateFee   = $invoice->late_fee ?? 0;
    $grandTotal = $invoice->amount + $lateFee;
@endphp

<div class="inv-print-page">

    {{-- Action bar (hidden on print) --}}
    <div class="inv-print-action-bar">
        <a href="javascript:history.back()" class="inv-print-action-btn inv-action-back">
            <i class="ri-arrow-left-line"></i> {{ __('Back') }}
        </a>
        <button onclick="window.print()" class="inv-print-action-btn inv-action-print">
            <i class="ri-printer-line"></i> {{ __('Print / Save PDF') }}
        </button>
    </div>

    {{-- Header --}}
    <div class="inv-print-header">
        <div class="inv-print-logo">
            @if ($owner->print_name)
                <img src="{{ assetUrl($owner->folder_name . '/' . $owner->file_name) }}" alt="Logo">
            @else
                <img src="{{ getSettingImage('app_logo') }}" alt="Logo">
            @endif
        </div>
        <div class="inv-print-hdr-right">
            <div class="inv-print-inv-label">{{ __('Invoice Number') }}</div>
            <div class="inv-print-inv-no">{{ $invoice->invoice_no }}</div>
            <span class="inv-print-status {{ $isPaid ? 'inv-status-paid' : ($isOverdue ? 'inv-status-overdue' : 'inv-status-pending') }}">
                @if($isPaid) ✓ {{ __('Paid') }}
                @elseif($isOverdue) ⚠ {{ __('Overdue') }}
                @else ● {{ __('Pending') }}
                @endif
            </span>
        </div>
    </div>

    {{-- Body --}}
    <div class="inv-print-body">

        @if($isOverdue)
        <div class="inv-overdue-alert">
            <strong>⚠ {{ __('Overdue Notice') }}:</strong>
            {{ __('This invoice was due on') }} {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}.
            @if($lateFee > 0) {{ __('A late fee of') }} {{ currencyPrice($lateFee) }} {{ __('has been applied.') }} @endif
            {{ __('Please make payment immediately to avoid further charges.') }}
        </div>
        @endif

        {{-- Parties --}}
        <div class="inv-print-parties">
            <div>
                <div class="inv-print-party-label">{{ __('Invoice To') }}</div>
                <div class="inv-print-party-name">{{ $tenant->first_name }} {{ $tenant->last_name }}</div>
                <div class="inv-print-party-detail">{{ $tenant->email }}</div>
                <div class="inv-print-party-detail">{{ $tenant->property_name }}</div>
                <div class="inv-print-party-detail">{{ $tenant->unit_name }}</div>
            </div>
            <div>
                <div class="inv-print-party-label">{{ __('Pay To') }}</div>
                @if ($owner->print_name)
                    <div class="inv-print-party-name">{{ $owner->print_name }}</div>
                    <div class="inv-print-party-detail">{{ $owner->print_address }}</div>
                    <div class="inv-print-party-detail">{{ $owner->print_contact }}</div>
                @else
                    <div class="inv-print-party-name">{{ getOption('app_name') }}</div>
                    <div class="inv-print-party-detail">{{ getOption('app_location') }}</div>
                    <div class="inv-print-party-detail">{{ getOption('app_contact_number') }}</div>
                @endif
            </div>
            <div>
                <div class="inv-print-party-label">{{ __('Invoice Details') }}</div>
                <div class="inv-print-meta-row"><span>{{ __('Period') }}</span><strong>{{ $invoice->month }}</strong></div>
                <div class="inv-print-meta-row"><span>{{ __('Issue Date') }}</span><strong>{{ $invoice->created_at->format('d M Y') }}</strong></div>
                <div class="inv-print-meta-row"><span>{{ __('Due Date') }}</span><strong>{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</strong></div>
                <div class="inv-print-meta-row"><span>{{ __('Status') }}</span>
                    <strong style="color:{{ $isPaid ? '#10b981' : ($isOverdue ? '#ef4444' : '#f59e0b') }}">
                        {{ $isPaid ? __('Paid') : ($isOverdue ? __('Overdue') : __('Pending')) }}
                    </strong>
                </div>
            </div>
        </div>

        <div class="inv-print-divider"></div>

        {{-- Items --}}
        <div class="inv-print-section-label">{{ __('Invoice Items') }}</div>
        <div class="inv-print-table-wrap">
            <table class="inv-print-table">
                <thead>
                    <tr>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th class="text-right">{{ __('Amount') }}</th>
                        <th class="text-right">{{ __('Tax') }}</th>
                        <th class="text-right">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                    <tr>
                        <td><span class="inv-type-pill">{{ $item->invoiceType?->name ?? '—' }}</span></td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->created_at->format('d M Y') }}</td>
                        <td class="text-right">{{ currencyPrice($item->amount) }}</td>
                        <td class="text-right">{{ $item->tax_amount > 0 ? currencyPrice($item->tax_amount) : '—' }}</td>
                        <td class="text-right" style="font-weight:600;">{{ currencyPrice($item->amount + $item->tax_amount) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Totals --}}
        <div class="inv-print-totals-wrap">
            <div class="inv-print-totals">
                <div class="inv-print-total-row"><span>{{ __('Subtotal') }}</span><span>{{ currencyPrice($subtotal) }}</span></div>
                @if($totalTax > 0)
                <div class="inv-print-total-row"><span>{{ __('Tax') }}</span><span>{{ currencyPrice($totalTax) }}</span></div>
                @endif
                @if($lateFee > 0)
                <div class="inv-print-total-row inv-print-total-fee"><span>{{ __('Late Fee') }}</span><span>{{ currencyPrice($lateFee) }}</span></div>
                @endif
                <div class="inv-print-total-row inv-print-total-grand"><span>{{ __('Total Due') }}</span><span>{{ currencyPrice($grandTotal) }}</span></div>
            </div>
        </div>

        {{-- Transaction --}}
        @if($isPaid)
        <div class="inv-print-section-label">{{ __('Payment Confirmation') }}</div>
        @isset($order)
        <div class="inv-print-txn">
            <div class="inv-print-txn-grid">
                <div class="inv-print-txn-item">
                    <span>{{ __('Payment Gateway') }}</span>
                    <strong>{{ $order?->gatewayTitle ?? __('Cash') }}</strong>
                </div>
                <div class="inv-print-txn-item">
                    <span>{{ __('Transaction ID') }}</span>
                    <strong style="font-family:monospace;font-size:.78rem;">{{ $order?->payment_id ?: $order?->transaction_id }}</strong>
                </div>
                <div class="inv-print-txn-item">
                    <span>{{ __('Payment Date') }}</span>
                    <strong>{{ $order?->created_at->format('d M Y, H:i') }}</strong>
                </div>
                <div class="inv-print-txn-item">
                    <span>{{ __('Amount Paid') }}</span>
                    <strong class="inv-txn-amount">{{ currencyPrice($order?->total) }}</strong>
                </div>
            </div>
        </div>
        @endisset
        @endif

    </div>

    {{-- Footer --}}
    <div class="inv-print-footer">
        <div class="inv-print-footer-left">
            <strong>{{ getOption('app_name') }}</strong>
            {{ getOption('app_location') }} &bull; {{ getOption('app_contact_number') }}
        </div>
        <div class="inv-print-footer-right">
            <strong>{{ currencyPrice($grandTotal) }}</strong>
            {{ $isPaid ? __('PAID IN FULL') : ($isOverdue ? __('PAYMENT OVERDUE') : __('PAYMENT DUE')) }}
        </div>
    </div>

</div>

@include('common.layouts.script')
<script>
    window.addEventListener('load', function() {
        setTimeout(function() { window.print(); }, 700);
    });
</script>
</body>
</html>
