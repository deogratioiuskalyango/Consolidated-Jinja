@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-megaphone-line me-2 text-primary"></i>{{ __('Notice Board') }}</h4>
                    <small class="text-muted">{{ __('All notices published in your workspace') }}</small>
                </div>
                <a href="{{ route('role.' . str_replace('_', '-', $role) . '.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i>{{ __('Dashboard') }}
                </a>
            </div>

            @if($records->isEmpty())
            <div class="text-center py-5">
                <i class="ri-megaphone-line d-block fs-1 text-muted mb-2"></i>
                <h5 class="text-muted">{{ __('No notices found') }}</h5>
                <p class="text-muted small">{{ __('Notices published for tenants will appear here.') }}</p>
            </div>
            @else
            <div class="row g-4">
                @foreach($records as $notice)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div style="width:36px;height:36px;border-radius:8px;background:#ede9fe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="ri-megaphone-line text-primary"></i>
                                </div>
                                <h6 class="mb-0 fw-semibold flex-grow-1 text-truncate">{{ $notice->title }}</h6>
                            </div>
                            @if($notice->details)
                            <p class="text-muted small mb-2">{{ Str::limit($notice->details, 100) }}</p>
                            @endif
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    @if($notice->property_all)
                                        <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:10px;">{{ __('All Properties') }}</span>
                                    @elseif($notice->unit_all)
                                        <span class="badge bg-info bg-opacity-10 text-info" style="font-size:10px;">{{ __('All Units') }}</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size:10px;">{{ __('Specific') }}</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $notice->created_at?->format('d M Y') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($records->hasPages())
            <div class="mt-4">{{ $records->links() }}</div>
            @endif
            @endif

        </div>
    </div>
</div>
@endsection
