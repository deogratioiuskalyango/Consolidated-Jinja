@extends('shareholder.layouts.app')
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">

                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-flex flex-column flex-sm-row align-items-sm-center justify-content-between g-20">
                            <div class="page-title-left">
                                <h2 class="mb-sm-0">{{ __('Dividends') }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('Period') }}</th>
                                                <th>{{ __('Declared Date') }}</th>
                                                <th>{{ __('Per-Share Amount') }}</th>
                                                <th>{{ __('Your Shares') }}</th>
                                                <th>{{ __('Your Payout') }}</th>
                                                <th>{{ __('Tax Withheld') }}</th>
                                                <th>{{ __('Net Amount') }}</th>
                                                <th>{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($payments as $payment)
                                                @php
                                                    $declaration = $payment->declaration;
                                                    $statusMap = [
                                                        1 => ['label' => 'Declared',  'class' => 'bg-info'],
                                                        2 => ['label' => 'Paid',       'class' => 'bg-success'],
                                                        3 => ['label' => 'Cancelled',  'class' => 'bg-danger'],
                                                    ];
                                                    $st = $statusMap[$declaration->status ?? 0] ?? ['label' => 'Unknown', 'class' => 'bg-secondary'];
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $declaration->title ?? '-' }}</td>
                                                    <td>
                                                        {{ $declaration && $declaration->declaration_date
                                                            ? $declaration->declaration_date->format('M d, Y')
                                                            : '-' }}
                                                    </td>
                                                    <td>{{ $declaration ? number_format($declaration->per_share_amount, 4) : '-' }}</td>
                                                    <td>{{ number_format($payment->shares_held ?? 0, 4) }}</td>
                                                    <td>{{ number_format($payment->gross_amount ?? 0, 2) }}</td>
                                                    <td>{{ number_format($payment->tax_withheld ?? 0, 2) }}</td>
                                                    <td><strong>{{ number_format($payment->net_amount ?? 0, 2) }}</strong></td>
                                                    <td>
                                                        <span class="badge {{ $st['class'] }}">{{ __($st['label']) }}</span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center text-muted py-4">
                                                        <i class="ri-coins-line fs-2 d-block mb-2"></i>
                                                        {{ __('No dividend payments found.') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            {{ $payments->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
