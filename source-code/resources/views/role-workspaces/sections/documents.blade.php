@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-file-shield-2-line me-2 text-primary"></i>{{ __('Governance Documents') }}</h4>
                    <small class="text-muted">{{ __('All governance documents in your workspace') }}</small>
                </div>
                <a href="{{ route('role.' . str_replace('_', '-', $role) . '.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i>{{ __('Dashboard') }}
                </a>
            </div>

            @if($records->isEmpty())
            <div class="text-center py-5">
                <i class="ri-file-shield-2-line d-block fs-1 text-muted mb-2"></i>
                <h5 class="text-muted">{{ __('No governance documents') }}</h5>
                <p class="text-muted small">{{ __('Governance documents will appear here once uploaded.') }}</p>
            </div>
            @else
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ __('All Documents') }}</h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary">{{ $totalCount }} {{ __('total') }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="padding:10px 16px;">{{ __('Title') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Uploaded') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($records as $doc)
                                @php
                                    $s = strtolower($doc->status ?? 'active');
                                    $sBadge = match($s) {
                                        'active', 'approved' => 'bg-success',
                                        'draft'              => 'bg-secondary',
                                        'expired'            => 'bg-danger',
                                        default              => 'bg-primary',
                                    };
                                @endphp
                                <tr>
                                    <td style="padding:10px 16px;"><strong class="small">{{ Str::limit($doc->title ?? $doc->name ?? '—', 50) }}</strong></td>
                                    <td class="text-muted small">{{ str_replace('_', ' ', ucfirst($doc->document_type ?? '—')) }}</td>
                                    <td><span class="badge {{ $sBadge }}" style="font-size:10px;">{{ ucfirst($s) }}</span></td>
                                    <td class="text-muted small">{{ $doc->created_at?->format('d M Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($records->hasPages())
                    <div class="px-4 py-3 border-top">{{ $records->links() }}</div>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
