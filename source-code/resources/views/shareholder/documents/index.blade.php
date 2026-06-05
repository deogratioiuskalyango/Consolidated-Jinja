@extends('shareholder.layouts.app')

@push('style')
<style>
    /* Card hover lift */
    .doc-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(0,0,0,.08);
    }
    .doc-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,.12) !important;
    }

    /* Colored top band per document type */
    .doc-band {
        height: 5px;
        border-radius: 0.375rem 0.375rem 0 0;
    }
    .doc-band--annual-report      { background: linear-gradient(90deg, #4361ee, #7b8cde); }
    .doc-band--financial-statement { background: linear-gradient(90deg, #2ec4b6, #3a9d97); }
    .doc-band--meeting-minutes     { background: linear-gradient(90deg, #ff9f1c, #ffbf69); }
    .doc-band--policy              { background: linear-gradient(90deg, #e63946, #f08080); }
    .doc-band--circular            { background: linear-gradient(90deg, #8338ec, #b07fe8); }
    .doc-band--notice              { background: linear-gradient(90deg, #06d6a0, #63e6be); }
    .doc-band--resolution          { background: linear-gradient(90deg, #f77f00, #fcbf49); }
    .doc-band--prospectus          { background: linear-gradient(90deg, #0077b6, #90e0ef); }
    .doc-band--default             { background: linear-gradient(90deg, #6c757d, #adb5bd); }

    /* Version pill */
    .version-pill {
        font-size: .7rem;
        letter-spacing: .03em;
    }

    /* Filter card */
    .filter-card .form-control,
    .filter-card .form-select {
        font-size: .875rem;
    }

    /* Empty state */
    .empty-state-icon {
        font-size: 4rem;
        line-height: 1;
    }

    /* Pagination alignment */
    .pagination-wrapper nav {
        display: flex;
        justify-content: center;
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">

                {{-- Page Header --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="page-title-box d-flex flex-column flex-sm-row align-items-sm-center justify-content-between g-20">
                            <div class="page-title-left">
                                <h2 class="mb-1">
                                    <i class="ri-file-shield-2-line me-2 text-primary"></i>
                                    {{ __('Company Documents') }}
                                </h2>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('shareholder.dashboard') }}" class="text-decoration-none">
                                                <i class="ri-home-4-line me-1"></i>{{ __('Dashboard') }}
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">{{ __('Company Documents') }}</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filter Bar --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm filter-card">
                            <div class="card-body py-3">
                                <form method="GET" action="{{ request()->url() }}" class="row g-2 align-items-end">
                                    <div class="col-12 col-sm-5 col-lg-5">
                                        <label class="form-label small fw-semibold text-muted mb-1">
                                            <i class="ri-search-line me-1"></i>{{ __('Search') }}
                                        </label>
                                        <input
                                            type="text"
                                            name="search"
                                            value="{{ request('search') }}"
                                            class="form-control"
                                            placeholder="{{ __('Search by title…') }}"
                                        >
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-4">
                                        <label class="form-label small fw-semibold text-muted mb-1">
                                            <i class="ri-folder-2-line me-1"></i>{{ __('Document Type') }}
                                        </label>
                                        <select name="type" class="form-select">
                                            <option value="">{{ __('All Types') }}</option>
                                            @foreach($docTypes as $type)
                                                <option value="{{ $type }}" @selected(request('type') === $type)>
                                                    {{ ucwords(str_replace(['-','_'], ' ', $type)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-3 col-lg-3 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary flex-fill">
                                            <i class="ri-filter-3-line me-1"></i>{{ __('Filter') }}
                                        </button>
                                        @if(request('search') || request('type'))
                                            <a href="{{ request()->url() }}" class="btn btn-outline-secondary" title="{{ __('Clear filters') }}">
                                                <i class="ri-close-line"></i>
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Document Grid --}}
                @if($documents->isEmpty())
                    {{-- Empty State --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="text-center py-5 my-3">
                                <div class="empty-state-icon text-muted mb-3">
                                    <i class="ri-file-shield-2-line"></i>
                                </div>
                                <h5 class="fw-semibold text-muted mb-1">{{ __('No documents available.') }}</h5>
                                <p class="text-muted small mb-0">
                                    {{ __('There are no documents matching your criteria. Try adjusting your filters.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                        @foreach($documents as $doc)
                            @php
                                // Status badge
                                $statusBadge = match((int) $doc->status) {
                                    0 => ['label' => 'Draft',   'class' => 'bg-secondary'],
                                    1 => ['label' => 'Active',  'class' => 'bg-success'],
                                    2 => ['label' => 'Expired', 'class' => 'bg-danger'],
                                    default => ['label' => 'Unknown', 'class' => 'bg-secondary'],
                                };

                                // Color band CSS class based on document_type
                                $typeSlug = strtolower(str_replace([' ', '_'], '-', $doc->document_type ?? ''));
                                $bandMap = [
                                    'annual-report'       => 'doc-band--annual-report',
                                    'financial-statement' => 'doc-band--financial-statement',
                                    'meeting-minutes'     => 'doc-band--meeting-minutes',
                                    'policy'              => 'doc-band--policy',
                                    'circular'            => 'doc-band--circular',
                                    'notice'              => 'doc-band--notice',
                                    'resolution'          => 'doc-band--resolution',
                                    'prospectus'          => 'doc-band--prospectus',
                                ];
                                $bandClass = $bandMap[$typeSlug] ?? 'doc-band--default';
                            @endphp

                            <div class="col">
                                <div class="card h-100 doc-card shadow-sm">

                                    {{-- Colored top band --}}
                                    <div class="doc-band {{ $bandClass }}"></div>

                                    <div class="card-body d-flex flex-column gap-2 pt-3">

                                        {{-- Status + lock icon row --}}
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <span class="badge {{ $statusBadge['class'] }} px-2 py-1">
                                                {{ __($statusBadge['label']) }}
                                            </span>
                                            @if($doc->requires_signature)
                                                <span class="text-warning" title="{{ __('Signature required') }}">
                                                    <i class="ri-lock-line"></i>
                                                    <span class="small">{{ __('Signature Required') }}</span>
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Title --}}
                                        <h6 class="fw-semibold mb-0 lh-sm">{{ $doc->title }}</h6>

                                        {{-- Document type + version --}}
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <small class="text-muted">
                                                <i class="ri-folder-2-line me-1"></i>
                                                {{ ucwords(str_replace(['-','_'], ' ', $doc->document_type ?? __('N/A'))) }}
                                            </small>
                                            <span class="badge bg-light text-dark border version-pill">
                                                v{{ $doc->version ?? '1.0' }}
                                            </span>
                                        </div>

                                        {{-- Description --}}
                                        @if($doc->description)
                                            <p class="text-muted small mb-0 lh-sm" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                                {{ $doc->description }}
                                            </p>
                                        @endif

                                        {{-- Expiry date --}}
                                        <div class="small text-muted">
                                            <i class="ri-calendar-event-line me-1"></i>
                                            @if($doc->expiry_date)
                                                <span class="{{ (int)$doc->status === 2 ? 'text-danger fw-semibold' : '' }}">
                                                    {{ __('Expires') }}: {{ \Carbon\Carbon::parse($doc->expiry_date)->format('M d, Y') }}
                                                </span>
                                            @else
                                                {{ __('No expiry') }}
                                            @endif
                                        </div>

                                        {{-- All shareholders tag --}}
                                        @if($doc->all_shareholders)
                                            <div class="small text-success">
                                                <i class="ri-checkbox-circle-line me-1"></i>{{ __('All Shareholders') }}
                                            </div>
                                        @endif

                                    </div>{{-- /card-body --}}

                                    {{-- Card footer --}}
                                    <div class="card-footer bg-transparent border-top d-flex align-items-center justify-content-between py-2 px-3">
                                        {{-- Stats --}}
                                        <div class="d-flex gap-3 text-muted small">
                                            <span title="{{ __('Views') }}">
                                                <i class="ri-eye-line me-1"></i>{{ number_format($doc->view_count ?? 0) }}
                                            </span>
                                            <span title="{{ __('Downloads') }}">
                                                <i class="ri-download-line me-1"></i>{{ number_format($doc->download_count ?? 0) }}
                                            </span>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('shareholder.documents.show', $doc) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="{{ __('View Document') }}">
                                                <i class="ri-eye-line me-1"></i>{{ __('View') }}
                                            </a>
                                            <a href="{{ route('shareholder.documents.download', $doc) }}"
                                               class="btn btn-sm btn-outline-secondary"
                                               title="{{ __('Download Document') }}">
                                                <i class="ri-download-line"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-success doc-share-btn"
                                                    title="{{ __('Share') }}"
                                                    data-title="{{ e($doc->title) }}"
                                                    data-type="{{ e($doc->document_type) }}"
                                                    data-desc="{{ e($doc->description) }}"
                                                    data-url="{{ route('shareholder.documents.show', $doc) }}">
                                                <i class="ri-share-forward-2-line"></i>
                                            </button>
                                        </div>
                                    </div>

                                </div>{{-- /card --}}
                            </div>{{-- /col --}}
                        @endforeach
                    </div>{{-- /row --}}

                    {{-- Pagination --}}
                    @if($documents->hasPages())
                        <div class="pagination-wrapper mt-4">
                            {{ $documents->appends(request()->query())->links() }}
                        </div>
                    @endif

                @endif

            </div>{{-- /page-content-wrapper --}}
        </div>
    </div>
</div>

@include('components.share-modal')

@endsection

@push('script')
<script src="{{ asset('assets/js/custom/share-utils.js') }}"></script>
<script>
$(document).on('click', '.doc-share-btn', function () {
    var title = $(this).data('title');
    var type  = $(this).data('type');
    var desc  = $(this).data('desc');
    var url   = $(this).data('url') || window.location.href;
    var text  = (type ? type + '\n' : '') + (desc || '');
    openShareModal(title, text, url);
});
</script>
@endpush
