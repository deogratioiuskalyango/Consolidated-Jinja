@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between border-bottom mb-20">
                            <div class="page-title-left">
                                <h3 class="mb-sm-0">{{ $pageTitle }}</h3>
                            </div>
                            <div class="page-title-right">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                    <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filter Bar --}}
                <div class="row mb-3">
                    <div class="col-12">
                        <form method="GET" action="{{ route('admin.governance.audit-logs') }}" class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Action Type') }}</label>
                                <select name="action" class="form-select">
                                    <option value="">{{ __('All Actions') }}</option>
                                    <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>{{ __('Login') }}</option>
                                    <option value="logout" {{ request('action') === 'logout' ? 'selected' : '' }}>{{ __('Logout') }}</option>
                                    <option value="vote" {{ request('action') === 'vote' ? 'selected' : '' }}>{{ __('Vote') }}</option>
                                    <option value="approve" {{ request('action') === 'approve' ? 'selected' : '' }}>{{ __('Approve') }}</option>
                                    <option value="reject" {{ request('action') === 'reject' ? 'selected' : '' }}>{{ __('Reject') }}</option>
                                    <option value="document_view" {{ request('action') === 'document_view' ? 'selected' : '' }}>{{ __('Document View') }}</option>
                                    <option value="document_download" {{ request('action') === 'document_download' ? 'selected' : '' }}>{{ __('Document Download') }}</option>
                                    <option value="profile_update" {{ request('action') === 'profile_update' ? 'selected' : '' }}>{{ __('Profile Update') }}</option>
                                    <option value="password_change" {{ request('action') === 'password_change' ? 'selected' : '' }}>{{ __('Password Change') }}</option>
                                    <option value="share_transfer" {{ request('action') === 'share_transfer' ? 'selected' : '' }}>{{ __('Share Transfer') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('From Date') }}</label>
                                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('To Date') }}</label>
                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ri-search-line me-1"></i> {{ __('Filter') }}
                                </button>
                                <a href="{{ route('admin.governance.audit-logs') }}" class="btn btn-secondary">
                                    <i class="ri-refresh-line"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="auditLogsDataTable" class="table table-bordered table-striped align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('SL') }}</th>
                                        <th>{{ __('Action') }}</th>
                                        <th>{{ __('Shareholder') }}</th>
                                        <th>{{ __('IP Address') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th>{{ __('Metadata') }}</th>
                                        <th>{{ __('Created At') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($logs as $index => $log)
                                    <tr>
                                        <td>{{ $logs->firstItem() + $index }}</td>
                                        <td>
                                            @php
                                                $actionClasses = [
                                                    'login'             => 'bg-success',
                                                    'logout'            => 'bg-secondary',
                                                    'vote'              => 'bg-primary',
                                                    'approve'           => 'bg-success',
                                                    'reject'            => 'bg-danger',
                                                    'document_view'     => 'bg-info',
                                                    'document_download' => 'bg-info',
                                                    'profile_update'    => 'bg-warning text-dark',
                                                    'password_change'   => 'bg-warning text-dark',
                                                    'share_transfer'    => 'bg-dark',
                                                ];
                                                $actionClass = $actionClasses[$log->action] ?? 'bg-secondary';
                                            @endphp
                                            <span class="badge {{ $actionClass }}">{{ str_replace('_', ' ', ucfirst($log->action)) }}</span>
                                        </td>
                                        <td>{{ $log->shareholder ? $log->shareholder->full_name : '—' }}</td>
                                        <td><code>{{ $log->ip_address ?? '—' }}</code></td>
                                        <td>{{ $log->description ?? '—' }}</td>
                                        <td>
                                            @if($log->metadata)
                                                <span title="{{ is_string($log->metadata) ? $log->metadata : json_encode($log->metadata) }}" style="cursor: help;">
                                                    {{ Str::limit(is_string($log->metadata) ? $log->metadata : json_encode($log->metadata), 60) }}
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $logs->appends(request()->query())->links() }}
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
    <script src="{{ asset('assets/js/custom/governance-audit-logs.js') }}"></script>
@endpush
