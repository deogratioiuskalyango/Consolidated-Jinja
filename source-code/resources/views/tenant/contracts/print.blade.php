<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenancy Agreement — {{ $contract->reference_no }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 13px; margin: 40px; color: #222; }
        h1 { text-align: center; font-size: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 6px; }
        h2 { font-size: 15px; margin-top: 24px; margin-bottom: 8px; border-bottom: 1px solid #999; padding-bottom: 4px; }
        p { margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px 10px; font-size: 13px; vertical-align: top; }
        th { background: #f0f0f0; width: 35%; font-weight: bold; text-align: left; }
        .clause { margin-bottom: 16px; }
        .clause-num { display: inline-block; background: #f0f0f0; border-radius: 50%; width: 22px; height: 22px;
            text-align: center; line-height: 22px; font-size: 11px; font-weight: bold; margin-right: 6px; }
        .clause h3 { font-size: 13px; font-weight: bold; margin-bottom: 4px; display: flex; align-items: center; }
        .clause p { margin-left: 28px; color: #444; line-height: 1.6; }
        .sig-section { margin-top: 30px; }
        .sig-row { display: flex; gap: 16px; flex-wrap: wrap; }
        .sig-block { flex: 1; min-width: 150px; text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .sig-block img { max-height: 60px; max-width: 100%; display: block; margin: 0 auto 8px; }
        .sig-line { border-top: 1px solid #000; margin-top: 50px; padding-top: 4px; font-size: 11px; color: #555; }
        .sig-label { font-weight: bold; font-size: 12px; margin-bottom: 2px; }
        .sig-sub { font-size: 11px; color: #666; }
        .print-btn { text-align: center; margin: 20px 0; }
        .print-btn button { padding: 8px 24px; font-size: 14px; cursor: pointer; background: #0d6efd;
            color: #fff; border: none; border-radius: 4px; }
        .print-btn button:hover { background: #0b5ed7; }
        .status-badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px;
            font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        @media print {
            .print-btn { display: none; }
            body { margin: 20px; }
        }
    </style>
</head>
<body>

    <div class="print-btn">
        <button onclick="window.print()">🖨️ Print / Save as PDF</button>
    </div>

    <h1>TENANCY AGREEMENT</h1>
    <p style="text-align:center; margin-top:6px; margin-bottom:4px;">
        Reference: <strong>{{ $contract->reference_no }}</strong>
    </p>
    <p style="text-align:center; color:#555; font-size:12px; margin-bottom:20px;">
        Status:
        <strong>{{ strtoupper(str_replace('_', ' ', $contract->status)) }}</strong>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        Date: {{ now()->format('d F Y') }}
    </p>

    {{-- ─── SECTION A: Tenant Particulars ─── --}}
    <h2>SECTION A — TENANT PARTICULARS</h2>
    <table>
        <tr><th>Full Name</th><td>{{ $contract->tenant_name ?? '—' }}</td></tr>
        <tr><th>Phone</th><td>{{ $contract->tenant_phone ?? '—' }}</td></tr>
        <tr><th>Email</th><td>{{ $contract->tenant_email ?? '—' }}</td></tr>
        <tr><th>Nationality</th><td>{{ $contract->tenant_nationality ?? '—' }}</td></tr>
        <tr><th>ID Type</th><td>{{ $contract->tenant_id_type ?? '—' }}</td></tr>
        <tr><th>ID Number</th><td>{{ $contract->tenant_id_number ?? '—' }}</td></tr>
        <tr><th>Next of Kin Name</th><td>{{ $contract->next_of_kin_name ?? '—' }}</td></tr>
        <tr><th>Next of Kin Phone</th><td>{{ $contract->next_of_kin_phone ?? '—' }}</td></tr>
    </table>

    {{-- ─── SECTION B: Property Details ─── --}}
    <h2>SECTION B — PROPERTY DETAILS</h2>
    <table>
        <tr><th>Property Name</th><td>{{ $contract->property_name ?? '—' }}</td></tr>
        <tr><th>Property Address</th><td>{{ $contract->property_address ?? '—' }}</td></tr>
        <tr><th>Unit / Room</th><td>{{ $contract->unit_name ?? '—' }}</td></tr>
    </table>

    {{-- ─── SECTION C: Payment Terms ─── --}}
    <h2>SECTION C — PAYMENT TERMS</h2>
    <table>
        <tr><th>Monthly Rent</th><td>{{ $contract->currency }} {{ number_format($contract->monthly_rent, 0) }}</td></tr>
        <tr><th>Security Deposit</th><td>{{ $contract->currency }} {{ number_format($contract->security_deposit, 0) }}</td></tr>
        <tr><th>Currency</th><td>{{ $contract->currency }}</td></tr>
        <tr><th>Payment Due Day</th><td>{{ $contract->payment_due_day ? 'Day ' . $contract->payment_due_day . ' of each month' : '—' }}</td></tr>
        <tr><th>Payment Method</th><td>{{ $contract->payment_method ?? '—' }}</td></tr>
    </table>

    {{-- ─── SECTION D: Bank Details ─── --}}
    <h2>SECTION D — BANK DETAILS</h2>
    <table>
        <tr><th>Bank Name</th><td>{{ $contract->bank_name ?? '—' }}</td></tr>
        <tr><th>Account Name</th><td>{{ $contract->bank_account_name ?? '—' }}</td></tr>
        <tr><th>Account Number</th><td>{{ $contract->bank_account_number ?? '—' }}</td></tr>
        <tr><th>Branch</th><td>{{ $contract->bank_branch ?? '—' }}</td></tr>
        <tr><th>Mobile Money Number</th><td>{{ $contract->mobile_money_number ?? '—' }}</td></tr>
    </table>

    {{-- ─── SECTION E: Tenancy Period ─── --}}
    <h2>SECTION E — TENANCY PERIOD</h2>
    <table>
        <tr><th>Commencement Date</th><td>{{ $contract->commencement_date ? $contract->commencement_date->format('d F Y') : '—' }}</td></tr>
        <tr><th>Expiry Date</th><td>{{ $contract->expiry_date ? $contract->expiry_date->format('d F Y') : '—' }}</td></tr>
        <tr><th>Notice Period</th><td>{{ $contract->notice_period_days ? $contract->notice_period_days . ' days' : '—' }}</td></tr>
    </table>

    {{-- ─── TERMS AND CONDITIONS ─── --}}
    <h2>TERMS AND CONDITIONS</h2>

    @php
        $clauseLabels = \App\Models\ContractClauseReview::CLAUSES;
        $clauseTexts  = [
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
        $i = 1;
    @endphp

    @foreach($clauseLabels as $key => $label)
        <div class="clause">
            <h3>
                <span class="clause-num">{{ $i++ }}</span>
                {{ $label }}
            </h3>
            <p>{{ $clauseTexts[$key] ?? '' }}</p>
        </div>
    @endforeach

    {{-- ─── Special Conditions ─── --}}
    @if($contract->special_conditions)
        <h2>SPECIAL CONDITIONS</h2>
        <p>{{ $contract->special_conditions }}</p>
    @endif

    {{-- ─── SIGNATURES ─── --}}
    <h2>SIGNATURES</h2>
    <p style="font-size:12px; color:#666; margin-bottom:16px;">
        By signing this agreement, the parties confirm that they have read, understood, and agree to be bound by all the terms and conditions set out herein.
    </p>

    {{-- Two-column layout: Owner's side (left) | Tenant's side (right) --}}
    <table style="width:100%; border-collapse:collapse; margin-top:8px;">
        <tr>
            <td style="width:50%; vertical-align:top; padding-right:20px; border-right:2px solid #ccc;">
                <p style="font-weight:bold; font-size:13px; border-bottom:1px solid #999; padding-bottom:4px; margin-bottom:12px; text-transform:uppercase; letter-spacing:0.5px;">
                    Owner's Side
                </p>

                {{-- LC1 Chairperson --}}
                <div class="sig-block" style="width:100%; margin-bottom:16px;">
                    <div class="sig-label">LC1 Chairperson</div>
                    <div class="sig-sub">{{ $contract->lc1_name ?? '&nbsp;' }}</div>
                    @if($contract->lc1_phone)
                        <div class="sig-sub">{{ $contract->lc1_phone }}</div>
                    @endif
                    @if($contract->lc1_signature)
                        <img src="{{ $contract->lc1_signature }}" alt="LC1 Signature" style="max-height:60px; max-width:180px; display:block; margin:6px 0;">
                        @if($contract->lc1_stamp)
                            <img src="{{ Storage::url($contract->lc1_stamp) }}" alt="LC1 Stamp" style="max-height:50px; max-width:120px; display:block; margin-top:4px;">
                        @endif
                        @if($contract->lc1_signed_at)
                            <div class="sig-sub">{{ $contract->lc1_signed_at->format('d M Y') }}</div>
                        @endif
                    @else
                        <div class="sig-line" style="margin-top:50px;">Signature &amp; Stamp &amp; Date</div>
                    @endif
                </div>

                {{-- Landlord --}}
                <div class="sig-block" style="width:100%; margin-bottom:16px;">
                    <div class="sig-label">Landlord / Owner</div>
                    <div class="sig-sub">&nbsp;</div>
                    @if($contract->landlord_signature)
                        <img src="{{ $contract->landlord_signature }}" alt="Landlord Signature" style="max-height:60px; max-width:180px; display:block; margin:6px 0;">
                        @if($contract->landlord_signed_at)
                            <div class="sig-sub">{{ $contract->landlord_signed_at->format('d M Y') }}</div>
                        @endif
                    @else
                        <div class="sig-line" style="margin-top:50px;">Signature &amp; Date</div>
                    @endif
                </div>

                {{-- Witness 1 (Owner's Witness) --}}
                <div class="sig-block" style="width:100%;">
                    <div class="sig-label">Witness 1</div>
                    <div class="sig-sub">{{ $contract->witness1_name ?? '&nbsp;' }}</div>
                    @if($contract->witness1_signature)
                        <img src="{{ $contract->witness1_signature }}" alt="Witness 1 Signature" style="max-height:60px; max-width:180px; display:block; margin:6px 0;">
                        @if($contract->witness1_signed_at)
                            <div class="sig-sub">{{ $contract->witness1_signed_at->format('d M Y') }}</div>
                        @endif
                    @else
                        <div class="sig-line" style="margin-top:50px;">Signature &amp; Date</div>
                    @endif
                </div>
            </td>

            <td style="width:50%; vertical-align:top; padding-left:20px;">
                <p style="font-weight:bold; font-size:13px; border-bottom:1px solid #999; padding-bottom:4px; margin-bottom:12px; text-transform:uppercase; letter-spacing:0.5px;">
                    Tenant's Side
                </p>

                {{-- Tenant --}}
                <div class="sig-block" style="width:100%; margin-bottom:16px;">
                    <div class="sig-label">Tenant</div>
                    <div class="sig-sub">{{ $contract->tenant_name ?? '&nbsp;' }}</div>
                    @if($contract->tenant_signature)
                        <img src="{{ $contract->tenant_signature }}" alt="Tenant Signature" style="max-height:60px; max-width:180px; display:block; margin:6px 0;">
                        @if($contract->tenant_signed_at)
                            <div class="sig-sub">{{ $contract->tenant_signed_at->format('d M Y') }}</div>
                        @endif
                    @else
                        <div class="sig-line" style="margin-top:50px;">Signature &amp; Date</div>
                    @endif
                </div>

                {{-- Witness 2 (Tenant's Witness) --}}
                <div class="sig-block" style="width:100%;">
                    <div class="sig-label">Witness 2</div>
                    <div class="sig-sub">{{ $contract->witness2_name ?? '&nbsp;' }}</div>
                    @if($contract->witness2_signature)
                        <img src="{{ $contract->witness2_signature }}" alt="Witness 2 Signature" style="max-height:60px; max-width:180px; display:block; margin:6px 0;">
                        @if($contract->witness2_signed_at)
                            <div class="sig-sub">{{ $contract->witness2_signed_at->format('d M Y') }}</div>
                        @endif
                    @else
                        <div class="sig-line" style="margin-top:50px;">Signature &amp; Date</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <p style="margin-top:30px; font-size:11px; color:#888; text-align:center;">
        This document was generated on {{ now()->format('d F Y \a\t H:i') }} &nbsp;|&nbsp;
        Reference: {{ $contract->reference_no }}
    </p>

    <div class="print-btn" style="margin-top:30px;">
        <button onclick="window.print()">🖨️ Print / Save as PDF</button>
    </div>

</body>
</html>
