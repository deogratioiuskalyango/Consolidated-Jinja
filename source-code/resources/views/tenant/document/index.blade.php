@extends('tenant.layouts.app')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- ── Page Header ── --}}
                <div class="doc-page-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <div>
                        <h3 class="doc-page-title mb-1">{{ $pageTitle }}</h3>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('tenant.dashboard') }}">{{ __('Dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                        </ol>
                    </div>
                    @if ($kycConfigs->isNotEmpty())
                    <button type="button" id="addFiles" class="doc-upload-btn">
                        <i class="ri-upload-2-line me-2"></i>{{ __('Upload Document') }}
                    </button>
                    @endif
                </div>

                {{-- ── Per-tenant config alert banners ── --}}
                @foreach ($kycConfigs->where('tenant_id', '!=', null) as $kycConfig)
                    <div class="doc-info-banner d-flex align-items-start gap-3 mb-3 alert-dismissible fade show" role="alert">
                        <div class="doc-info-banner-icon flex-shrink-0">
                            <i class="ri-notification-3-line"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="doc-info-banner-title mb-1">{{ $kycConfig->name }}</p>
                            <p class="doc-info-banner-body mb-0">
                                {{ $kycConfig->details }}
                                <button type="button" class="doc-info-banner-action specialUploadFilesAdd" data-id="{{ $kycConfig->id }}">
                                    {{ __('Upload Now') }} <i class="ri-arrow-right-line"></i>
                                </button>
                            </p>
                        </div>
                        <button type="button" class="doc-info-banner-close btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
                    </div>
                @endforeach

                {{-- ── Document Card Grid ── --}}
                @if ($kycVerifications->isEmpty())
                    <div class="doc-empty-state text-center py-5">
                        <i class="ri-file-shield-2-line doc-empty-icon"></i>
                        <p class="doc-empty-label">{{ __('No documents uploaded yet') }}</p>
                        @if ($kycConfigs->isNotEmpty())
                        <button type="button" id="addFilesEmpty" class="doc-upload-btn mt-2">
                            <i class="ri-upload-2-line me-2"></i>{{ __('Upload Your First Document') }}
                        </button>
                        @endif
                    </div>
                @else
                    <div class="row g-4 doc-card-grid">
                        @foreach ($kycVerifications as $kycVerification)
                            @php
                                $frontExt  = strtolower(pathinfo($kycVerification->front ?? '', PATHINFO_EXTENSION));
                                $backExt   = strtolower(pathinfo($kycVerification->back ?? '',  PATHINFO_EXTENSION));
                                $isPdf     = $frontExt === 'pdf';
                                $isBackPdf = $backExt  === 'pdf';
                            @endphp
                            <div class="col-xl-4 col-md-6 col-12">
                                <div class="doc-card">
                                    <div class="doc-card-thumb d-flex gap-2">
                                        <a href="{{ $kycVerification->front }}" download class="doc-card-thumb-item flex-fill" title="{{ __('Download Front') }}">
                                            @if ($isPdf)
                                                <div class="doc-card-pdf-icon"><i class="ri-file-pdf-2-line"></i><span class="doc-card-pdf-label">PDF</span></div>
                                            @else
                                                <img src="{{ $kycVerification->front }}" alt="{{ __('Front') }}" class="doc-card-img">
                                            @endif
                                            <span class="doc-card-side-badge">{{ __('Front') }}</span>
                                        </a>
                                        @if ($kycVerification->back)
                                            <a href="{{ $kycVerification->back }}" download class="doc-card-thumb-item flex-fill" title="{{ __('Download Back') }}">
                                                @if ($isBackPdf)
                                                    <div class="doc-card-pdf-icon"><i class="ri-file-pdf-2-line"></i><span class="doc-card-pdf-label">PDF</span></div>
                                                @else
                                                    <img src="{{ $kycVerification->back }}" alt="{{ __('Back') }}" class="doc-card-img">
                                                @endif
                                                <span class="doc-card-side-badge">{{ __('Back') }}</span>
                                            </a>
                                        @endif
                                    </div>
                                    <div class="doc-card-body">
                                        <p class="doc-card-name">{{ $kycVerification->config_name }}</p>
                                        @if ($kycVerification->category ?? null)
                                            <span class="doc-card-cat">{{ $kycVerification->category }}</span>
                                        @endif
                                        @if ($kycVerification->status == KYC_STATUS_ACCEPTED)
                                            <span class="doc-status doc-status--accepted"><i class="ri-checkbox-circle-line me-1"></i>{{ __('Accepted') }}</span>
                                        @elseif ($kycVerification->status == KYC_STATUS_PENDING)
                                            <span class="doc-status doc-status--pending"><i class="ri-time-line me-1"></i>{{ __('Pending Review') }}</span>
                                        @elseif ($kycVerification->status == KYC_STATUS_REJECTED)
                                            <span class="doc-status doc-status--rejected"><i class="ri-close-circle-line me-1"></i>{{ __('Rejected') }}</span>
                                            @if ($kycVerification->reason)
                                                <div class="doc-card-rejection mt-2">
                                                    <button type="button" class="doc-reason-toggle read-more-btn kycVerificationRow" data-rowid="{{ $kycVerification->id }}">
                                                        <i class="ri-information-line me-1"></i>{{ __('View Reason') }}
                                                        <i class="ri-arrow-down-s-line doc-reason-chevron"></i>
                                                    </button>
                                                    <div class="doc-reason-text reason-text d-none" id="reason_{{ $kycVerification->id }}">
                                                        {{ $kycVerification->reason }}
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                        @if ($kycVerification->status != KYC_STATUS_ACCEPTED)
                                            <div class="doc-card-actions mt-3 d-flex gap-2">
                                                <button type="button" class="doc-action-btn doc-action-btn--edit edit" data-id="{{ $kycVerification->id }}" title="{{ __('Edit') }}">
                                                    <i class="ri-pencil-line me-1"></i>{{ __('Edit') }}
                                                </button>
                                                <button type="button" class="doc-action-btn doc-action-btn--delete deleteItem" data-formid="delete_row_form_{{ $kycVerification->id }}" title="{{ __('Delete') }}">
                                                    <i class="ri-delete-bin-6-line me-1"></i>{{ __('Delete') }}
                                                </button>
                                                <form action="{{ route('tenant.document.delete', [$kycVerification->id]) }}" method="post" id="delete_row_form_{{ $kycVerification->id }}" class="d-none">
                                                    @method('DELETE') @csrf
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Upload-more tile --}}
                        @if ($kycConfigs->isNotEmpty())
                        <div class="col-xl-4 col-md-6 col-12">
                            <div class="doc-card doc-card--add" id="addFilesCard">
                                <i class="ri-add-circle-line doc-add-icon"></i>
                                <p class="doc-add-label">{{ __('Upload Another Document') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         UPLOAD WIZARD MODAL  (3 steps)
    ══════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="addFilesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog dwiz-dialog">
            <div class="modal-content dwiz-content">

                {{-- Header --}}
                <div class="dwiz-header">
                    <div class="dwiz-header-top">
                        <h5>📄 {{ __('Upload Document') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="dwiz-steps">
                        <div class="dwiz-pill active" id="dwiz-pill-1">
                            <div class="dwiz-dot">1</div>
                            <span class="dwiz-pill-label">{{ __('Category') }}</span>
                        </div>
                        <div class="dwiz-pill" id="dwiz-pill-2">
                            <div class="dwiz-dot">2</div>
                            <span class="dwiz-pill-label">{{ __('Document Type') }}</span>
                        </div>
                        <div class="dwiz-pill" id="dwiz-pill-3">
                            <div class="dwiz-dot">3</div>
                            <span class="dwiz-pill-label">{{ __('Upload') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Form --}}
                <form id="dwizForm" class="ajax" action="{{ route('tenant.document.store') }}" method="POST"
                      enctype="multipart/form-data" data-handler="getShowMessage">
                    @csrf
                    <input type="hidden" name="kyc_config_id" id="dwiz_config_id">

                    <div class="dwiz-body">

                        {{-- Step 1 — Category --}}
                        <div class="dwiz-panel active" id="dwiz-step-1">
                            <p class="dwiz-hint">{{ __('What type of document are you uploading?') }}</p>
                            <div class="dwiz-cat-grid" id="dwizCatGrid">
                                @php
                                    $catIcons = [
                                        'Identity'           => '🪪',
                                        'Proof of Address'   => '🏠',
                                        'Financial'          => '💰',
                                        'Employment'         => '💼',
                                        'Rental History'     => '🔑',
                                        'Education'          => '🎓',
                                        'Visa & Immigration' => '✈️',
                                        'Legal'              => '⚖️',
                                        'Health & Insurance' => '🏥',
                                        'Other'              => '📋',
                                    ];
                                @endphp
                                @foreach ($configsByCategory as $category => $catConfigs)
                                    <div class="dwiz-cat-card" data-cat="{{ $category }}" tabindex="0">
                                        <span class="dwiz-cat-icon">{{ $catIcons[$category] ?? '📄' }}</span>
                                        <div class="dwiz-cat-name">{{ $category }}</div>
                                        <div class="dwiz-cat-count">{{ $catConfigs->count() }} {{ __('types') }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Step 2 — Document type --}}
                        <div class="dwiz-panel" id="dwiz-step-2">
                            <button type="button" class="dwiz-back-cat" id="dwizBackCat">← {{ __('All categories') }}</button>
                            <div class="dwiz-cat-heading" id="dwizCatHeading"></div>
                            <div class="dwiz-search-wrap">
                                <span class="dwiz-search-icon">🔍</span>
                                <input type="text" id="dwizDocSearch" placeholder="{{ __('Search document types...') }}" autocomplete="off">
                            </div>
                            <div class="dwiz-type-list" id="dwizTypeList"></div>
                        </div>

                        {{-- Step 3 — Upload --}}
                        <div class="dwiz-panel" id="dwiz-step-3">
                            <div class="dwiz-chip" id="dwizChip">
                                <span id="dwizChipText"></span>
                                <button type="button" id="dwizChangeDoc" title="{{ __('Change') }}">✕</button>
                            </div>
                            <div class="dwiz-requires-badge d-none" id="dwizRequiresBadge">
                                <i class="ri-id-card-line me-1"></i>{{ __('Front and back sides required') }}
                            </div>
                            <div class="dwiz-demo-row d-none" id="dwizDemoRow">
                                <a class="dwiz-demo-link" id="dwizDemoLink" href="#" download>
                                    <i class="ri-download-2-line me-1"></i>{{ __('Download sample') }}
                                </a>
                            </div>

                            {{-- Front upload --}}
                            <div class="mb-3">
                                <label class="dwiz-upload-label"><i class="ri-image-line me-1"></i>{{ __('Front Side') }} <span class="text-danger">*</span></label>
                                <div class="doc-upload-zone" id="dwizFrontZone">
                                    <input type="file" name="front" class="doc-upload-input" accept=".jpeg,.jpg,.png,.pdf">
                                    <div class="doc-upload-default">
                                        <i class="ri-cloud-upload-line doc-upload-icon"></i>
                                        <p class="doc-upload-text">{{ __('Drag & drop or click to browse') }}</p>
                                        <p class="doc-upload-hint">{{ __('JPEG, PNG or PDF — max 10 MB') }}</p>
                                    </div>
                                    <div class="doc-preview d-none">
                                        <div class="doc-preview-inner">
                                            <img class="doc-preview-img d-none" alt="preview">
                                            <div class="doc-preview-pdf d-none"><i class="ri-file-pdf-2-line"></i></div>
                                            <div class="doc-preview-meta">
                                                <span class="doc-preview-name"></span>
                                                <span class="doc-preview-size"></span>
                                            </div>
                                        </div>
                                        <button type="button" class="doc-preview-clear" aria-label="Remove"><i class="ri-close-circle-fill"></i></button>
                                    </div>
                                </div>
                            </div>

                            {{-- Back upload (hidden unless is_both) --}}
                            <div class="mb-2 d-none" id="dwizBackWrap">
                                <label class="dwiz-upload-label"><i class="ri-image-2-line me-1"></i>{{ __('Back Side') }} <span class="text-danger">*</span></label>
                                <div class="doc-upload-zone" id="dwizBackZone">
                                    <input type="file" name="back" class="doc-upload-input" accept=".jpeg,.jpg,.png,.pdf">
                                    <div class="doc-upload-default">
                                        <i class="ri-cloud-upload-line doc-upload-icon"></i>
                                        <p class="doc-upload-text">{{ __('Drag & drop or click to browse') }}</p>
                                        <p class="doc-upload-hint">{{ __('JPEG, PNG or PDF — max 10 MB') }}</p>
                                    </div>
                                    <div class="doc-preview d-none">
                                        <div class="doc-preview-inner">
                                            <img class="doc-preview-img d-none" alt="preview">
                                            <div class="doc-preview-pdf d-none"><i class="ri-file-pdf-2-line"></i></div>
                                            <div class="doc-preview-meta">
                                                <span class="doc-preview-name"></span>
                                                <span class="doc-preview-size"></span>
                                            </div>
                                        </div>
                                        <button type="button" class="doc-preview-clear" aria-label="Remove"><i class="ri-close-circle-fill"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>{{-- /dwiz-body --}}

                    <div class="dwiz-footer">
                        <button type="button"   class="dwiz-btn dwiz-btn-back" id="dwizBtnBack" style="visibility:hidden">← {{ __('Back') }}</button>
                        <button type="button"   class="dwiz-btn dwiz-btn-next" id="dwizBtnNext" disabled>{{ __('Select Type') }} →</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         EDIT MODAL
    ══════════════════════════════════════════════════════ --}}
    <div class="modal fade doc-modal" id="editFilesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="doc-modal-title-wrap">
                        <i class="ri-edit-2-line doc-modal-icon"></i>
                        <h4 class="modal-title">{{ __('Edit Document') }}</h4>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="ajax" action="{{ route('tenant.document.store') }}" method="POST"
                      enctype="multipart/form-data" data-handler="getShowMessage">
                    @csrf
                    <input type="hidden" name="id" class="id">
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="doc-field-label">{{ __('Document Type') }}</label>
                            <input type="hidden" name="kyc_config_id" class="kyc_config_id">
                            <input type="text" class="form-control kyc_config_name doc-config-name-input" disabled placeholder="{{ __('Document type') }}">
                        </div>
                        <div class="mb-4">
                            <label class="doc-field-label"><i class="ri-image-line me-1"></i>{{ __('Front Side') }}</label>
                            <div class="doc-upload-zone" id="editFrontZone">
                                <input type="file" name="front" class="doc-upload-input front" accept=".jpeg,.jpg,.png,.pdf">
                                <div class="doc-upload-default">
                                    <i class="ri-cloud-upload-line doc-upload-icon"></i>
                                    <p class="doc-upload-text">{{ __('Drag & drop or click to browse') }}</p>
                                    <p class="doc-upload-hint">{{ __('JPEG, PNG or PDF — max 10 MB') }}</p>
                                </div>
                                <div class="doc-preview d-none">
                                    <div class="doc-preview-inner">
                                        <img class="doc-preview-img d-none" alt="preview">
                                        <div class="doc-preview-pdf d-none"><i class="ri-file-pdf-2-line"></i></div>
                                        <div class="doc-preview-meta">
                                            <span class="doc-preview-name"></span>
                                            <span class="doc-preview-size"></span>
                                        </div>
                                    </div>
                                    <button type="button" class="doc-preview-clear" aria-label="Remove"><i class="ri-close-circle-fill"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2 d-none isBoth">
                            <label class="doc-field-label"><i class="ri-image-2-line me-1"></i>{{ __('Back Side') }}</label>
                            <div class="doc-upload-zone" id="editBackZone">
                                <input type="file" name="back" class="doc-upload-input back" accept=".jpeg,.jpg,.png,.pdf">
                                <div class="doc-upload-default">
                                    <i class="ri-cloud-upload-line doc-upload-icon"></i>
                                    <p class="doc-upload-text">{{ __('Drag & drop or click to browse') }}</p>
                                    <p class="doc-upload-hint">{{ __('JPEG, PNG or PDF — max 10 MB') }}</p>
                                </div>
                                <div class="doc-preview d-none">
                                    <div class="doc-preview-inner">
                                        <img class="doc-preview-img d-none" alt="preview">
                                        <div class="doc-preview-pdf d-none"><i class="ri-file-pdf-2-line"></i></div>
                                        <div class="doc-preview-meta">
                                            <span class="doc-preview-name"></span>
                                            <span class="doc-preview-size"></span>
                                        </div>
                                    </div>
                                    <button type="button" class="doc-preview-clear" aria-label="Remove"><i class="ri-close-circle-fill"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="doc-btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="doc-btn-primary"><i class="ri-save-line me-1"></i>{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Route helpers --}}
    <input type="hidden" id="getInfoRoute"       value="{{ route('tenant.document.get.info') }}">
    <input type="hidden" id="getConfigInfoRoute" value="{{ route('tenant.document.get.config.info') }}">

    {{-- Config data for wizard --}}
    @php
        $dwizConfigsJson = json_encode($configsByCategory->map(function($items){
            return $items->map(function($c){
                return ['id' => $c->id, 'name' => $c->name, 'is_both' => $c->is_both, 'details' => $c->details];
            });
        }));
    @endphp
    <script>
    window.dwizConfigs = {!! $dwizConfigsJson !!};
    </script>
@endsection

@push('style')
<style>
/* ── Variables (light / dark) ── */
:root {
    --doc-radius:       12px;
    --doc-radius-sm:    8px;
    --doc-thumb-h:      160px;
    --doc-transition:   0.22s ease;
    --doc-accent:       #4f46e5;
    --doc-accent-grad:  linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    --doc-card-bg:      var(--t-bg-card, #ffffff);
    --doc-surface:      var(--t-bg-hover, #f8fafc);
    --doc-border:       var(--t-border, rgba(0,0,0,.1));
    --doc-text:         var(--t-text-primary, #1e293b);
    --doc-muted:        var(--t-text-muted, #64748b);
    --doc-shadow:       0 2px 12px rgba(0,0,0,.06);
    --doc-shadow-hover: 0 8px 28px rgba(0,0,0,.14);
}
body[data-bs-theme="dark"] {
    --doc-card-bg:      var(--t-bg-card, #1e2535);
    --doc-surface:      var(--t-bg-hover, #161d2d);
    --doc-border:       var(--t-border, rgba(255,255,255,.08));
    --doc-text:         var(--t-text-primary, #e2e8f0);
    --doc-muted:        var(--t-text-muted, #94a3b8);
    --doc-shadow:       0 2px 12px rgba(0,0,0,.3);
    --doc-shadow-hover: 0 8px 28px rgba(0,0,0,.5);
}

/* ── Page ── */
.doc-page-title { font-size:1.5rem; font-weight:700; color:var(--doc-text); margin:0; }
.doc-upload-btn {
    display:inline-flex; align-items:center; padding:.55rem 1.35rem;
    border:none; border-radius:var(--doc-radius-sm);
    background:var(--doc-accent-grad); color:#fff;
    font-size:.875rem; font-weight:600; cursor:pointer;
    box-shadow:0 4px 14px rgba(79,70,229,.35);
    transition:opacity var(--doc-transition), transform var(--doc-transition);
}
.doc-upload-btn:hover { opacity:.9; transform:translateY(-1px); }

/* ── Info banner ── */
.doc-info-banner { padding:1rem 1.25rem; border-radius:var(--doc-radius); background:rgba(79,70,229,.08); border:1px solid rgba(79,70,229,.2); position:relative; }
.doc-info-banner-icon { width:2.25rem; height:2.25rem; background:rgba(79,70,229,.15); border-radius:50%; display:flex; align-items:center; justify-content:center; color:var(--doc-accent); font-size:1rem; flex-shrink:0; }
.doc-info-banner-title { font-weight:600; color:var(--doc-text); font-size:.9375rem; }
.doc-info-banner-body  { color:var(--doc-muted); font-size:.8125rem; }
.doc-info-banner-action { background:none; border:none; padding:0; color:var(--doc-accent); font-weight:600; font-size:.8125rem; cursor:pointer; text-decoration:underline; }
.doc-info-banner-close { position:absolute; top:.75rem; right:.75rem; }

/* ── Empty state ── */
.doc-empty-state { color:var(--doc-muted); }
.doc-empty-icon  { font-size:5rem; opacity:.3; display:block; margin-bottom:.75rem; }
.doc-empty-label { font-size:1.125rem; font-weight:500; color:var(--doc-muted); }

/* ── Document card ── */
.doc-card {
    background:var(--doc-card-bg); border:1px solid var(--doc-border);
    border-radius:var(--doc-radius); overflow:hidden;
    box-shadow:var(--doc-shadow); height:100%; display:flex; flex-direction:column;
    transition:transform var(--doc-transition), box-shadow var(--doc-transition);
}
.doc-card:hover { transform:translateY(-4px); box-shadow:var(--doc-shadow-hover); }

/* Add-more tile */
.doc-card--add {
    align-items:center; justify-content:center;
    border-style:dashed; border-color:rgba(79,70,229,.35);
    cursor:pointer; min-height:260px; background:rgba(79,70,229,.03);
}
.doc-card--add:hover { border-color:var(--doc-accent); background:rgba(79,70,229,.07); }
.doc-add-icon  { font-size:2.5rem; color:rgba(79,70,229,.5); margin-bottom:.5rem; }
.doc-add-label { font-size:.875rem; font-weight:600; color:var(--doc-muted); margin:0; }

/* Card thumbnail */
.doc-card-thumb { background:var(--doc-surface); min-height:var(--doc-thumb-h); max-height:var(--doc-thumb-h); overflow:hidden; border-bottom:1px solid var(--doc-border); padding:.5rem; }
.doc-card-thumb-item { position:relative; display:flex; align-items:center; justify-content:center; border-radius:var(--doc-radius-sm); overflow:hidden; background:var(--doc-card-bg); border:1px solid var(--doc-border); text-decoration:none; transition:opacity var(--doc-transition); min-height:calc(var(--doc-thumb-h) - 1rem); }
.doc-card-thumb-item:hover { opacity:.85; }
.doc-card-img { width:100%; height:100%; object-fit:cover; display:block; }
.doc-card-pdf-icon { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:1rem; color:#ef4444; }
.doc-card-pdf-icon i { font-size:3rem; line-height:1; }
.doc-card-pdf-label { font-size:.65rem; font-weight:700; color:#ef4444; letter-spacing:.05em; margin-top:.25rem; }
.doc-card-side-badge { position:absolute; bottom:4px; left:50%; transform:translateX(-50%); background:rgba(0,0,0,.55); backdrop-filter:blur(4px); color:#fff; font-size:.65rem; font-weight:600; padding:.1rem .45rem; border-radius:100px; white-space:nowrap; }

/* Card body */
.doc-card-body { padding:1rem 1.1rem 1.1rem; flex-grow:1; display:flex; flex-direction:column; }
.doc-card-name { font-weight:600; font-size:.9375rem; color:var(--doc-text); margin-bottom:.35rem; }
.doc-card-cat  { display:inline-block; font-size:.72rem; font-weight:600; color:var(--doc-accent); background:rgba(79,70,229,.08); border-radius:100px; padding:.1rem .55rem; margin-bottom:.5rem; }

/* Status badges */
.doc-status { display:inline-flex; align-items:center; font-size:.75rem; font-weight:600; padding:.25rem .7rem; border-radius:100px; letter-spacing:.02em; }
.doc-status--accepted { background:rgba(34,197,94,.12); color:#16a34a; border:1px solid rgba(34,197,94,.25); }
.doc-status--pending  { background:rgba(245,158,11,.12); color:#d97706; border:1px solid rgba(245,158,11,.25); }
.doc-status--rejected { background:rgba(239,68,68,.12);  color:#dc2626; border:1px solid rgba(239,68,68,.25);  }

/* Rejection reason */
.doc-reason-toggle { display:inline-flex; align-items:center; background:none; border:none; padding:0; font-size:.8125rem; color:var(--doc-muted); cursor:pointer; transition:color var(--doc-transition); }
.doc-reason-toggle:hover { color:#ef4444; }
.doc-reason-chevron { transition:transform var(--doc-transition); }
.doc-reason-toggle.open .doc-reason-chevron { transform:rotate(180deg); }
.doc-reason-text { margin-top:.4rem; font-size:.8125rem; color:var(--doc-muted); padding:.6rem .75rem; background:rgba(239,68,68,.06); border-left:3px solid #ef4444; border-radius:0 var(--doc-radius-sm) var(--doc-radius-sm) 0; line-height:1.5; }

/* Action buttons */
.doc-card-actions { margin-top:auto; }
.doc-action-btn { display:inline-flex; align-items:center; padding:.35rem .85rem; font-size:.8125rem; font-weight:500; border-radius:var(--doc-radius-sm); border:1px solid var(--doc-border); cursor:pointer; transition:background var(--doc-transition), border-color var(--doc-transition), color var(--doc-transition); background:var(--doc-card-bg); color:var(--doc-text); }
.doc-action-btn--edit:hover   { background:rgba(79,70,229,.1); border-color:rgba(79,70,229,.4); color:var(--doc-accent); }
.doc-action-btn--delete:hover { background:rgba(239,68,68,.1); border-color:rgba(239,68,68,.4); color:#dc2626; }

/* ── Upload drop zone (shared by wizard + edit modal) ── */
.doc-upload-zone {
    position:relative; min-height:130px;
    border:2px dashed rgba(79,70,229,.35); border-radius:var(--doc-radius);
    background:var(--doc-surface); cursor:pointer;
    display:flex; align-items:center; justify-content:center;
    transition:border-color var(--doc-transition), background var(--doc-transition); overflow:hidden;
}
.doc-upload-zone:hover, .doc-upload-zone.dragover { border-color:var(--doc-accent); background:rgba(79,70,229,.05); }
.doc-upload-zone.dragover { border-style:solid; }
.doc-upload-input { position:absolute; inset:0; width:100%; height:100%; opacity:0; cursor:pointer; z-index:2; }
.doc-upload-default { display:flex; flex-direction:column; align-items:center; padding:1.5rem 1rem; text-align:center; pointer-events:none; z-index:1; }
.doc-upload-icon { font-size:2.25rem; color:var(--doc-accent); margin-bottom:.4rem; opacity:.7; }
.doc-upload-text { font-size:.875rem; font-weight:500; color:var(--doc-text); margin:0 0 .15rem; }
.doc-upload-hint { font-size:.75rem; color:var(--doc-muted); margin:0; }
.doc-preview { position:absolute; inset:0; display:flex; align-items:center; padding:1rem; gap:.75rem; background:var(--doc-surface); z-index:3; }
.doc-preview.d-none { display:none !important; }
.doc-preview-inner { display:flex; align-items:center; gap:.75rem; flex:1; min-width:0; }
.doc-preview-img { width:64px; height:64px; object-fit:cover; border-radius:var(--doc-radius-sm); border:1px solid var(--doc-border); flex-shrink:0; }
.doc-preview-pdf { width:64px; height:64px; border-radius:var(--doc-radius-sm); background:rgba(239,68,68,.08); border:1px solid rgba(239,68,68,.2); display:flex; align-items:center; justify-content:center; color:#ef4444; font-size:2rem; flex-shrink:0; }
.doc-preview-meta { min-width:0; }
.doc-preview-name { display:block; font-size:.875rem; font-weight:600; color:var(--doc-text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.doc-preview-size { font-size:.75rem; color:var(--doc-muted); }
.doc-preview-clear { flex-shrink:0; margin-left:auto; background:none; border:none; padding:.25rem; font-size:1.35rem; color:var(--doc-muted); cursor:pointer; line-height:1; z-index:4; position:relative; }
.doc-preview-clear:hover { color:#ef4444; }

/* ── Edit modal ── */
.doc-modal .modal-content { background:var(--doc-card-bg); border:1px solid var(--doc-border); border-radius:var(--doc-radius); box-shadow:0 20px 60px rgba(0,0,0,.25); }
.doc-modal .modal-header { border-bottom:1px solid var(--doc-border); padding:1.25rem 1.5rem; }
.doc-modal .modal-footer { border-top:1px solid var(--doc-border); padding:1rem 1.5rem; background:var(--doc-card-bg); }
.doc-modal .modal-body { padding:1.5rem; background:var(--doc-card-bg); }
.doc-modal-title-wrap { display:flex; align-items:center; gap:.6rem; }
.doc-modal-icon { font-size:1.25rem; color:var(--doc-accent); }
.doc-modal .modal-title { font-size:1.0625rem; font-weight:700; color:var(--doc-text); }
.doc-field-label { display:block; font-size:.8125rem; font-weight:600; color:var(--doc-muted); margin-bottom:.4rem; letter-spacing:.02em; text-transform:uppercase; }
.doc-config-name-input { background:var(--doc-surface) !important; border-color:var(--doc-border) !important; color:var(--doc-text) !important; font-weight:500; }
.doc-btn-primary { display:inline-flex; align-items:center; padding:.5rem 1.35rem; background:var(--doc-accent-grad); color:#fff; border:none; border-radius:var(--doc-radius-sm); font-size:.875rem; font-weight:600; cursor:pointer; box-shadow:0 3px 10px rgba(79,70,229,.3); transition:opacity var(--doc-transition); }
.doc-btn-primary:hover { opacity:.88; }
.doc-btn-secondary { display:inline-flex; align-items:center; padding:.5rem 1.25rem; background:transparent; color:var(--doc-muted); border:1px solid var(--doc-border); border-radius:var(--doc-radius-sm); font-size:.875rem; font-weight:500; cursor:pointer; transition:border-color var(--doc-transition), color var(--doc-transition); }
.doc-btn-secondary:hover { border-color:var(--doc-muted); color:var(--doc-text); }

/* ════════════════════════════════════════════════
   UPLOAD WIZARD
════════════════════════════════════════════════ */
.dwiz-dialog { max-width:680px; margin:.75rem auto; }
.dwiz-content {
    border:none; border-radius:18px; overflow:hidden;
    box-shadow:0 20px 60px rgba(0,0,0,.22);
    display:flex; flex-direction:column;
    max-height:calc(100vh - 1.5rem);
}
#dwizForm { display:flex; flex-direction:column; flex:1 1 auto; min-height:0; overflow:hidden; }

/* Header */
.dwiz-header { background:linear-gradient(135deg, #312e81 0%, #4f46e5 100%); padding:22px 24px 0; flex-shrink:0; }
.dwiz-header-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; gap:10px; }
.dwiz-header-top h5 { color:#fff; font-weight:700; font-size:.95rem; margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.dwiz-header-top .btn-close { filter:invert(1); opacity:.7; flex-shrink:0; }

/* Pills */
.dwiz-steps { display:flex; }
.dwiz-pill { flex:1; text-align:center; padding:8px 4px 12px; position:relative; cursor:default; min-width:0; }
.dwiz-pill::after { content:''; position:absolute; bottom:0; left:50%; transform:translateX(-50%); width:0; height:3px; background:#fff; border-radius:3px 3px 0 0; transition:width .25s; }
.dwiz-pill.active::after { width:70%; }
.dwiz-dot { width:26px; height:26px; border-radius:50%; background:rgba(255,255,255,.2); color:rgba(255,255,255,.6); font-size:.72rem; font-weight:700; display:inline-flex; align-items:center; justify-content:center; margin-bottom:4px; transition:all .2s; }
.dwiz-pill.active .dwiz-dot { background:#fff; color:#312e81; }
.dwiz-pill.done   .dwiz-dot { background:rgba(52,211,153,.9); color:#fff; }
.dwiz-pill-label { font-size:.63rem; color:rgba(255,255,255,.5); display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.dwiz-pill.active .dwiz-pill-label { color:rgba(255,255,255,.9); font-weight:600; }

/* Body */
.dwiz-body { padding:22px 24px 14px; background:var(--doc-card-bg); flex:1 1 auto; overflow-y:auto; min-height:0; -webkit-overflow-scrolling:touch; }
.dwiz-body::-webkit-scrollbar { width:4px; }
.dwiz-body::-webkit-scrollbar-thumb { background:var(--doc-border); border-radius:4px; }
.dwiz-panel { display:none; }
.dwiz-panel.active { display:block; }
.dwiz-hint { font-size:.83rem; color:var(--doc-muted); margin-bottom:16px; line-height:1.5; }

/* Category grid */
.dwiz-cat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; }
.dwiz-cat-card { border:2px solid var(--doc-border); border-radius:12px; padding:14px 8px 12px; text-align:center; cursor:pointer; transition:border-color .15s, box-shadow .15s, transform .15s, background .15s; background:var(--doc-card-bg); position:relative; user-select:none; -webkit-tap-highlight-color:transparent; }
.dwiz-cat-card:hover { border-color:#4f46e5; box-shadow:0 4px 16px rgba(79,70,229,.12); transform:translateY(-1px); }
.dwiz-cat-card:active { transform:scale(.97); }
.dwiz-cat-card.selected { border-color:#4f46e5; background:rgba(79,70,229,.06); }
.dwiz-cat-card.selected::after { content:'✓'; position:absolute; top:7px; right:8px; width:17px; height:17px; border-radius:50%; background:#4f46e5; color:#fff; font-size:.58rem; font-weight:700; display:flex; align-items:center; justify-content:center; line-height:1; }
.dwiz-cat-icon { font-size:1.55rem; margin-bottom:5px; display:block; line-height:1; }
.dwiz-cat-name  { font-size:.72rem; font-weight:600; color:var(--doc-text); line-height:1.3; }
.dwiz-cat-count { font-size:.63rem; color:var(--doc-muted); margin-top:2px; }

/* Doc type list */
.dwiz-back-cat { display:inline-flex; align-items:center; gap:6px; font-size:.8rem; color:#4f46e5; cursor:pointer; margin-bottom:14px; font-weight:600; background:none; border:none; padding:0; }
.dwiz-back-cat:hover { text-decoration:underline; }
.dwiz-cat-heading { font-size:.9rem; font-weight:700; color:var(--doc-text); margin-bottom:12px; }
.dwiz-search-wrap { position:relative; margin-bottom:12px; }
.dwiz-search-wrap input { padding:0 14px 0 34px; border-radius:10px; border:1px solid var(--doc-border); background:var(--doc-surface); color:var(--doc-text); font-size:.88rem; height:40px; width:100%; transition:border-color .15s; }
.dwiz-search-wrap input:focus { outline:none; border-color:#4f46e5; box-shadow:0 0 0 3px rgba(79,70,229,.1); background:var(--doc-card-bg); }
.dwiz-search-icon { position:absolute; left:11px; top:50%; transform:translateY(-50%); color:var(--doc-muted); font-size:.85rem; pointer-events:none; }
.dwiz-type-list { display:flex; flex-direction:column; gap:6px; }
.dwiz-type-item { display:flex; align-items:flex-start; gap:12px; padding:11px 14px; border-radius:10px; border:1.5px solid var(--doc-border); background:var(--doc-card-bg); cursor:pointer; transition:border-color .15s, background .15s; user-select:none; }
.dwiz-type-item:hover { border-color:#4f46e5; background:rgba(79,70,229,.04); }
.dwiz-type-item.selected { border-color:#4f46e5; background:rgba(79,70,229,.08); box-shadow:0 0 0 3px rgba(79,70,229,.1); }
.dwiz-type-radio { width:18px; height:18px; border-radius:50%; border:2px solid var(--doc-border); background:var(--doc-card-bg); flex-shrink:0; display:flex; align-items:center; justify-content:center; transition:all .15s; margin-top:1px; }
.dwiz-type-item.selected .dwiz-type-radio { border-color:#4f46e5; background:#4f46e5; }
.dwiz-type-item.selected .dwiz-type-radio::after { content:''; width:6px; height:6px; border-radius:50%; background:#fff; }
.dwiz-type-info { flex:1; min-width:0; }
.dwiz-type-name   { font-size:.85rem; font-weight:600; color:var(--doc-text); margin-bottom:2px; }
.dwiz-type-detail { font-size:.76rem; color:var(--doc-muted); line-height:1.4; }
.dwiz-both-badge { display:inline-flex; align-items:center; gap:3px; font-size:.68rem; font-weight:600; color:#4f46e5; background:rgba(79,70,229,.1); border-radius:100px; padding:.1rem .5rem; margin-top:4px; }
.dwiz-empty { text-align:center; padding:24px; color:var(--doc-muted); font-size:.84rem; }

/* Step 3 details */
.dwiz-chip { display:inline-flex; align-items:center; gap:8px; background:rgba(79,70,229,.1); border:1px solid rgba(79,70,229,.25); border-radius:20px; padding:5px 14px; font-size:.8rem; font-weight:600; color:#4f46e5; margin-bottom:14px; }
.dwiz-chip button { background:none; border:none; color:#4f46e5; font-size:.9rem; padding:0; line-height:1; cursor:pointer; opacity:.7; }
.dwiz-chip button:hover { opacity:1; }
.dwiz-requires-badge { display:inline-flex; align-items:center; font-size:.78rem; font-weight:600; color:#d97706; background:rgba(245,158,11,.1); border:1px solid rgba(245,158,11,.3); border-radius:8px; padding:.3rem .75rem; margin-bottom:14px; }
.dwiz-demo-row { margin-bottom:12px; }
.dwiz-demo-link { display:inline-flex; align-items:center; font-size:.8rem; font-weight:600; color:#4f46e5; text-decoration:none; padding:.3rem .75rem; border:1px solid rgba(79,70,229,.3); border-radius:8px; background:rgba(79,70,229,.05); }
.dwiz-demo-link:hover { background:rgba(79,70,229,.12); }
.dwiz-upload-label { display:block; font-size:.8125rem; font-weight:600; color:var(--doc-muted); text-transform:uppercase; letter-spacing:.03em; margin-bottom:.4rem; }

/* Footer */
.dwiz-footer { display:flex; align-items:center; justify-content:space-between; padding:16px 24px; background:var(--doc-card-bg); border-top:1px solid var(--doc-border); flex-shrink:0; gap:10px; }
.dwiz-btn { padding:10px 22px; border-radius:10px; font-size:.88rem; font-weight:600; cursor:pointer; border:none; transition:all .15s; display:inline-flex; align-items:center; gap:6px; }
.dwiz-btn-back { background:var(--doc-surface); color:var(--doc-muted); border:1.5px solid var(--doc-border); }
.dwiz-btn-back:hover { background:var(--doc-border); }
.dwiz-btn-next { background:linear-gradient(135deg,#312e81,#4f46e5); color:#fff; box-shadow:0 4px 14px rgba(79,70,229,.35); }
.dwiz-btn-next:hover { box-shadow:0 6px 20px rgba(79,70,229,.45); transform:translateY(-1px); }
.dwiz-btn-next:disabled { opacity:.5; cursor:not-allowed; transform:none; box-shadow:none; }

/* Responsive */
@media (max-width:576px) {
    .dwiz-cat-grid { grid-template-columns:repeat(2,1fr); }
    .dwiz-dialog { margin:0; align-items:flex-end; display:flex; min-height:100vh; }
    .dwiz-content { border-radius:18px 18px 0 0; max-height:92vh; width:100%; }
    .dwiz-footer { flex-wrap:wrap; gap:8px; }
    .dwiz-btn { flex:1; min-width:110px; }
    .dwiz-body { padding:18px 16px 12px; }
    .dwiz-footer { padding:14px 16px; }
}
@media (min-width:577px) and (max-width:768px) {
    .dwiz-cat-grid { grid-template-columns:repeat(3,1fr); }
}
</style>
@endpush

@push('script')
<script>
(function () {
    'use strict';

    /* ── Upload zone helpers ── */
    function formatBytes(b) {
        if (!b) return '0 B';
        var k = 1024, s = ['B','KB','MB','GB'], i = Math.floor(Math.log(b)/Math.log(k));
        return parseFloat((b/Math.pow(k,i)).toFixed(1)) + ' ' + s[i];
    }
    function showFilePreview(zone, file) {
        var $z = $(zone), $d = $z.find('.doc-upload-default'), $p = $z.find('.doc-preview');
        $p.find('.doc-preview-name').text(file.name);
        $p.find('.doc-preview-size').text(formatBytes(file.size));
        if (file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf')) {
            $p.find('.doc-preview-img').addClass('d-none');
            $p.find('.doc-preview-pdf').removeClass('d-none');
        } else {
            var r = new FileReader();
            r.onload = function(e) { $p.find('.doc-preview-img').attr('src', e.target.result).removeClass('d-none'); };
            r.readAsDataURL(file);
            $p.find('.doc-preview-pdf').addClass('d-none');
        }
        $d.addClass('d-none'); $p.removeClass('d-none');
    }
    function showExistingPreview(zone, url) {
        if (!url) return;
        var $z = $(zone), $d = $z.find('.doc-upload-default'), $p = $z.find('.doc-preview');
        var fn = url.split('/').pop().split('?')[0] || 'Current file';
        var ext = fn.split('.').pop().toLowerCase();
        $p.find('.doc-preview-name').text(fn);
        $p.find('.doc-preview-size').text('Current file');
        if (ext === 'pdf') { $p.find('.doc-preview-img').addClass('d-none'); $p.find('.doc-preview-pdf').removeClass('d-none'); }
        else { $p.find('.doc-preview-img').attr('src', url).removeClass('d-none'); $p.find('.doc-preview-pdf').addClass('d-none'); }
        $d.addClass('d-none'); $p.removeClass('d-none');
    }
    function resetZone(zone) {
        var $z = $(zone);
        $z.find('.doc-upload-input').val('');
        $z.find('.doc-preview-img').attr('src','').addClass('d-none');
        $z.find('.doc-preview-pdf').addClass('d-none');
        $z.find('.doc-preview').addClass('d-none');
        $z.find('.doc-upload-default').removeClass('d-none');
    }
    function bindZone(zone) {
        var $z = $(zone), $input = $z.find('.doc-upload-input');
        $z.on('dragover dragenter', function(e){ e.preventDefault(); e.stopPropagation(); $z.addClass('dragover'); });
        $z.on('dragleave', function(e){ if (!$.contains(zone, e.relatedTarget)) $z.removeClass('dragover'); });
        $z.on('drop', function(e){
            e.preventDefault(); e.stopPropagation(); $z.removeClass('dragover');
            var files = e.originalEvent.dataTransfer.files;
            if (files && files.length) {
                try { var dt = new DataTransfer(); dt.items.add(files[0]); $input[0].files = dt.files; } catch(err) {}
                showFilePreview(zone, files[0]);
            }
        });
        $input.on('change', function(){ if (this.files && this.files.length) showFilePreview(zone, this.files[0]); });
        $z.find('.doc-preview-clear').on('click', function(e){ e.stopPropagation(); resetZone(zone); });
    }
    $('.doc-upload-zone').each(function(){ bindZone(this); });

    /* ════════════════════════════════
       UPLOAD WIZARD
    ════════════════════════════════ */
    var state = { step: 1, cat: null, configId: null, configName: null, isBoth: 0 };
    var $modal  = $('#addFilesModal');
    var $form   = $('#dwizForm');
    var $back   = $('#dwizBtnBack');
    var $next   = $('#dwizBtnNext');
    var $cfgIn  = $('#dwiz_config_id');
    var $frontZ = $('#dwizFrontZone');
    var $backWr = $('#dwizBackWrap');
    var $backZ  = $('#dwizBackZone');

    function showStep(n) {
        state.step = n;
        [1,2,3].forEach(function(i){
            $('#dwiz-step-'+i).toggleClass('active', i===n);
            var $p = $('#dwiz-pill-'+i);
            $p.toggleClass('active', i===n).toggleClass('done', i<n);
        });
        $back.css('visibility', n > 1 ? 'visible' : 'hidden');
        if (n===1) { $next.text('Select Type →').prop('type','button').prop('disabled', !state.cat); }
        else if (n===2) { $next.text('Continue →').prop('type','button').prop('disabled', !state.configId); }
        else { $next.text('✔ Submit Document').prop('type','submit').prop('disabled', false); }
    }

    function renderTypes(cat, filter) {
        filter = (filter||'').toLowerCase();
        var items = (window.dwizConfigs && window.dwizConfigs[cat]) ? window.dwizConfigs[cat] : [];
        var html = '';
        items.forEach(function(item){
            if (filter && item.name.toLowerCase().indexOf(filter) === -1) return;
            var sel = item.id == state.configId ? 'selected' : '';
            var bothBadge = item.is_both ? '<span class="dwiz-both-badge"><i class="ri-id-card-line"></i> Front & Back</span>' : '';
            html += '<div class="dwiz-type-item '+sel+'" data-id="'+item.id+'" data-name="'+item.name.replace(/"/g,'&quot;')+'" data-isboth="'+item.is_both+'" data-details="'+item.details.replace(/"/g,'&quot;')+'">'
                  + '<div class="dwiz-type-radio"></div>'
                  + '<div class="dwiz-type-info">'
                  + '<div class="dwiz-type-name">'+item.name+'</div>'
                  + '<div class="dwiz-type-detail">'+item.details+'</div>'
                  + bothBadge
                  + '</div></div>';
        });
        if (!html) html = '<div class="dwiz-empty">No document types match your search.</div>';
        $('#dwizTypeList').html(html);
    }

    function goStep2(cat) {
        state.cat = cat;
        document.querySelectorAll('.dwiz-cat-card').forEach(function(c){ c.classList.toggle('selected', c.dataset.cat===cat); });
        $('#dwizCatHeading').text(cat);
        $('#dwizDocSearch').val('');
        renderTypes(cat, '');
        showStep(2);
    }

    function goStep3() {
        $('#dwizChipText').text(state.configName);
        if (state.isBoth) {
            $backWr.removeClass('d-none');
            $('#dwizRequiresBadge').removeClass('d-none');
        } else {
            $backWr.addClass('d-none');
            $('#dwizRequiresBadge').addClass('d-none');
        }
        resetZone($frontZ[0]);
        resetZone($backZ[0]);
        showStep(3);
    }

    function resetWizard() {
        state = { step:1, cat:null, configId:null, configName:null, isBoth:0 };
        $cfgIn.val('');
        resetZone($frontZ[0]);
        resetZone($backZ[0]);
        document.querySelectorAll('.dwiz-cat-card').forEach(function(c){ c.classList.remove('selected'); });
        showStep(1);
    }

    /* Category grid clicks */
    $('#dwizCatGrid').on('click', function(e){
        var $c = $(e.target).closest('.dwiz-cat-card');
        if (!$c.length) return;
        state.cat = $c.data('cat');
        document.querySelectorAll('.dwiz-cat-card').forEach(function(c){ c.classList.toggle('selected', c.dataset.cat===state.cat); });
        showStep(1);
    });
    $('#dwizCatGrid').on('keydown', function(e){
        if (e.key==='Enter'||e.key===' ') { var $c=$(e.target).closest('.dwiz-cat-card'); if ($c.length){ e.preventDefault(); goStep2($c.data('cat')); } }
    });

    /* Back to categories */
    $('#dwizBackCat').on('click', function(){ showStep(1); });

    /* Type list clicks */
    $('#dwizTypeList').on('click', function(e){
        var $item = $(e.target).closest('.dwiz-type-item');
        if (!$item.length) return;
        state.configId   = $item.data('id');
        state.configName = $item.data('name');
        state.isBoth     = +$item.data('isboth');
        $cfgIn.val(state.configId);
        document.querySelectorAll('.dwiz-type-item').forEach(function(el){ el.classList.toggle('selected', el.dataset.id==state.configId); });
        showStep(2);
    });

    /* Search */
    $('#dwizDocSearch').on('input', function(){ renderTypes(state.cat, this.value); });

    /* Change doc chip */
    $('#dwizChangeDoc').on('click', function(){
        state.configId=null; state.configName=null; state.isBoth=0; $cfgIn.val('');
        renderTypes(state.cat,'');
        showStep(2);
    });

    /* Back */
    $back.on('click', function(){
        if (state.step===2) showStep(1);
        else if (state.step===3) { renderTypes(state.cat,''); showStep(2); }
    });

    /* Next */
    $next.on('click', function(){
        if (state.step===1 && state.cat) goStep2(state.cat);
        else if (state.step===2 && state.configId) goStep3();
    });

    /* Demo file */
    $('#getConfigInfoRoute').length && $(document).on('change', '#dwiz_config_id', function(){
        var val = $(this).val();
        if (val) commonAjax('GET', $('#getConfigInfoRoute').val(), function(r){
            if (r && r.data && r.data.image && r.data.image.indexOf('no-image') === -1) {
                $('#dwizDemoRow').removeClass('d-none');
                $('#dwizDemoLink').attr('href', r.data.image);
            } else {
                $('#dwizDemoRow').addClass('d-none');
            }
        }, function(){}, { id: val });
        else $('#dwizDemoRow').addClass('d-none');
    });

    /* Client-side upload validation */
    $form[0].addEventListener('submit', function(e){
        if (state.step !== 3) { e.preventDefault(); e.stopImmediatePropagation(); return; }
        var frontInput = $frontZ.find('.doc-upload-input')[0];
        if (!frontInput.files || !frontInput.files.length) {
            e.preventDefault(); e.stopImmediatePropagation();
            $frontZ.css({'border-color':'#ef4444','border-style':'solid'});
            $frontZ.find('.doc-upload-text').css('color','#ef4444');
            setTimeout(function(){ $frontZ.css({'border-color':'','border-style':''}); $frontZ.find('.doc-upload-text').css('color',''); }, 3000);
            return;
        }
    });

    /* Open wizard */
    $('#addFiles, #addFilesEmpty, #addFilesCard').on('click', function(){ resetWizard(); $modal.modal('show'); });

    /* Pre-select config from alert banner */
    $(document).on('click', '.specialUploadFilesAdd', function(){
        var configId = $(this).data('id');
        resetWizard();
        // Find which category this config belongs to
        var foundCat = null;
        if (window.dwizConfigs) {
            Object.keys(window.dwizConfigs).forEach(function(cat){
                window.dwizConfigs[cat].forEach(function(c){ if (c.id == configId) foundCat = cat; });
            });
        }
        if (foundCat) {
            state.cat = foundCat;
            // pre-select the config
            if (window.dwizConfigs[foundCat]) {
                window.dwizConfigs[foundCat].forEach(function(c){
                    if (c.id == configId) { state.configId=c.id; state.configName=c.name; state.isBoth=c.is_both; }
                });
            }
            $cfgIn.val(state.configId);
            goStep3();
        }
        $modal.modal('show');
    });

    /* Reset wizard on modal close */
    $modal.on('hidden.bs.modal', resetWizard);

    /* Bind wizard zones after modal open */
    $modal.on('shown.bs.modal', function(){
        bindZone($frontZ[0]);
        bindZone($backZ[0]);
    });

    /* ════════════════════════════════
       EDIT MODAL
    ════════════════════════════════ */
    $(document).on('click', '.edit', function(){
        commonAjax('GET', $('#getInfoRoute').val(), function(response){
            var $em = $('#editFilesModal'), data = response.data;
            $em.find('.id').val(data.id);
            $em.find('.kyc_config_id').val(data.kyc_config_id);
            $em.find('.kyc_config_name').val(data.config_name);
            if (data.is_both==1) $em.find('.isBoth').removeClass('d-none');
            else $em.find('.isBoth').addClass('d-none');
            resetZone($('#editFrontZone')[0]);
            if (data.front) showExistingPreview($('#editFrontZone')[0], data.front);
            resetZone($('#editBackZone')[0]);
            if (data.back)  showExistingPreview($('#editBackZone')[0], data.back);
            $em.modal('show');
        }, function(){}, { id: $(this).data('id') });
    });
    $('#editFilesModal').on('shown.bs.modal', function(){
        bindZone($('#editFrontZone')[0]);
        bindZone($('#editBackZone')[0]);
    });
    $('#editFilesModal').on('hidden.bs.modal', function(){
        resetZone($('#editFrontZone')[0]);
        resetZone($('#editBackZone')[0]);
    });

    /* ════════════════════════════════
       DELETE
    ════════════════════════════════ */
    $(document).on('click', '.deleteItem', function(){ $('#' + $(this).data('formid')).submit(); });

    /* ════════════════════════════════
       REJECTION REASON TOGGLE
    ════════════════════════════════ */
    $(document).on('click', '.read-more-btn', function(){
        var rowId = $(this).data('rowid'), $text = $('#reason_'+rowId);
        if ($text.hasClass('d-none')) { $text.slideDown(250).removeClass('d-none'); $(this).addClass('open'); }
        else { $text.slideUp(250, function(){ $(this).addClass('d-none'); }); $(this).removeClass('open'); }
    });

}());
</script>
@endpush
