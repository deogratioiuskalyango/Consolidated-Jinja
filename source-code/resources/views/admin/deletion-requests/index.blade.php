@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            {{-- Page header --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between border-bottom pb-3">
                        <h4 class="mb-0">{{ $pageTitle }}</h4>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                        </ol>
                    </div>
                </div>
            </div>

            {{-- Status filter tabs --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('admin.deletion-requests.index', ['status' => 'pending']) }}"
                           class="btn {{ $activeStatus === 'pending' ? 'btn-warning' : 'btn-outline-warning' }} btn-sm">
                            <i class="ri-time-line me-1"></i>{{ __('Pending') }}
                            <span class="badge bg-dark ms-1">{{ $pendingCount }}</span>
                        </a>
                        <a href="{{ route('admin.deletion-requests.index', ['status' => 'approved']) }}"
                           class="btn {{ $activeStatus === 'approved' ? 'btn-success' : 'btn-outline-success' }} btn-sm">
                            <i class="ri-checkbox-circle-line me-1"></i>{{ __('Approved') }}
                            <span class="badge bg-dark ms-1">{{ $approvedCount }}</span>
                        </a>
                        <a href="{{ route('admin.deletion-requests.index', ['status' => 'rejected']) }}"
                           class="btn {{ $activeStatus === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }} btn-sm">
                            <i class="ri-close-circle-line me-1"></i>{{ __('Rejected') }}
                            <span class="badge bg-dark ms-1">{{ $rejectedCount }}</span>
                        </a>
                        <a href="{{ route('admin.deletion-requests.index') }}"
                           class="btn {{ !in_array($activeStatus, ['pending','approved','rejected']) ? 'btn-primary' : 'btn-outline-secondary' }} btn-sm">
                            <i class="ri-list-check me-1"></i>{{ __('All') }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="row">
                <div class="col-12">
                    <div class="card" style="border-radius:12px;border:1px solid var(--t-border,#e2e8f0);">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead style="background:var(--t-bg-surface,#f8fafc);border-bottom:1px solid var(--t-border,#e2e8f0);">
                                        <tr>
                                            <th class="ps-4">#</th>
                                            <th>{{ __('User') }}</th>
                                            <th>{{ __('Role') }}</th>
                                            <th>{{ __('Reason') }}</th>
                                            <th>{{ __('Requested') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Reviewed By') }}</th>
                                            <th>{{ __('Admin Note') }}</th>
                                            <th class="text-center">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($requests as $req)
                                        <tr>
                                            <td class="ps-4 text-muted" style="font-size:12px;">{{ $req->id }}</td>
                                            <td>
                                                @if ($req->user)
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="avatar-xs flex-shrink-0">
                                                            <span class="avatar-title rounded-circle bg-primary text-white" style="font-size:12px;">
                                                                {{ strtoupper(substr($req->user->first_name ?? '?', 0, 1)) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <div style="font-weight:600;font-size:13.5px;">
                                                                {{ $req->user->first_name }} {{ $req->user->last_name }}
                                                            </div>
                                                            <div style="font-size:11.5px;color:var(--t-text-muted,#64748b);">
                                                                {{ $req->user->email }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">{{ __('Deleted user') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($req->user)
                                                    @php
                                                        $roleName = match($req->user->role) {
                                                            USER_ROLE_TENANT     => 'Tenant',
                                                            USER_ROLE_MAINTAINER => 'Maintainer',
                                                            default              => 'User',
                                                        };
                                                    @endphp
                                                    <span class="badge bg-info bg-opacity-15 text-info" style="font-size:11px;">{{ $roleName }}</span>
                                                @endif
                                            </td>
                                            <td style="max-width:180px;">
                                                <span style="font-size:12.5px;color:var(--t-text-muted,#64748b);">
                                                    {{ $req->reason ? Str::limit($req->reason, 60) : '—' }}
                                                </span>
                                            </td>
                                            <td style="font-size:12.5px;white-space:nowrap;">
                                                {{ $req->created_at->format('M d, Y') }}<br>
                                                <span style="font-size:11px;color:var(--t-text-muted,#64748b);">{{ $req->created_at->diffForHumans() }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $req->statusBadgeClass() }}">
                                                    {{ $req->statusLabel() }}
                                                </span>
                                            </td>
                                            <td style="font-size:12.5px;">
                                                {{ $req->reviewer ? $req->reviewer->first_name . ' ' . $req->reviewer->last_name : '—' }}
                                                @if ($req->reviewed_at)
                                                    <div style="font-size:11px;color:var(--t-text-muted,#64748b);">{{ $req->reviewed_at->format('M d, Y') }}</div>
                                                @endif
                                            </td>
                                            <td style="max-width:160px;font-size:12px;color:var(--t-text-muted,#64748b);">
                                                {{ $req->admin_note ? Str::limit($req->admin_note, 50) : '—' }}
                                            </td>
                                            <td class="text-center">
                                                @if ($req->status === DELETION_REQUEST_PENDING)
                                                <div class="d-flex gap-1 justify-content-center">
                                                    {{-- Approve --}}
                                                    <button type="button"
                                                        class="btn btn-sm btn-success"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#approveModal{{ $req->id }}"
                                                        title="{{ __('Approve') }}">
                                                        <i class="ri-checkbox-circle-line"></i>
                                                    </button>
                                                    {{-- Reject --}}
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#rejectModal{{ $req->id }}"
                                                        title="{{ __('Reject') }}">
                                                        <i class="ri-close-circle-line"></i>
                                                    </button>
                                                </div>
                                                @else
                                                    <span class="text-muted" style="font-size:12px;">—</span>
                                                @endif
                                            </td>
                                        </tr>

                                        {{-- Approve Modal --}}
                                        @if ($req->status === DELETION_REQUEST_PENDING)
                                        <div class="modal fade" id="approveModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title text-success">
                                                            <i class="ri-checkbox-circle-line me-2"></i>{{ __('Approve Deletion') }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form class="ajax" method="POST"
                                                          action="{{ route('admin.deletion-requests.approve', $req->id) }}"
                                                          data-handler="getShowMessage">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="alert alert-warning mb-3">
                                                                <i class="ri-error-warning-line me-2"></i>
                                                                {{ __('This will permanently mark the account as deleted. This cannot be undone.') }}
                                                            </div>
                                                            <p class="mb-2">
                                                                <strong>{{ __('User:') }}</strong>
                                                                {{ optional($req->user)->first_name }} {{ optional($req->user)->last_name }}
                                                                ({{ optional($req->user)->email }})
                                                            </p>
                                                            <div class="mb-3">
                                                                <label class="form-label">{{ __('Admin Note') }} <span class="text-muted">({{ __('optional') }})</span></label>
                                                                <textarea name="admin_note" class="form-control" rows="2"
                                                                    placeholder="{{ __('Internal note about this approval...') }}"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="ri-checkbox-circle-line me-1"></i>{{ __('Confirm Approval') }}
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Reject Modal --}}
                                        <div class="modal fade" id="rejectModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title text-danger">
                                                            <i class="ri-close-circle-line me-2"></i>{{ __('Reject Deletion Request') }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form class="ajax" method="POST"
                                                          action="{{ route('admin.deletion-requests.reject', $req->id) }}"
                                                          data-handler="getShowMessage">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>{{ __('The account will be restored to Active status and the user will be notified.') }}</p>
                                                            <p class="mb-2">
                                                                <strong>{{ __('User:') }}</strong>
                                                                {{ optional($req->user)->first_name }} {{ optional($req->user)->last_name }}
                                                                ({{ optional($req->user)->email }})
                                                            </p>
                                                            <div class="mb-3">
                                                                <label class="form-label">
                                                                    {{ __('Reason for Rejection') }} <span class="text-danger">*</span>
                                                                </label>
                                                                <textarea name="admin_note" class="form-control" rows="3" required
                                                                    placeholder="{{ __('Explain why the deletion request was rejected...') }}"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="ri-close-circle-line me-1"></i>{{ __('Reject Request') }}
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5">
                                                <i class="ri-user-unfollow-line" style="font-size:42px;opacity:.3;display:block;margin-bottom:10px;"></i>
                                                <p class="text-muted mb-0">{{ __('No deletion requests found.') }}</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination --}}
                            @if ($requests->hasPages())
                                <div class="p-3 border-top">
                                    {{ $requests->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
