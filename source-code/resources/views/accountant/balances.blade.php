@php $navBalancesActiveClass = 'active mm-active'; @endphp
@extends('accountant.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">

                {{-- Page Title & Breadcrumb --}}
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between border-bottom mb-20">
                            <div class="page-title-left">
                                <h3 class="mb-sm-0">{{ __('Tenant Balances') }}</h3>
                            </div>
                            <div class="page-title-right">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                    <li class="breadcrumb-item active">{{ __('Tenant Balances') }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Summary Card --}}
                @php
                    $totalOutstanding = $balances->sum(function ($b) {
                        return max(0, ($b->total_charged ?? 0) - ($b->total_paid ?? 0));
                    });
                @endphp
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-danger border-start border-4 shadow-sm">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                                    <i class="ri-alarm-warning-line fs-4 text-danger"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">{{ __('Total Outstanding Balance (UGX)') }}</div>
                                    <div class="fs-4 fw-bold text-danger">{{ number_format($totalOutstanding) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-secondary border-start border-4 shadow-sm">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="bg-secondary bg-opacity-10 rounded-circle p-3">
                                    <i class="ri-group-line fs-4 text-secondary"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">{{ __('Total Tenants') }}</div>
                                    <div class="fs-4 fw-bold">{{ $balances->count() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-warning border-start border-4 shadow-sm">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                    <i class="ri-user-unfollow-line fs-4 text-warning"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">{{ __('Tenants with Balance Due') }}</div>
                                    <div class="fs-4 fw-bold">
                                        {{ $balances->filter(fn($b) => (($b->total_charged ?? 0) - ($b->total_paid ?? 0)) > 0)->count() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DataTable --}}
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="balancesTable" class="table table-bordered table-striped align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('SL') }}</th>
                                        <th>{{ __('Tenant Name') }}</th>
                                        <th>{{ __('Property / Unit') }}</th>
                                        <th>{{ __('Total Charged (UGX)') }}</th>
                                        <th>{{ __('Total Paid (UGX)') }}</th>
                                        <th>{{ __('Balance Due (UGX)') }}</th>
                                        <th>{{ __('Last Updated') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($balances as $index => $balance)
                                    @php
                                        $charged = $balance->total_charged ?? 0;
                                        $paid    = $balance->total_paid ?? 0;
                                        $due     = $charged - $paid;
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $balance->tenant->name ?? '—' }}</strong>
                                            @if($balance->tenant->phone ?? false)
                                                <br><small class="text-muted">{{ $balance->tenant->phone }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $balance->property->name ?? ($balance->tenant->property->name ?? '—') }}
                                            @if($balance->unit ?? false)
                                                <br><small class="text-muted">{{ __('Unit:') }} {{ $balance->unit->unit_number ?? $balance->unit->name }}</small>
                                            @elseif($balance->tenant->unit ?? false)
                                                <br><small class="text-muted">{{ __('Unit:') }} {{ $balance->tenant->unit->unit_number ?? $balance->tenant->unit->name }}</small>
                                            @endif
                                        </td>
                                        <td>{{ number_format($charged) }}</td>
                                        <td>{{ number_format($paid) }}</td>
                                        <td>
                                            @if($due > 0)
                                                <span class="text-danger fw-bold">{{ number_format($due) }}</span>
                                            @elseif($due < 0)
                                                <span class="text-info fw-semibold">({{ number_format(abs($due)) }}) {{ __('Overpaid') }}</span>
                                            @else
                                                <span class="text-success fw-semibold">{{ number_format(0) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $balance->updated_at ? \Carbon\Carbon::parse($balance->updated_at)->format('d M Y') : '—' }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light fw-bold">
                                    <tr>
                                        <td colspan="3" class="text-end">{{ __('Totals:') }}</td>
                                        <td>{{ number_format($balances->sum('total_charged')) }}</td>
                                        <td>{{ number_format($balances->sum('total_paid')) }}</td>
                                        <td class="text-danger">{{ number_format($totalOutstanding) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
    @include('common.layouts.datatable-style')
@endpush

@push('script')
    @include('common.layouts.datatable-script')
    <script>
        $(document).ready(function () {
            $('#balancesTable').DataTable({
                responsive: true,
                pageLength: 50,
                order: [[5, 'desc']],
                columnDefs: [
                    { type: 'num-fmt', targets: [3, 4, 5] }
                ]
            });
        });
    </script>
@endpush
