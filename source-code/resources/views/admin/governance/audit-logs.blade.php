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
                                    <li class="breadcrumb-item"><a href="{{ route('admin.governance.resolutions.index') }}">{{ __('Governance') }}</a></li>
                                    <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filter Bar --}}
                <form method="GET" action="{{ route('admin.governance.audit-logs') }}" class="row g-2 align-items-end mb-25">
                    <div class="col-md-4">
                        <label class="label-text-title color-heading font-medium mb-1">{{ __('Action Type') }}</label>
                        <select name="action" class="form-control">
                            <option value="">{{ __('All Actions') }}</option>
                            @foreach([
                                'login'             => 'Login',
                                'logout'            => 'Logout',
                                'vote'              => 'Vote',
                                'approve'           => 'Approve',
                                'reject'            => 'Reject',
                                'document_view'     => 'Document View',
                                'document_download' => 'Document Download',
                                'profile_update'    => 'Profile Update',
                                'password_change'   => 'Password Change',
                                'share_transfer'    => 'Share Transfer',
                            ] as $val => $label)
                                <option value="{{ $val }}" {{ request('action') === $val ? 'selected' : '' }}>{{ __($label) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="label-text-title color-heading font-medium mb-1">{{ __('From Date') }}</label>
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="label-text-title color-heading font-medium mb-1">{{ __('To Date') }}</label>
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="theme-btn w-100">{{ __('Filter') }}</button>
                        <a href="{{ route('admin.governance.audit-logs') }}" class="theme-btn-back px-3">
                            <i class="ri-refresh-line"></i>
                        </a>
                    </div>
                </form>

                <div class="row">
                    <div class="billing-center-area bg-off-white theme-border radius-4 p-25">
                        <table class="table responsive theme-border">
                            <thead>
                                <th>{{ __('SL') }}</th>
                                <th>{{ __('Action') }}</th>
                                <th>{{ __('Shareholder') }}</th>
                                <th>{{ __('IP Address') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Metadata') }}</th>
                                <th>{{ __('Date') }}</th>
                            </thead>
                            <tbody>
                                @forelse($logs as $index => $log)
                                @php
                                    $actionClasses = [
                                        'login'             => 'badge bg-success',
                                        'logout'            => 'badge bg-secondary',
                                        'vote'              => 'badge bg-primary',
                                        'approve'           => 'badge bg-success',
                                        'reject'            => 'badge bg-danger',
                                        'document_view'     => 'badge bg-info',
                                        'document_download' => 'badge bg-info',
                                        'profile_update'    => 'badge bg-warning text-dark',
                                        'password_change'   => 'badge bg-warning text-dark',
                                        'share_transfer'    => 'badge bg-dark',
                                    ];
                                    $cls = $actionClasses[$log->action] ?? 'badge bg-secondary';
                                    $meta = is_string($log->metadata) ? $log->metadata : json_encode($log->metadata);
                                @endphp
                                <tr>
                                    <td>{{ $logs->firstItem() + $index }}</td>
                                    <td><span class="{{ $cls }}">{{ str_replace('_', ' ', ucfirst($log->action)) }}</span></td>
                                    <td>{{ $log->shareholder?->full_name ?? '—' }}</td>
                                    <td><code>{{ $log->ip_address ?? '—' }}</code></td>
                                    <td>{{ $log->description ?? '—' }}</td>
                                    <td>
                                        @if($meta)
                                            <span title="{{ $meta }}" style="cursor:help;">{{ Str::limit($meta, 60) }}</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">{{ __('No audit log entries found.') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
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
@endpush
