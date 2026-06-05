@extends('owner.layouts.app')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="page-content-wrapper bg-white p-30 radius-20">

                    {{-- Page Header --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-flex flex-column flex-sm-row align-items-sm-center justify-content-between g-20 border-bottom mb-25">
                                <div class="page-title-left">
                                    <h2 class="mb-sm-0">
                                        {{ __('Auditor Dashboard') }}
                                        <span class="badge bg-info text-white ms-2 font-13 fw-normal">{{ __('Read Only Access') }}</span>
                                    </h2>
                                    <p class="mb-0">{{ now()->format('l, d F Y') }}</p>
                                </div>
                                <div class="page-title-right">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                        <li class="breadcrumb-item active">{{ __('Auditor') }}</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Read-only alert banner --}}
                    <div class="alert" style="background:#e8f4fd;border:1px solid #bee3f8;color:#2b6cb0;border-radius:6px;" role="alert">
                        <i class="ri-shield-check-line me-2"></i>
                        <strong>{{ __('Information:') }}</strong>
                        {{ __('This is a read-only view. You cannot modify any records.') }}
                    </div>

                    {{-- Row 1: 6 KPI Cards --}}
                    <div class="row">
                        <div class="col-sm-6 col-lg-4 col-xl-2">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-file-list-3-line primary-color font-22"></i>
                                </div>
                                <p class="mt-2">{{ __('Total Audit Logs') }}</p>
                                <h2 class="mt-1">{{ number_format($totalAuditLogs, 0) }}</h2>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-xl-2">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-calendar-todo-line orange-color font-22"></i>
                                </div>
                                <p class="mt-2">{{ __("Today's Activity") }}</p>
                                <h2 class="mt-1">{{ number_format($todayLogs, 0) }}</h2>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-xl-2">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-calendar-line" style="color:#805ad5;font-size:22px;"></i>
                                </div>
                                <p class="mt-2">{{ __('This Week') }}</p>
                                <h2 class="mt-1">{{ number_format($weekLogs, 0) }}</h2>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-xl-2">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-money-dollar-circle-line green-color font-22"></i>
                                </div>
                                <p class="mt-2">{{ __('Annual Revenue') }}</p>
                                <h2 class="mt-1" style="font-size:1.1rem;">UGX {{ number_format($totalRevenue, 0) }}</h2>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-xl-2">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-arrow-up-circle-line" style="color:#e53e3e;font-size:22px;"></i>
                                </div>
                                <p class="mt-2">{{ __('Annual Expenses') }}</p>
                                <h2 class="mt-1" style="font-size:1.1rem;">UGX {{ number_format($totalExpenses, 0) }}</h2>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-xl-2">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-time-line orange-color font-22"></i>
                                </div>
                                <p class="mt-2">{{ __('Pending Approvals') }}</p>
                                <h2 class="mt-1">{{ number_format($pendingApprovals, 0) }}</h2>
                            </div>
                        </div>
                    </div>
                    {{-- End KPI Cards --}}

                    {{-- Row 2: Audit Log Table + Activity Summary --}}
                    <div class="row">
                        {{-- Recent Audit Log --}}
                        <div class="col-lg-8">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="d-flex align-items-center justify-content-between mb-20">
                                    <h4 class="mb-0"><i class="ri-file-list-2-line me-2"></i>{{ __('Recent Audit Log') }}</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table theme-border">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Action') }}</th>
                                                <th>{{ __('Actor') }}</th>
                                                <th>{{ __('Note') }}</th>
                                                <th>{{ __('IP Address') }}</th>
                                                <th>{{ __('Date & Time') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($recentLogs as $log)
                                                <tr>
                                                    <td>
                                                        @php
                                                            $badgeClass = match($log->action ?? '') {
                                                                'payment_recorded' => 'bg-success',
                                                                'payment_reversed' => 'bg-danger',
                                                                'expense_approved' => 'bg-info',
                                                                default            => 'bg-secondary',
                                                            };
                                                        @endphp
                                                        <span class="badge {{ $badgeClass }} text-white">
                                                            {{ str_replace('_', ' ', ucfirst($log->action ?? '—')) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $log->actor_name ?? '—' }}</td>
                                                    <td title="{{ $log->note }}">{{ Str::limit($log->note, 50) }}</td>
                                                    <td>{{ $log->ip_address ?? '—' }}</td>
                                                    <td>{{ $log->created_at ? $log->created_at->format('d M Y H:i') : '—' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">{{ __('No audit logs found') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Activity Summary --}}
                        <div class="col-lg-4">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <h4 class="mb-20"><i class="ri-bar-chart-2-line me-2"></i>{{ __('Activity Summary') }}</h4>
                                @forelse ($actionSummary as $item)
                                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                        <span class="font-14">{{ str_replace('_', ' ', ucfirst($item->action ?? '—')) }}</span>
                                        <span class="badge bg-primary text-white">{{ $item->total }}</span>
                                    </div>
                                @empty
                                    <p class="text-center text-muted mt-3">{{ __('No data available') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    {{-- End Row 2 --}}

                    {{-- Row 3: Large Transactions --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="d-flex align-items-center justify-content-between mb-20">
                                    <h4 class="mb-0"><i class="ri-bank-card-line me-2"></i>{{ __('Top 5 Transactions This Year') }}</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table theme-border">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('Tenant') }}</th>
                                                <th>{{ __('Amount (UGX)') }}</th>
                                                <th>{{ __('Date') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($largeTransactions as $i => $invoice)
                                                <tr>
                                                    <td>{{ $i + 1 }}</td>
                                                    <td>{{ $invoice->tenant?->name ?? '—' }}</td>
                                                    <td><strong>UGX {{ number_format($invoice->amount, 0) }}</strong></td>
                                                    <td>{{ $invoice->created_at ? $invoice->created_at->format('d M Y') : '—' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">{{ __('No transactions found') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- End Row 3 --}}

                    {{-- Footer note --}}
                    <div class="row">
                        <div class="col-12">
                            <p class="text-muted font-13">
                                <i class="ri-download-line me-1"></i>
                                @if (Route::has('accountant.reports.index'))
                                    <a href="{{ route('accountant.reports.index') }}" class="theme-link">{{ __('Download full audit report') }}</a>
                                @else
                                    {{ __('Download full audit report') }} — {{ __('available via Accountant Reports') }}
                                @endif
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
