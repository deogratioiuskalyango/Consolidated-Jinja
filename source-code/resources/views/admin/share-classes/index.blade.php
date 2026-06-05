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

                @php
                    $classBadgeColors = [
                        'A' => 'bg-purple',
                        'B' => 'bg-primary',
                        'C' => 'bg-success',
                        'D' => 'bg-secondary',
                        'E' => 'bg-warning text-dark',
                        'F' => 'bg-danger',
                    ];
                    $classCardBorders = [
                        'A' => 'border-start border-4 border-purple',
                        'B' => 'border-start border-4 border-primary',
                        'C' => 'border-start border-4 border-success',
                        'D' => 'border-start border-4 border-secondary',
                        'E' => 'border-start border-4 border-warning',
                        'F' => 'border-start border-4 border-danger',
                    ];
                    $govLevelLabels = [1 => 'Standard', 2 => 'Elevated', 3 => 'Supreme'];
                    $govLevelColors = [1 => 'bg-info', 2 => 'bg-warning text-dark', 3 => 'bg-danger'];
                @endphp

                @if($shareClasses->isEmpty())
                    <div class="alert alert-info text-center py-5">
                        <i class="ri-information-line fs-2 mb-2 d-block"></i>
                        {{ __('No share classes have been configured yet.') }}
                    </div>
                @else
                    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                        @foreach($shareClasses as $sc)
                        @php
                            $code        = strtoupper($sc->class_code ?? 'A');
                            $badgeCls    = $classBadgeColors[$code] ?? 'bg-secondary';
                            $borderCls   = $classCardBorders[$code] ?? 'border-start border-4 border-secondary';
                            $allowedPerms = $sc->permissions->where('is_allowed', true)->count();
                            $totalPerms   = $sc->permissions->count();
                        @endphp
                        <div class="col">
                            <div class="card h-100 shadow-sm {{ $borderCls }}">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div>
                                            <span class="badge {{ $badgeCls }} fs-5 px-3 py-2 me-2">{{ $code }}</span>
                                            <span class="fw-semibold fs-6">{{ $sc->name }}</span>
                                        </div>
                                        @php $gl = $sc->governance_level ?? 1; @endphp
                                        <span class="badge {{ $govLevelColors[$gl] ?? 'bg-info' }} ms-2">
                                            {{ $govLevelLabels[$gl] ?? 'Standard' }}
                                        </span>
                                    </div>

                                    @if($sc->description)
                                        <p class="text-muted small mb-3">{{ Str::limit($sc->description, 100) }}</p>
                                    @endif

                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="bg-light rounded p-2 text-center">
                                                <div class="fw-bold text-primary fs-5">{{ number_format($sc->voting_multiplier ?? 1, 2) }}x</div>
                                                <div class="text-muted small">{{ __('Voting Multiplier') }}</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="bg-light rounded p-2 text-center">
                                                <div class="fw-bold text-success fs-5">{{ $sc->dividend_priority ?? '—' }}</div>
                                                <div class="text-muted small">{{ __('Dividend Priority') }}</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="bg-light rounded p-2 text-center">
                                                <div class="fw-bold text-info fs-5">{{ $sc->shareholders_count }}</div>
                                                <div class="text-muted small">{{ __('Shareholders') }}</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="bg-light rounded p-2 text-center">
                                                <div class="fw-bold fs-5">
                                                    @if($totalPerms > 0)
                                                        <span class="text-success">{{ $allowedPerms }}</span><span class="text-muted fs-6">/{{ $totalPerms }}</span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </div>
                                                <div class="text-muted small">{{ __('Permissions') }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-wrap gap-1 mb-3">
                                        @if($sc->voting_rights)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                                <i class="ri-check-line me-1"></i>{{ __('Voting') }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border">
                                                <i class="ri-close-line me-1"></i>{{ __('No Voting') }}
                                            </span>
                                        @endif
                                        @if($sc->dividend_rights)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                <i class="ri-check-line me-1"></i>{{ __('Dividends') }}
                                            </span>
                                        @endif
                                        @if($sc->is_transferable)
                                            <span class="badge bg-info-subtle text-info border border-info-subtle">
                                                <i class="ri-arrow-left-right-line me-1"></i>{{ __('Transferable') }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border">
                                                <i class="ri-lock-line me-1"></i>{{ __('Non-transferable') }}
                                            </span>
                                        @endif
                                        @if($sc->is_founder_class)
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                                <i class="ri-star-line me-1"></i>{{ __('Founder') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-top-0 pt-0 pb-3 px-3">
                                    <a href="{{ route('admin.share-classes.show', $sc) }}" class="btn btn-sm btn-primary w-100">
                                        <i class="ri-settings-3-line me-1"></i>{{ __('Manage') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .bg-purple { background-color: #6f42c1 !important; color: #fff; }
    .border-purple { border-color: #6f42c1 !important; }
    .bg-primary-subtle { background-color: #cfe2ff; }
    .bg-success-subtle { background-color: #d1e7dd; }
    .bg-info-subtle    { background-color: #cff4fc; }
    .bg-warning-subtle { background-color: #fff3cd; }
    .border-4 { border-width: 4px !important; }
</style>
@endpush
