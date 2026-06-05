<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Receipt') }} - {{ $collection->receipt_number ?? 'N/A' }}</title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
          crossorigin="anonymous">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f5f5f5;
        }

        .receipt-wrapper {
            max-width: 680px;
            margin: 40px auto;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }

        .receipt-header {
            background: #1a3c5e;
            color: #fff;
            text-align: center;
            padding: 30px 20px 20px;
        }

        .receipt-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .receipt-header p {
            font-size: 0.85rem;
            opacity: 0.85;
            margin: 0;
        }

        .receipt-subtitle {
            background: #e8f0fe;
            text-align: center;
            padding: 12px;
            font-size: 1rem;
            font-weight: 600;
            color: #1a3c5e;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #1a3c5e;
        }

        .receipt-body {
            padding: 24px 32px;
        }

        .receipt-amount {
            text-align: center;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
            border: 2px dashed #1a3c5e;
        }

        .receipt-amount .amount-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6c757d;
        }

        .receipt-amount .amount-value {
            font-size: 2rem;
            font-weight: 800;
            color: #1a3c5e;
        }

        .info-table td {
            padding: 8px 4px;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-table td:first-child {
            color: #6c757d;
            font-size: 0.85rem;
            width: 45%;
        }

        .info-table td:last-child {
            font-weight: 500;
        }

        .receipt-footer {
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            text-align: center;
            padding: 16px;
            font-size: 0.8rem;
            color: #6c757d;
        }

        @media print {
            body {
                background: #fff;
            }

            .receipt-wrapper {
                margin: 0;
                border: none;
                border-radius: 0;
                box-shadow: none;
            }

            .d-print-none {
                display: none !important;
            }

            .receipt-header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    {{-- Action Buttons (hidden on print) --}}
    <div class="text-center mt-3 d-print-none">
        <button onclick="window.print()" class="btn btn-primary me-2">
            <i class="ri-printer-line me-1"></i> {{ __('Print Receipt') }}
        </button>
        <a href="{{ route('accountant.collections.show', $collection) }}" class="btn btn-secondary">
            {{ __('Back') }}
        </a>
    </div>

    <div class="receipt-wrapper mt-3">

        {{-- Header --}}
        <div class="receipt-header">
            <h2>JINJA CONSOLIDATED PROPERTIES</h2>
            <p>P.O. Box 1234, Jinja, Uganda &bull; Tel: +256 700 000 000</p>
        </div>

        <div class="receipt-subtitle">
            RENT PAYMENT RECEIPT
        </div>

        {{-- Body --}}
        <div class="receipt-body">

            {{-- Meta: Receipt # and Date --}}
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <span class="text-muted small">{{ __('Receipt No.') }}</span><br>
                    <strong class="fs-5">{{ $collection->receipt_number ?? 'N/A' }}</strong>
                </div>
                <div class="text-end">
                    <span class="text-muted small">{{ __('Date') }}</span><br>
                    <strong>{{ \Carbon\Carbon::parse($collection->payment_date)->format('d M Y') }}</strong>
                </div>
            </div>

            <hr>

            {{-- Amount block --}}
            <div class="receipt-amount">
                <div class="amount-label">{{ __('Amount Paid') }}</div>
                <div class="amount-value">UGX {{ number_format($collection->amount) }}</div>
            </div>

            {{-- Details table --}}
            <table class="table info-table w-100">
                <tbody>
                    <tr>
                        <td>{{ __('Tenant Name') }}</td>
                        <td>{{ $collection->tenant->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('Property') }}</td>
                        <td>{{ $collection->property->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('Unit') }}</td>
                        <td>{{ $collection->unit->unit_number ?? ($collection->unit->name ?? '—') }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('Payment For') }}</td>
                        <td>{{ $collectionTypes[$collection->collection_type] ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('Payment Method') }}</td>
                        <td>{{ $paymentMethods[$collection->payment_method] ?? '—' }}</td>
                    </tr>
                    @if($collection->transaction_ref)
                    <tr>
                        <td>{{ __('Transaction Reference') }}</td>
                        <td>{{ $collection->transaction_ref }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>{{ __('Status') }}</td>
                        <td>
                            @if($collection->status == 'confirmed')
                                <span class="badge bg-success">{{ __('Confirmed') }}</span>
                            @elseif($collection->status == 'pending')
                                <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                            @elseif($collection->status == 'reversed')
                                <span class="badge bg-danger">{{ __('Reversed') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($collection->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>{{ __('Received By') }}</td>
                        <td>{{ $collection->collectedBy->name ?? '—' }}</td>
                    </tr>
                </tbody>
            </table>

            @if($collection->notes)
            <div class="alert alert-light border mt-2 mb-0">
                <small><strong>{{ __('Notes:') }}</strong> {{ $collection->notes }}</small>
            </div>
            @endif

        </div>

        {{-- Footer --}}
        <div class="receipt-footer">
            <strong>{{ __('This is an official receipt. Keep for your records.') }}</strong><br>
            {{ __('Generated on') }} {{ now()->format('d M Y H:i') }}
        </div>

    </div>

    <div class="text-center my-3 d-print-none">
        <button onclick="window.print()" class="btn btn-primary me-2">
            <i class="ri-printer-line me-1"></i> {{ __('Print Receipt') }}
        </button>
        <a href="{{ route('accountant.collections.show', $collection) }}" class="btn btn-secondary">
            {{ __('Back') }}
        </a>
    </div>

</body>
</html>
