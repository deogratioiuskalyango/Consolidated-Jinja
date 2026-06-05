@extends('shareholder.layouts.app')

@push('style')
<style>
    .doc-info-grid .info-cell {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: background 0.15s;
    }
    .doc-info-grid .info-cell:hover {
        background: #e9ecef;
    }
    .doc-info-grid .info-cell small {
        font-size: 0.72rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .doc-info-grid .info-cell strong {
        font-size: 0.95rem;
    }
    .preview-wrapper {
        background: #f1f3f5;
        border-radius: 12px;
        overflow: hidden;
    }
    .preview-placeholder {
        min-height: 220px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }
    .stats-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.9rem;
    }
    .stats-row:last-child {
        border-bottom: none;
    }
    .stats-row .stat-count {
        margin-left: auto;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">

                @php
                    $statusMap = [
                        0 => ['label' => 'Draft',   'class' => 'bg-secondary'],
                        1 => ['label' => 'Active',  'class' => 'bg-success'],
                        2 => ['label' => 'Expired', 'class' => 'bg-danger'],
                    ];
                    $st = $statusMap[$document->status] ?? ['label' => 'Unknown', 'class' => 'bg-secondary'];

                    $ext = '';
                    if ($document->file) {
                        $ext = strtolower(pathinfo($document->file->file_name, PATHINFO_EXTENSION));
                    }
                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    $isPdf   = $ext === 'pdf';
                @endphp

                {{-- Page Header --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <nav aria-label="breadcrumb" class="mb-2">
                            <ol class="breadcrumb mb-0" style="font-size:0.82rem;">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('shareholder.documents.index') }}" class="text-decoration-none text-muted">
                                        {{ __('Dashboard') }}
                                    </a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('shareholder.documents.index') }}" class="text-decoration-none text-muted">
                                        {{ __('Documents') }}
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-truncate" style="max-width:260px;" aria-current="page">
                                    {{ $document->title }}
                                </li>
                            </ol>
                        </nav>
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h2 class="mb-0 fw-bold">{{ $document->title }}</h2>
                                <span class="badge {{ $st['class'] }} fs-6 fw-normal px-2 py-1">
                                    {{ __($st['label']) }}
                                </span>
                            </div>
                            <div class="d-flex gap-2 flex-shrink-0">
                                <a href="{{ route('shareholder.documents.index') }}"
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="ri-arrow-left-line me-1"></i>{{ __('Back') }}
                                </a>
                                @if($document->file)
                                <a href="{{ route('shareholder.documents.download', $document) }}"
                                   class="btn btn-primary btn-sm">
                                    <i class="ri-download-2-line me-1"></i>{{ __('Download') }}
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- File Preview Panel --}}
                @if($document->file)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="preview-wrapper p-2">
                            @if($isPdf)
                                <iframe src="{{ $document->file->FileUrl }}"
                                        style="width:100%; height:500px; border:0; border-radius:8px;"></iframe>
                            @elseif($isImage)
                                <img src="{{ $document->file->FileUrl }}"
                                     class="img-fluid rounded"
                                     style="max-height:500px; width:100%; object-fit:contain;"
                                     alt="{{ $document->title }}">
                            @else
                                <div class="preview-placeholder py-5">
                                    <i class="ri-file-3-line" style="font-size:4rem; color:#6c757d;"></i>
                                    <p class="mb-0 text-muted fw-semibold">{{ $document->file->file_name }}</p>
                                    <a href="{{ route('shareholder.documents.download', $document) }}"
                                       class="btn btn-primary mt-2">
                                        <i class="ri-download-2-line me-2"></i>{{ __('Download File') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- Two-column layout --}}
                <div class="row g-4">

                    {{-- LEFT: Document Details --}}
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
                                <i class="ri-file-text-line fs-5 text-primary"></i>
                                <h5 class="mb-0 fw-semibold">{{ __('Document Details') }}</h5>
                            </div>
                            <div class="card-body">

                                {{-- Badge row --}}
                                <div class="d-flex flex-wrap gap-2 mb-4">
                                    <span class="badge bg-secondary px-2 py-1">
                                        {{ $document->document_type }}
                                    </span>
                                    <span class="badge rounded-pill bg-light text-dark border px-2 py-1">
                                        v{{ $document->version ?? '1.0' }}
                                    </span>
                                    @if($document->requires_signature)
                                        <span class="badge bg-warning text-dark px-2 py-1">
                                            <i class="ri-lock-line me-1"></i>{{ __('Signature Required') }}
                                        </span>
                                    @endif
                                    @if($document->all_shareholders)
                                        <span class="badge bg-info px-2 py-1">
                                            <i class="ri-group-line me-1"></i>{{ __('All Shareholders') }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Info grid --}}
                                <div class="row g-2 doc-info-grid">
                                    <div class="col-sm-6">
                                        <div class="info-cell">
                                            <small class="text-muted d-block mb-1">{{ __('Document Type') }}</small>
                                            <strong>{{ $document->document_type ?? '-' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="info-cell">
                                            <small class="text-muted d-block mb-1">{{ __('Version') }}</small>
                                            <strong>v{{ $document->version ?? '1.0' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="info-cell">
                                            <small class="text-muted d-block mb-1">{{ __('Expiry Date') }}</small>
                                            <strong>
                                                {{ $document->expiry_date
                                                    ? \Carbon\Carbon::parse($document->expiry_date)->format('M d, Y')
                                                    : __('No expiry') }}
                                            </strong>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="info-cell">
                                            <small class="text-muted d-block mb-1">{{ __('Upload Date') }}</small>
                                            <strong>{{ $document->created_at->format('M d, Y') }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="info-cell">
                                            <small class="text-muted d-block mb-1">{{ __('Uploaded By') }}</small>
                                            <strong>
                                                @if($document->uploadedBy)
                                                    {{ trim($document->uploadedBy->first_name . ' ' . $document->uploadedBy->last_name) }}
                                                @else
                                                    -
                                                @endif
                                            </strong>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="info-cell">
                                            <small class="text-muted d-block mb-1">{{ __('Views') }}</small>
                                            <strong>{{ number_format($document->view_count) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="info-cell">
                                            <small class="text-muted d-block mb-1">{{ __('Downloads') }}</small>
                                            <strong>{{ number_format($document->download_count) }}</strong>
                                        </div>
                                    </div>
                                </div>

                                {{-- Description --}}
                                @if($document->description)
                                    <hr class="my-4">
                                    <div>
                                        <label class="form-label fw-semibold text-muted small text-uppercase" style="letter-spacing:.05em;">
                                            {{ __('Description') }}
                                        </label>
                                        <p class="mb-0" style="line-height:1.7;">{{ $document->description }}</p>
                                    </div>
                                @endif

                                {{-- Related To --}}
                                @if($document->meeting || $document->resolution)
                                    <hr class="my-4">
                                    <div>
                                        <label class="form-label fw-semibold text-muted small text-uppercase" style="letter-spacing:.05em;">
                                            {{ __('Related To') }}
                                        </label>
                                        @if($document->meeting)
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <i class="ri-calendar-event-line text-muted"></i>
                                                <span class="text-muted small">{{ __('Meeting:') }}</span>
                                                <span class="text-muted">{{ $document->meeting->title }}</span>
                                            </div>
                                        @endif
                                        @if($document->resolution)
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="ri-file-list-3-line text-muted"></i>
                                                <span class="text-muted small">{{ __('Resolution:') }}</span>
                                                <span class="text-muted">{{ $document->resolution->title }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: Actions sidebar --}}
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-bottom py-3">
                                <h6 class="mb-0 fw-semibold">{{ __('Actions') }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2 mb-4">
                                    @if($document->file)
                                    <a href="{{ route('shareholder.documents.download', $document) }}"
                                       class="btn btn-primary">
                                        <i class="ri-download-2-line me-2"></i>{{ __('Download Document') }}
                                    </a>
                                    @endif
                                    <a href="{{ route('shareholder.documents.index') }}"
                                       class="btn btn-outline-secondary">
                                        <i class="ri-arrow-left-line me-2"></i>{{ __('Back to Documents') }}
                                    </a>
                                </div>

                                <div>
                                    <p class="mb-2 fw-semibold text-muted small text-uppercase" style="letter-spacing:.05em;">
                                        {{ __('Document Stats') }}
                                    </p>
                                    <div class="stats-row">
                                        <i class="ri-eye-line text-muted"></i>
                                        <span>{{ __('Views') }}</span>
                                        <span class="stat-count">{{ number_format($document->view_count) }}</span>
                                    </div>
                                    <div class="stats-row">
                                        <i class="ri-download-line text-muted"></i>
                                        <span>{{ __('Downloads') }}</span>
                                        <span class="stat-count">{{ number_format($document->download_count) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>{{-- /row --}}

            </div>
        </div>
    </div>
</div>
@endsection
