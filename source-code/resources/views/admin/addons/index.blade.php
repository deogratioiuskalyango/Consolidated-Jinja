@extends('admin.layouts.app')
@push('title'){{ $title }}@endpush

@push('style')
<style>
    .addon-card {
        border-radius: 12px;
        border: 1px solid #f0f0f0;
        transition: box-shadow .2s, transform .2s;
        overflow: hidden;
    }
    .addon-card:hover {
        box-shadow: 0 6px 24px rgba(0,0,0,.10);
        transform: translateY(-2px);
    }
    .addon-card .card-header-bar {
        height: 5px;
    }
    .addon-icon-wrap {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        flex-shrink: 0;
    }
    .badge-installed   { background: #d1fae5; color: #065f46; }
    .badge-uninstalled { background: #fee2e2; color: #991b1b; }
    .dz-preview-wrap { display: none; }
    .dropzone-area {
        border: 2px dashed #c4b5fd;
        border-radius: 10px;
        padding: 24px;
        text-align: center;
        cursor: pointer;
        background: #faf5ff;
        transition: background .2s;
    }
    .dropzone-area:hover { background: #f3e8ff; }
    .dropzone-area.has-file { background: #f0fdf4; border-color: #86efac; }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            {{-- Page header --}}
            <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-20">
                <h3 class="mb-sm-0">{{ __('Addons') }}</h3>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Addons') }}</li>
                </ol>
            </div>

            {{-- Info banner --}}
            <div class="alert alert-info d-flex align-items-center gap-2 mb-24" role="alert">
                <i class="ri-information-line fs-5"></i>
                <div>
                    {{ __('To install an addon, purchase it from CodeCanyon, download the zip, then upload it here.') }}
                    <strong>{{ __('Always backup your database and files before installing.') }}</strong>
                </div>
            </div>

            {{-- Addon cards --}}
            <div class="row g-4">
                @foreach ($addons as $addon)
                    @php
                        $colorMap = [
                            'primary' => ['bg' => '#ede9fe', 'text' => '#5b21b6', 'bar' => '#7c3aed'],
                            'success' => ['bg' => '#d1fae5', 'text' => '#065f46', 'bar' => '#059669'],
                            'warning' => ['bg' => '#fef3c7', 'text' => '#92400e', 'bar' => '#d97706'],
                            'info'    => ['bg' => '#dbeafe', 'text' => '#1e40af', 'bar' => '#2563eb'],
                            'danger'  => ['bg' => '#fee2e2', 'text' => '#991b1b', 'bar' => '#dc2626'],
                        ];
                        $c = $colorMap[$addon['color']] ?? $colorMap['primary'];
                    @endphp
                    <div class="col-xl-4 col-md-6">
                        <div class="addon-card bg-white h-100 d-flex flex-column">
                            <div class="card-header-bar" style="background:{{ $c['bar'] }}"></div>
                            <div class="p-4 flex-grow-1 d-flex flex-column">

                                {{-- Header --}}
                                <div class="d-flex align-items-start gap-3 mb-3">
                                    <div class="addon-icon-wrap"
                                         style="background:{{ $c['bg'] }}; color:{{ $c['text'] }}">
                                        <i class="{{ $addon['icon'] }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1 fw-700">{{ $addon['name'] }}</h5>
                                        <span class="badge rounded-pill px-2 py-1
                                            {{ $addon['is_installed'] ? 'badge-installed' : 'badge-uninstalled' }}">
                                            <i class="ri-{{ $addon['is_installed'] ? 'check' : 'close' }}-circle-line me-1"></i>
                                            {{ $addon['is_installed'] ? __('Installed') : __('Not Installed') }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Description --}}
                                <p class="text-muted small mb-3 flex-grow-1">{{ $addon['description'] }}</p>

                                {{-- Version info --}}
                                @if ($addon['is_installed'])
                                    <div class="bg-light rounded p-2 mb-3 small">
                                        <i class="ri-price-tag-3-line me-1 text-muted"></i>
                                        {{ __('Version') }}:
                                        <strong>{{ $addon['current_version'] ?? __('Unknown') }}</strong>
                                    </div>
                                @endif

                                {{-- Action buttons --}}
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button"
                                            class="btn btn-sm btn-primary flex-fill"
                                            data-bs-toggle="modal"
                                            data-bs-target="#installModal-{{ $addon['code'] }}">
                                        <i class="ri-upload-cloud-line me-1"></i>
                                        {{ $addon['is_installed'] ? __('Update Addon') : __('Install Addon') }}
                                    </button>
                                    <a href="{{ $addon['codecanyon_url'] }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="ri-external-link-line"></i>
                                        {{ __('Purchase') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Install / Update Modal for this addon --}}
                    <div class="modal fade" id="installModal-{{ $addon['code'] }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header" style="border-top: 4px solid {{ $c['bar'] }}">
                                    <h5 class="modal-title">
                                        <i class="{{ $addon['icon'] }} me-2" style="color:{{ $c['bar'] }}"></i>
                                        {{ $addon['is_installed'] ? __('Update') : __('Install') }}
                                        — {{ $addon['name'] }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">

                                    {{-- Warning --}}
                                    <div class="alert alert-warning small mb-3">
                                        <i class="ri-alert-line me-1"></i>
                                        <strong>{{ __('Important:') }}</strong>
                                        {{ __('Do not install if you have customised this addon — your changes will be lost. Take a full backup first.') }}
                                    </div>

                                    {{-- Step 1: Upload zip --}}
                                    <h6 class="fw-700 mb-2">
                                        <span class="badge bg-secondary me-1">1</span>
                                        {{ __('Upload Addon ZIP') }}
                                    </h6>

                                    <div class="dropzone-area mb-3 {{ $addon['uploaded_zip'] ? 'has-file' : '' }}"
                                         id="dz-area-{{ $addon['code'] }}"
                                         onclick="document.getElementById('fileInput-{{ $addon['code'] }}').click()">
                                        <input type="file" id="fileInput-{{ $addon['code'] }}"
                                               accept=".zip" style="display:none"
                                               onchange="handleFileSelect(this, '{{ $addon['code'] }}')">

                                        <div id="dz-placeholder-{{ $addon['code'] }}">
                                            @if ($addon['uploaded_zip'])
                                                <i class="ri-file-zip-line fs-2 text-success"></i>
                                                <p class="mb-0 mt-1 small text-success fw-600">
                                                    {{ $addon['uploaded_zip'] }} {{ __('already uploaded') }}
                                                </p>
                                                <p class="mb-0 small text-muted">{{ __('Click to replace') }}</p>
                                            @else
                                                <i class="ri-upload-cloud-2-line fs-2 text-primary"></i>
                                                <p class="mb-0 mt-1 fw-600">{{ __('Click to select ZIP file') }}</p>
                                                <p class="mb-0 small text-muted">{{ __('Only .zip files accepted') }}</p>
                                            @endif
                                        </div>

                                        <div id="dz-selected-{{ $addon['code'] }}" style="display:none">
                                            <i class="ri-file-zip-line fs-2 text-success"></i>
                                            <p class="mb-0 mt-1 fw-600 text-success" id="dz-filename-{{ $addon['code'] }}"></p>
                                            <button type="button"
                                                    class="btn btn-sm btn-primary mt-2"
                                                    onclick="event.stopPropagation(); uploadAddonZip('{{ $addon['code'] }}')">
                                                <i class="ri-upload-line me-1"></i>{{ __('Upload') }}
                                            </button>
                                        </div>

                                        <div class="progress mt-2" id="dz-progress-{{ $addon['code'] }}"
                                             style="display:none; height:6px;">
                                            <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated"
                                                 id="dz-bar-{{ $addon['code'] }}" style="width:0%"></div>
                                        </div>
                                        <p class="mb-0 mt-1 small text-success d-none" id="dz-done-{{ $addon['code'] }}">
                                            <i class="ri-check-line"></i> {{ __('Uploaded successfully') }}
                                        </p>
                                    </div>

                                    {{-- Step 2: License key (if required) --}}
                                    @if ($addon['license'])
                                    <h6 class="fw-700 mb-2">
                                        <span class="badge bg-secondary me-1">2</span>
                                        {{ __('License Details') }}
                                    </h6>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-600">{{ __('Purchase Email') }}</label>
                                            <input type="email" class="form-control form-control-sm"
                                                   id="email-{{ $addon['code'] }}"
                                                   placeholder="{{ __('your@email.com') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-600">{{ __('Purchase Code') }}</label>
                                            <input type="text" class="form-control form-control-sm"
                                                   id="pcode-{{ $addon['code'] }}"
                                                   placeholder="{{ __('xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx') }}">
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Result area --}}
                                    <div id="install-result-{{ $addon['code'] }}" style="display:none"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm"
                                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                    <button type="button"
                                            class="btn btn-primary btn-sm"
                                            onclick="executeAddonInstall('{{ $addon['code'] }}', {{ $addon['license'] ? 1 : 0 }})"
                                            id="install-btn-{{ $addon['code'] }}">
                                        <i class="ri-play-circle-line me-1"></i>
                                        {{ $addon['is_installed'] ? __('Update Now') : __('Install Now') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</div>
@endsection

@push('script')
<script>
const CSRF      = document.querySelector('meta[name="csrf-token"]').content;
const STORE_URL = "{{ route('admin.addon.store') }}";
const EXEC_URL  = "{{ route('admin.addon.execute') }}";

// Selected files map: code → File
const selectedFiles = {};

function handleFileSelect(input, code) {
    const file = input.files[0];
    if (!file) return;
    selectedFiles[code] = file;

    document.getElementById('dz-placeholder-' + code).style.display = 'none';
    document.getElementById('dz-selected-' + code).style.display   = 'block';
    document.getElementById('dz-filename-' + code).textContent      = file.name;
}

function uploadAddonZip(code) {
    const file = selectedFiles[code];
    if (!file) { alert('{{ __("Please select a ZIP file first.") }}'); return; }

    const formData = new FormData();
    formData.append('_token',      CSRF);
    formData.append('code',        code);
    formData.append('update_file', file);

    // Show progress
    document.getElementById('dz-selected-' + code).style.display    = 'none';
    document.getElementById('dz-progress-' + code).style.display    = '';
    const bar = document.getElementById('dz-bar-' + code);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', STORE_URL);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Accept', 'application/json');

    xhr.upload.onprogress = e => {
        if (e.lengthComputable) {
            bar.style.width = Math.round(e.loaded / e.total * 100) + '%';
        }
    };

    xhr.onload = () => {
        document.getElementById('dz-progress-' + code).style.display = 'none';
        const done = document.getElementById('dz-done-' + code);
        done.classList.remove('d-none');

        const area = document.getElementById('dz-area-' + code);
        area.classList.add('has-file');

        // Mark as ready
        area.dataset.ready = '1';
    };

    xhr.onerror = () => {
        document.getElementById('dz-progress-' + code).style.display = 'none';
        alert('{{ __("Upload failed. Please try again.") }}');
        // Reset
        document.getElementById('dz-selected-' + code).style.display = 'block';
    };

    xhr.send(formData);
}

function executeAddonInstall(code, needsLicense) {
    const area = document.getElementById('dz-area-' + code);
    const resultDiv = document.getElementById('install-result-' + code);
    const btn = document.getElementById('install-btn-' + code);

    // Must have an uploaded file
    const hasExisting = area.classList.contains('has-file');
    if (!hasExisting) {
        showInstallResult(code, false, '{{ __("Please upload the addon ZIP first.") }}');
        return;
    }

    const email = needsLicense ? (document.getElementById('email-' + code)?.value ?? '') : '';
    const pcode = needsLicense ? (document.getElementById('pcode-' + code)?.value ?? '') : '';

    if (needsLicense && (!email || !pcode)) {
        showInstallResult(code, false, '{{ __("Purchase email and code are required.") }}');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>{{ __("Installing...") }}';

    const formData = new FormData();
    formData.append('_token',       CSRF);
    formData.append('code',         code);
    formData.append('licenseStatus', needsLicense ? 1 : 0);
    if (needsLicense) {
        formData.append('email',         email);
        formData.append('purchase_code', pcode);
    }

    fetch(EXEC_URL, {
        method:  'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        body:    formData,
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-play-circle-line me-1"></i>{{ __("Install Now") }}';

        if (data && data.status === true) {
            showInstallResult(code, true, data.message || '{{ __("Addon installed successfully! Reloading...") }}');
            setTimeout(() => location.reload(), 2500);
        } else {
            showInstallResult(code, false, data.message || '{{ __("Installation failed. Check logs for details.") }}');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-play-circle-line me-1"></i>{{ __("Install Now") }}';
        showInstallResult(code, false, '{{ __("A network error occurred. Please try again.") }}');
    });
}

function showInstallResult(code, success, message) {
    const div = document.getElementById('install-result-' + code);
    div.style.display = '';
    div.innerHTML = `
        <div class="alert alert-${success ? 'success' : 'danger'} small mt-2">
            <i class="ri-${success ? 'check' : 'close'}-circle-line me-1"></i>
            ${message}
        </div>`;
}
</script>
@endpush
