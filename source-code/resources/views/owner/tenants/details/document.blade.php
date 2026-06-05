@extends('owner.layouts.app')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <!-- Page Content Wrapper Start -->
                <div class="page-content-wrapper bg-white p-30 radius-20">
                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div
                                class="page-title-box d-sm-flex align-items-center justify-content-between border-bottom mb-20">
                                <div class="page-title-left">
                                    <h3 class="mb-sm-0">{{ $pageTitle }}</h3>
                                </div>
                                <div class="page-title-right">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}"
                                                title="Dashboard">{{ __('Dashboard') }}</a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('owner.tenant.index') }}"
                                                title="Home">{{ __('Tenants') }}</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                                    </ol>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <!-- Tenants Details Layout Wrap Area row Start -->
                    <div class="tenants-details-layout-wrap position-relative">
                        <div class="row">
                            <!-- Account settings Left Side Start-->
                            <div class="col-md-12 col-lg-12 col-xl-4 col-xxl-3">
                                <div class="account-settings-leftside bg-white theme-border radius-4 p-20 mb-25">
                                    <div class="tenants-details-leftsidebar-wrap d-flex">
                                        @include('owner.tenants.details.sidenav')
                                    </div>
                                </div>
                            </div>
                            <!-- Account settings Area Right Side Start-->
                            <div class="col-md-12 col-lg-12 col-xl-8 col-xxl-9">
                                <div class="account-settings-rightside bg-off-white theme-border radius-4 p-25">
                                    <!-- Tenants Details Documents Start -->

                                    {{-- Section header --}}
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <h4 class="mb-0">{{ __('Documents') }}</h4>
                                        @if ($tenant->documents->count() > 0)
                                            <span class="badge bg-success">
                                                {{ $tenant->documents->count() }} {{ Str::plural('file', $tenant->documents->count()) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                0 files
                                            </span>
                                        @endif
                                    </div>
                                    <hr class="mb-4">

                                    @if ($tenant->documents->count() > 0)
                                        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
                                            @foreach ($tenant->documents as $document)
                                                @php
                                                    $ext = strtolower(pathinfo($document->file_name, PATHINFO_EXTENSION));
                                                    if ($ext === 'pdf') {
                                                        $iconClass = 'ri-file-pdf-line text-danger';
                                                    } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                                                        $iconClass = 'ri-image-line text-primary';
                                                    } elseif (in_array($ext, ['doc', 'docx'])) {
                                                        $iconClass = 'ri-file-word-line text-info';
                                                    } elseif (in_array($ext, ['xls', 'xlsx'])) {
                                                        $iconClass = 'ri-file-excel-line text-success';
                                                    } else {
                                                        $iconClass = 'ri-file-3-line text-secondary';
                                                    }
                                                @endphp
                                                <div class="col">
                                                    <div class="card h-100 border shadow-sm">
                                                        <div class="card-body d-flex flex-column align-items-start gap-2 pb-2">
                                                            {{-- Icon circle --}}
                                                            <div class="d-flex align-items-center justify-content-center rounded-circle bg-light flex-shrink-0"
                                                                style="width:48px;height:48px;">
                                                                <i class="{{ $iconClass }} fs-4"></i>
                                                            </div>
                                                            {{-- Filename --}}
                                                            <p class="mb-0 text-truncate w-100 fw-medium small"
                                                                title="{{ $document->file_name }}">
                                                                {{ $document->file_name }}
                                                            </p>
                                                            {{-- Extension badge + upload date --}}
                                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                                @if ($ext)
                                                                    <span class="badge bg-light text-dark border text-uppercase"
                                                                        style="font-size:0.7rem;">{{ $ext }}</span>
                                                                @endif
                                                                <small class="text-muted">
                                                                    <i class="ri-time-line me-1"></i>
                                                                    {{ $document->created_at->format('d M Y') }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div class="card-footer bg-transparent border-top-0 pt-0 px-3 pb-3">
                                                            <a href="{{ $document->FileUrl }}" download
                                                                class="btn btn-sm btn-outline-primary w-100">
                                                                <i class="ri-download-2-line me-1"></i>{{ __('Download') }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        {{-- Empty state --}}
                                        <div class="d-flex flex-column align-items-center justify-content-center py-5 text-center">
                                            <i class="ri-file-shield-2-line fs-1 text-muted mb-3"></i>
                                            <p class="fw-medium mb-1">{{ __('No documents uploaded.') }}</p>
                                            <small class="text-muted">{{ __('Documents submitted by the tenant appear here.') }}</small>
                                        </div>
                                    @endif

                                    <!-- Tenants Details Documents End -->
                                </div>
                            </div>
                            <!-- Account settings Area Right Side End-->
                        </div>
                    </div>
                    <!-- Tenants Details Layout Wrap Area row End -->
                </div>
                <!-- Page Content Wrapper End -->
            </div>
        </div>
        <!-- End Page-content -->
    </div>
@endsection
