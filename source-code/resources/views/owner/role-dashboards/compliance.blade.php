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
                                    <h2 class="mb-sm-0">{{ __('Compliance Officer Dashboard') }}</h2>
                                    <p class="mb-0">{{ now()->format('l, d F Y') }}</p>
                                </div>
                                <div class="page-title-right">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                        <li class="breadcrumb-item active">{{ __('Compliance') }}</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 1: 6 KPI Cards --}}
                    <div class="row">
                        <div class="col-sm-6 col-lg-4 col-xl-2">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-file-paper-2-line primary-color font-22"></i>
                                </div>
                                <p class="mt-2">{{ __('Total Documents') }}</p>
                                <h2 class="mt-1">{{ number_format($totalDocuments, 0) }}</h2>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-xl-2">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-survey-line orange-color font-22"></i>
                                </div>
                                <p class="mt-2">{{ __('Total Resolutions') }}</p>
                                <h2 class="mt-1">{{ number_format($totalResolutions, 0) }}</h2>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-xl-2">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-checkbox-circle-line green-color font-22"></i>
                                </div>
                                <p class="mt-2">{{ __('Passed') }}</p>
                                <h2 class="mt-1">{{ number_format($passedResolutions, 0) }}</h2>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-xl-2">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-close-circle-line" style="color:#e53e3e;font-size:22px;"></i>
                                </div>
                                <p class="mt-2">{{ __('Failed') }}</p>
                                <h2 class="mt-1">{{ number_format($failedResolutions, 0) }}</h2>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-xl-2">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-time-line orange-color font-22"></i>
                                </div>
                                <p class="mt-2">{{ __('Open') }}</p>
                                <h2 class="mt-1">{{ number_format($openResolutions, 0) }}</h2>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-xl-2">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-user-follow-line primary-color font-22"></i>
                                </div>
                                <p class="mt-2">{{ __('Active Tenants') }}</p>
                                <h2 class="mt-1">{{ number_format($activeTenants, 0) }}</h2>
                            </div>
                        </div>
                    </div>
                    {{-- End KPI Cards --}}

                    {{-- Compliance Alerts --}}
                    @if (!empty($complianceAlerts))
                        <div class="row">
                            <div class="col-12">
                                <div class="bg-off-white theme-border radius-4 p-20 mb-25" style="border-left:4px solid #d69e2e !important;">
                                    <h5 class="mb-15"><i class="ri-alert-line me-2" style="color:#d69e2e;"></i>{{ __('Compliance Alerts') }}</h5>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($complianceAlerts as $alert)
                                            @php
                                                $cls = match($alert['type']) {
                                                    'warning' => 'bg-warning text-dark',
                                                    'danger'  => 'bg-danger text-white',
                                                    'info'    => 'bg-info text-white',
                                                    default   => 'bg-secondary text-white',
                                                };
                                            @endphp
                                            <span class="badge {{ $cls }} py-2 px-3 font-13">
                                                {{ $alert['message'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Row 2: Documents + Resolutions Overview --}}
                    <div class="row">
                        {{-- Recent Documents --}}
                        <div class="col-lg-7">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="d-flex align-items-center justify-content-between mb-20">
                                    <h4 class="mb-0"><i class="ri-folder-2-line me-2"></i>{{ __('Recent Documents') }}</h4>
                                    @if (Route::has('owner.governance.documents.index'))
                                        <a href="{{ route('owner.governance.documents.index') }}" class="theme-link font-14 d-flex align-items-center">
                                            {{ __('View All') }}<i class="ri-arrow-right-line ms-1"></i>
                                        </a>
                                    @endif
                                </div>
                                <div class="table-responsive">
                                    <table class="table theme-border">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Title') }}</th>
                                                <th>{{ __('Type') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($recentDocuments as $doc)
                                                <tr>
                                                    <td>{{ Str::limit($doc->title, 35) }}</td>
                                                    <td>{{ str_replace('_', ' ', ucfirst($doc->document_type ?? '—')) }}</td>
                                                    <td>{{ $doc->created_at ? $doc->created_at->format('d M Y') : '—' }}</td>
                                                    <td>
                                                        <span class="badge bg-secondary text-white">
                                                            {{ ucfirst($doc->status ?? '—') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">{{ __('No documents found') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Resolutions Overview + Audit Logs --}}
                        <div class="col-lg-5">
                            {{-- Mini stat boxes --}}
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <h4 class="mb-20"><i class="ri-pie-chart-line me-2"></i>{{ __('Resolutions Overview') }}</h4>

                                <div class="d-flex align-items-center justify-content-between p-15 radius-4 mb-10" style="background:#f0fff4;border:1px solid #c6f6d5;">
                                    <span class="font-15 fw-medium"><i class="ri-checkbox-circle-line me-2 green-color"></i>{{ __('Passed') }}</span>
                                    <strong class="green-color font-20">{{ $passedResolutions }}</strong>
                                </div>
                                <div class="d-flex align-items-center justify-content-between p-15 radius-4 mb-10" style="background:#fff5f5;border:1px solid #fed7d7;">
                                    <span class="font-15 fw-medium"><i class="ri-close-circle-line me-2" style="color:#e53e3e;"></i>{{ __('Failed') }}</span>
                                    <strong style="color:#e53e3e;font-size:1.25rem;">{{ $failedResolutions }}</strong>
                                </div>
                                <div class="d-flex align-items-center justify-content-between p-15 radius-4" style="background:#fffbeb;border:1px solid #fbd38d;">
                                    <span class="font-15 fw-medium"><i class="ri-time-line me-2 orange-color"></i>{{ __('Open') }}</span>
                                    <strong class="orange-color font-20">{{ $openResolutions }}</strong>
                                </div>
                            </div>

                            {{-- Recent Audit Logs --}}
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <h5 class="mb-15"><i class="ri-file-list-line me-2"></i>{{ __('Recent Audit Logs') }}</h5>
                                @forelse ($recentAuditLogs as $log)
                                    <div class="d-flex align-items-start py-2 border-bottom">
                                        <i class="ri-shield-check-line me-2 mt-1 primary-color"></i>
                                        <div>
                                            <p class="mb-0 font-14">
                                                <strong>{{ $log->actor_name ?? '—' }}</strong>
                                                — {{ str_replace('_', ' ', ucfirst($log->action ?? '—')) }}
                                            </p>
                                            <small class="text-muted">{{ $log->created_at ? $log->created_at->diffForHumans() : '—' }}</small>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-center text-muted mt-2">{{ __('No audit logs') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    {{-- End Row 2 --}}

                    {{-- Quick Actions --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <h5 class="mb-15"><i class="ri-flashlight-line me-2"></i>{{ __('Quick Actions') }}</h5>
                                <div class="d-flex flex-wrap gap-2">
                                    @if (Route::has('owner.governance.documents.create'))
                                        <a href="{{ route('owner.governance.documents.create') }}" class="theme-btn">
                                            <i class="ri-upload-2-line me-1"></i>{{ __('Upload Document') }}
                                        </a>
                                    @else
                                        <button class="theme-btn" disabled><i class="ri-upload-2-line me-1"></i>{{ __('Upload Document') }}</button>
                                    @endif
                                    @if (Route::has('owner.financial-audit-log.index'))
                                        <a href="{{ route('owner.financial-audit-log.index') }}" class="theme-btn-outline">
                                            <i class="ri-file-list-3-line me-1"></i>{{ __('View Audit Trail') }}
                                        </a>
                                    @endif
                                    @if (Route::has('accountant.reports.index'))
                                        <a href="{{ route('accountant.reports.index') }}" class="theme-btn-outline">
                                            <i class="ri-bar-chart-2-line me-1"></i>{{ __('Compliance Report') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
