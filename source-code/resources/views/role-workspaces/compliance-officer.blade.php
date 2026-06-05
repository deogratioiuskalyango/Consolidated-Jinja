@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-file-shield-2-line me-2 text-primary"></i>{{ __('Compliance Officer Dashboard') }}</h4>
                    <small class="text-muted">{{ __('Governance risk console') }}</small>
                </div>
            </div>

            <div class="sh-stat-grid mb-4">
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#6366f1;"><i class="ri-file-list-3-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Governance Documents') }}</div>
                        <div class="sh-stat-value">{{ number_format($totalDocuments) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#3b82f6;"><i class="ri-discuss-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Total Resolutions') }}</div>
                        <div class="sh-stat-value">{{ number_format($totalResolutions) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#10b981;"><i class="ri-checkbox-circle-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Passed') }}</div>
                        <div class="sh-stat-value text-success">{{ number_format($passedResolutions) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#ef4444;"><i class="ri-close-circle-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Failed') }}</div>
                        <div class="sh-stat-value {{ $failedResolutions > 0 ? 'text-danger' : '' }}">{{ number_format($failedResolutions) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#f59e0b;"><i class="ri-alert-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Open Resolutions') }}</div>
                        <div class="sh-stat-value {{ $openResolutions > 0 ? 'text-warning' : '' }}">{{ number_format($openResolutions) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#8b5cf6;"><i class="ri-user-smile-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Active Tenants') }}</div>
                        <div class="sh-stat-value">{{ number_format($activeTenants) }}</div>
                    </div>
                </div>
            </div>

            {{-- Compliance Alerts --}}
            @if(!empty($complianceAlerts))
            <div class="mb-4">
                @foreach($complianceAlerts as $alert)
                @php
                    $alertBg    = match($alert['type'] ?? 'warning') { 'danger' => 'bg-danger bg-opacity-10 border-danger', 'success' => 'bg-success bg-opacity-10 border-success', default => 'bg-warning bg-opacity-10 border-warning' };
                    $alertText  = match($alert['type'] ?? 'warning') { 'danger' => 'text-danger', 'success' => 'text-success', default => 'text-warning' };
                    $alertIcon  = match($alert['type'] ?? 'warning') { 'danger' => 'ri-error-warning-line', 'success' => 'ri-checkbox-circle-line', default => 'ri-alert-line' };
                @endphp
                <div class="d-flex align-items-center gap-2 px-3 py-2 rounded border {{ $alertBg }} mb-2" style="font-size:13px;">
                    <i class="{{ $alertIcon }} {{ $alertText }} flex-shrink-0"></i>
                    <span class="{{ $alertText }}">{{ $alert['message'] }}</span>
                </div>
                @endforeach
            </div>
            @endif

            <div class="row g-4">
                {{-- Recent Documents --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">{{ __('Governance Documents') }}</h5>
                            <a href="{{ route('role.workbench', [$role, 'documents']) }}" class="btn btn-sm btn-outline-primary" style="font-size:11px;">{{ __('All') }}</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" style="font-size:12px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="padding:8px 16px;">{{ __('Title') }}</th>
                                            <th>{{ __('Type') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentDocuments as $doc)
                                        @php
                                            $s = strtolower($doc->status ?? 'active');
                                            $sBadge = match($s) { 'active', 'approved' => 'bg-success', 'draft' => 'bg-secondary', 'expired' => 'bg-danger', default => 'bg-primary' };
                                        @endphp
                                        <tr>
                                            <td style="padding:8px 16px;">{{ Str::limit($doc->title ?? $doc->name ?? '—', 35) }}</td>
                                            <td class="text-muted small">{{ str_replace('_', ' ', ucfirst($doc->document_type ?? '—')) }}</td>
                                            <td><span class="badge {{ $sBadge }}" style="font-size:10px;">{{ ucfirst($s) }}</span></td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="3" class="text-center py-3 text-muted small">{{ __('No governance documents uploaded.') }}</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Resolution Health --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0">{{ __('Resolution Health') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span style="font-size:13px;">{{ __('Passed') }}</span>
                                <strong class="text-success fs-5">{{ $passedResolutions }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span style="font-size:13px;">{{ __('Failed') }}</span>
                                <strong class="text-danger fs-5">{{ $failedResolutions }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span style="font-size:13px;">{{ __('Open / Pending') }}</span>
                                <strong class="text-warning fs-5">{{ $openResolutions }}</strong>
                            </div>
                            @if($totalResolutions > 0)
                            @php $passRate = round(($passedResolutions / $totalResolutions) * 100); @endphp
                            <div>
                                <div class="d-flex justify-content-between mb-1" style="font-size:12px;">
                                    <span>{{ __('Pass Rate') }}</span>
                                    <span>{{ $passRate }}%</span>
                                </div>
                                <div class="progress" style="height:8px;border-radius:4px;">
                                    <div class="progress-bar {{ $passRate >= 70 ? 'bg-success' : ($passRate >= 40 ? 'bg-warning' : 'bg-danger') }}"
                                         style="width:{{ $passRate }}%;" role="progressbar"
                                         aria-valuenow="{{ $passRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Governance Audit Trail --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ __('Governance Audit Trail') }}</h5>
                    <a href="{{ route('role.workbench', [$role, 'audit-logs']) }}" class="btn btn-sm btn-outline-primary" style="font-size:11px;">{{ __('Full Trail') }}</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height:300px;overflow-y:auto;">
                        <table class="table table-hover mb-0" style="font-size:12px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="padding:8px 16px;">{{ __('Action') }}</th>
                                    <th>{{ __('User') }}</th>
                                    <th>{{ __('Details') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAuditLogs as $log)
                                <tr>
                                    <td style="padding:8px 16px;">
                                        <span class="badge bg-secondary" style="font-size:10px;">{{ str_replace('_', ' ', ucfirst($log->action ?? '—')) }}</span>
                                    </td>
                                    <td class="text-muted small">{{ $log->user?->name ?? '—' }}</td>
                                    <td>{{ Str::limit($log->description ?? (is_array($log->new_values) ? implode(', ', array_keys($log->new_values ?? [])) : '—'), 45) }}</td>
                                    <td class="text-muted small">{{ $log->created_at?->format('d M H:i') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-3 text-muted small">{{ __('No governance audit events recorded.') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
