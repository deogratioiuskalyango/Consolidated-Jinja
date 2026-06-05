@extends(getLayout() . '.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">

                {{-- Page title --}}
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between border-bottom mb-20">
                            <div class="page-title-left">
                                <h3 class="mb-sm-0">{{ $pageTitle }}</h3>
                            </div>
                            <div class="page-title-right">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route(getLayout() . '.dashboard') }}">{{ __('Dashboard') }}</a>
                                    </li>
                                    <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Flash messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-20" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-20" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-20" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="settings-page-layout-wrap position-relative">
                    <div class="row">
                        <div class="col-12">
                            <div class="account-settings-rightside bg-off-white theme-border radius-4 p-25">
                                <form action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data" id="profileForm">
                                    @csrf

                                    {{-- ───── Personal Information ───── --}}
                                    <div class="settings-inner-box bg-white theme-border radius-4 mb-25">
                                        <div class="settings-inner-box-title border-bottom p-20">
                                            <h4>{{ __('Personal Information') }}</h4>
                                        </div>
                                        <div class="settings-inner-box-fields p-20 pb-0">
                                            <div class="row rg-24">

                                                {{-- Avatar --}}
                                                <div class="col-12 d-flex align-items-center justify-content-between flex-wrap mb-10">
                                                    <div class="upload-profile-photo-box upload-profile-photo-with-delete-btn">
                                                        <div class="profile-user position-relative d-inline-block">
                                                            <img id="profilePhotoPreview"
                                                                src="{{ auth()->user()->image ?: asset('assets/images/users/empty-user.jpg') }}"
                                                                class="rounded-circle avatar-xl default-user-profile-image"
                                                                alt="profile photo">
                                                            <div class="avatar-xs p-0 rounded-circle default-profile-photo-edit">
                                                                <input id="default-profile-img-file-input"
                                                                    type="file"
                                                                    name="image"
                                                                    class="default-profile-img-file-input"
                                                                    accept="image/*">
                                                                <label for="default-profile-img-file-input"
                                                                    class="default-profile-photo-edit avatar-xs"
                                                                    title="{{ __('Change photo') }}">
                                                                    <span class="avatar-title rounded-circle">
                                                                        <i class="ri-camera-fill"></i>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="ms-3 mt-2">
                                                            <small class="text-muted d-block">{{ __('Click the camera icon to change your photo') }}</small>
                                                            <small class="text-muted">{{ __('Accepts: JPG, PNG, WebP, GIF, BMP, HEIC, TIFF, AVIF') }}</small>
                                                        </div>
                                                    </div>
                                                    @if(in_array(auth()->user()->role, [USER_ROLE_TENANT, USER_ROLE_MAINTAINER]))
                                                        @if(isset($deletionRequest) && $deletionRequest && $deletionRequest->status === DELETION_REQUEST_PENDING)
                                                            {{-- Pending: show status badge, no button --}}
                                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                                <span class="badge bg-warning text-dark px-3 py-2" style="font-size:13px;">
                                                                    <i class="ri-time-line me-1"></i>{{ __('Deletion Pending Admin Review') }}
                                                                </span>
                                                                <small class="text-muted">{{ __('Submitted') }} {{ $deletionRequest->created_at->diffForHumans() }}</small>
                                                            </div>
                                                        @elseif(isset($deletionRequest) && $deletionRequest && $deletionRequest->status === DELETION_REQUEST_REJECTED)
                                                            {{-- Rejected: show why + allow re-request --}}
                                                            <div class="d-flex flex-column gap-1">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <span class="badge bg-danger px-3 py-2" style="font-size:13px;">
                                                                        <i class="ri-close-circle-line me-1"></i>{{ __('Previous Request Rejected') }}
                                                                    </span>
                                                                    <button type="button" class="theme-btn-red btn-sm" id="deleteMyAccountBtn">
                                                                        {{ __('Request Again') }}
                                                                    </button>
                                                                </div>
                                                                @if($deletionRequest->admin_note)
                                                                    <small class="text-muted"><strong>{{ __('Reason:') }}</strong> {{ $deletionRequest->admin_note }}</small>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <button type="button" class="theme-btn-red" id="deleteMyAccountBtn">
                                                                {{ __('Delete my Account') }}
                                                            </button>
                                                        @endif
                                                    @endif
                                                </div>

                                                <div class="col-md-4 mb-25">
                                                    <label class="label-text-title color-heading font-medium mb-2">
                                                        {{ __('First Name') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                                        name="first_name"
                                                        placeholder="{{ __('First Name') }}"
                                                        value="{{ old('first_name', auth()->user()->first_name) }}"
                                                        required>
                                                    @error('first_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4 mb-25">
                                                    <label class="label-text-title color-heading font-medium mb-2">
                                                        {{ __('Last Name') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                                        name="last_name"
                                                        placeholder="{{ __('Last Name') }}"
                                                        value="{{ old('last_name', auth()->user()->last_name) }}"
                                                        required>
                                                    @error('last_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4 mb-25">
                                                    <label class="label-text-title color-heading font-medium mb-2">
                                                        {{ __('Email') }} <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                        name="email"
                                                        placeholder="{{ __('Email') }}"
                                                        value="{{ old('email', auth()->user()->email) }}"
                                                        @if(!in_array(auth()->user()->role, [USER_ROLE_ADMIN, USER_ROLE_OWNER])) readonly @endif
                                                        required>
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4 mb-25">
                                                    <label class="label-text-title color-heading font-medium mb-2">{{ __('Contact Number') }}</label>
                                                    <input type="text" class="form-control @error('contact_number') is-invalid @enderror"
                                                        name="contact_number"
                                                        placeholder="{{ __('Contact Number') }}"
                                                        value="{{ old('contact_number', auth()->user()->contact_number) }}">
                                                    @error('contact_number')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4 mb-25">
                                                    <label class="label-text-title color-heading font-medium mb-2">
                                                        <i class="ri-whatsapp-line me-1 text-success"></i>{{ __('WhatsApp Number') }}
                                                    </label>
                                                    <input type="text" class="form-control @error('whatsapp_number') is-invalid @enderror"
                                                        name="whatsapp_number"
                                                        placeholder="{{ __('e.g. 628123456789') }}"
                                                        value="{{ old('whatsapp_number', auth()->user()->whatsapp_number) }}">
                                                    <small class="text-muted">{{ __('Include country code. Leave blank to opt out of WhatsApp notifications.') }}</small>
                                                    @error('whatsapp_number')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                @if(auth()->user()->role != USER_ROLE_TEAM_MEMBER)
                                                    <div class="col-md-4 mb-25">
                                                        <label class="label-text-title color-heading font-medium mb-2">{{ __('Date of Birth') }}</label>
                                                        <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                                            name="date_of_birth"
                                                            value="{{ old('date_of_birth', auth()->user()->date_of_birth) }}">
                                                        @error('date_of_birth')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-4 mb-25">
                                                        <label class="label-text-title color-heading font-medium mb-2">{{ __('NID Number') }}</label>
                                                        <input type="text" class="form-control @error('nid_number') is-invalid @enderror"
                                                            name="nid_number"
                                                            placeholder="{{ __('NID / Passport Number') }}"
                                                            value="{{ old('nid_number', auth()->user()->nid_number) }}">
                                                        @error('nid_number')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ───── Owner: Print / Invoice Details ───── --}}
                                    @if(auth()->user()->role == USER_ROLE_OWNER)
                                        @php $owner = $owner ?? null; @endphp
                                        <div class="settings-inner-box bg-white theme-border radius-4 mb-25">
                                            <div class="settings-inner-box-title border-bottom p-20">
                                                <h4>{{ __('Invoice & Print Details') }}</h4>
                                                <small class="text-muted">{{ __('These appear on printed invoices and receipts.') }}</small>
                                            </div>
                                            <div class="settings-inner-box-fields p-20 pb-0">
                                                <div class="row">
                                                    <div class="col-md-4 mb-25">
                                                        <label class="label-text-title color-heading font-medium mb-2">{{ __('Business / Print Name') }}</label>
                                                        <input type="text" class="form-control"
                                                            name="print_name"
                                                            placeholder="{{ __('Business name') }}"
                                                            value="{{ old('print_name', optional($owner)->print_name) }}">
                                                    </div>
                                                    <div class="col-md-4 mb-25">
                                                        <label class="label-text-title color-heading font-medium mb-2">{{ __('Print Address') }}</label>
                                                        <input type="text" class="form-control"
                                                            name="print_address"
                                                            placeholder="{{ __('Business address') }}"
                                                            value="{{ old('print_address', optional($owner)->print_address) }}">
                                                    </div>
                                                    <div class="col-md-4 mb-25">
                                                        <label class="label-text-title color-heading font-medium mb-2">{{ __('Print Contact') }}</label>
                                                        <input type="text" class="form-control"
                                                            name="print_contact"
                                                            placeholder="{{ __('Business contact') }}"
                                                            value="{{ old('print_contact', optional($owner)->print_contact) }}">
                                                    </div>
                                                    <div class="col-md-4 mb-25">
                                                        <label class="label-text-title color-heading font-medium mb-2">{{ __('Company Logo') }}</label>
                                                        <div class="upload-profile-photo-box">
                                                            <div class="profile-user position-relative d-inline-block">
                                                                <img id="logoPreview"
                                                                    src="{{ optional($owner)->file_name ? assetUrl((optional($owner)->folder_name ?? '') . '/' . $owner->file_name) : asset('assets/images/users/empty-user.jpg') }}"
                                                                    class="rounded-circle avatar-xl user-profile-image"
                                                                    alt="company logo">
                                                                <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                                                    <input id="profile-img-file-input"
                                                                        name="print_logo"
                                                                        type="file"
                                                                        class="profile-img-file-input"
                                                                        accept="image/*">
                                                                    <label for="profile-img-file-input"
                                                                        class="profile-photo-edit avatar-xs"
                                                                        title="{{ __('Upload Logo') }}">
                                                                        <span class="avatar-title rounded-circle">
                                                                            <i class="ri-camera-fill"></i>
                                                                        </span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted d-block mt-1">{{ __('Accepts any image format') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- ───── Tenant: Address & Other Info ───── --}}
                                    @if(auth()->user()->role == USER_ROLE_TENANT)
                                        <div class="settings-inner-box bg-white theme-border radius-4 mb-25">
                                            <div class="settings-inner-box-title border-bottom p-20">
                                                <h4>{{ __('Previous Address') }}</h4>
                                            </div>
                                            <div class="settings-inner-box-fields p-20 pb-0">
                                                <div class="row">
                                                    <div class="col-md-4 mb-25">
                                                        <label class="label-text-title color-heading font-medium mb-2">{{ __('Address') }}</label>
                                                        <input type="text" class="form-control" name="previous_address"
                                                            placeholder="{{ __('Street address') }}"
                                                            value="{{ old('previous_address', optional($details)->previous_address) }}">
                                                    </div>
                                                    <div class="col-md-4 mb-25">
                                                        <label class="label-text-title color-heading font-medium mb-2">{{ __('Country') }}</label>
                                                        <input type="text" class="form-control" name="previous_country_id"
                                                            placeholder="{{ __('Country') }}"
                                                            value="{{ old('previous_country_id', optional($details)->previous_country_id) }}">
                                                    </div>
                                                    <div class="col-md-4 mb-25">
                                                        <label class="label-text-title color-heading font-medium mb-2">{{ __('State / Region') }}</label>
                                                        <input type="text" class="form-control" name="previous_state_id"
                                                            placeholder="{{ __('State or region') }}"
                                                            value="{{ old('previous_state_id', optional($details)->previous_state_id) }}">
                                                    </div>
                                                    <div class="col-md-4 mb-25">
                                                        <label class="label-text-title color-heading font-medium mb-2">{{ __('City') }}</label>
                                                        <input type="text" class="form-control" name="previous_city_id"
                                                            placeholder="{{ __('City') }}"
                                                            value="{{ old('previous_city_id', optional($details)->previous_city_id) }}">
                                                    </div>
                                                    <div class="col-md-4 mb-25">
                                                        <label class="label-text-title color-heading font-medium mb-2">{{ __('Zip / Postal Code') }}</label>
                                                        <input type="text" class="form-control" name="previous_zip_code"
                                                            placeholder="{{ __('Zip code') }}"
                                                            value="{{ old('previous_zip_code', optional($details)->previous_zip_code) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="settings-inner-box bg-white theme-border radius-4 mb-25">
                                            <div class="settings-inner-box-title border-bottom p-20">
                                                <h4>{{ __('Permanent Address') }}</h4>
                                            </div>
                                            <div class="settings-inner-box-fields p-20 pb-0">
                                                <div class="row">
                                                    <div class="col-md-4 mb-25">
                                                        <label class="label-text-title color-heading font-medium mb-2">{{ __('Address') }}</label>
                                                        <input type="text" class="form-control" name="permanent_address"
                                                            placeholder="{{ __('Street address') }}"
                                                            value="{{ old('permanent_address', optional($details)->permanent_address) }}">
                                                    </div>
                                                    <div class="col-md-4 mb-25">
                                                        <label class="label-text-title color-heading font-medium mb-2">{{ __('Country') }}</label>
                                                        <input type="text" class="form-control" name="permanent_country_id"
                                                            placeholder="{{ __('Country') }}"
                                                            value="{{ old('permanent_country_id', optional($details)->permanent_country_id) }}">
                                                    </div>
                                                    <div class="col-md-4 mb-25">
                                                        <label class="label-text-title color-heading font-medium mb-2">{{ __('State / Region') }}</label>
                                                        <input type="text" class="form-control" name="permanent_state_id"
                                                            placeholder="{{ __('State or region') }}"
                                                            value="{{ old('permanent_state_id', optional($details)->permanent_state_id) }}">
                                                    </div>
                                                    <div class="col-md-4 mb-25">
                                                        <label class="label-text-title color-heading font-medium mb-2">{{ __('City') }}</label>
                                                        <input type="text" class="form-control" name="permanent_city_id"
                                                            placeholder="{{ __('City') }}"
                                                            value="{{ old('permanent_city_id', optional($details)->permanent_city_id) }}">
                                                    </div>
                                                    <div class="col-md-4 mb-25">
                                                        <label class="label-text-title color-heading font-medium mb-2">{{ __('Zip / Postal Code') }}</label>
                                                        <input type="text" class="form-control" name="permanent_zip_code"
                                                            placeholder="{{ __('Zip code') }}"
                                                            value="{{ old('permanent_zip_code', optional($details)->permanent_zip_code) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="settings-inner-box bg-white theme-border radius-4 mb-25">
                                            <div class="settings-inner-box-title border-bottom p-20">
                                                <h4>{{ __('Additional Information') }}</h4>
                                            </div>
                                            <div class="settings-inner-box-fields p-20 pb-0">
                                                <div class="row">
                                                    <div class="col-md-4 mb-25">
                                                        <label class="label-text-title color-heading font-medium mb-2">{{ __('Employment / Occupation') }}</label>
                                                        <input type="text" class="form-control" name="job"
                                                            placeholder="{{ __('Job title or employer') }}"
                                                            value="{{ old('job', optional($tenant)->job) }}">
                                                    </div>
                                                    <div class="col-md-4 mb-25">
                                                        <label class="label-text-title color-heading font-medium mb-2">{{ __('Family Members') }}</label>
                                                        <input type="number" class="form-control" name="family_member"
                                                            min="0" placeholder="{{ __('Number of people') }}"
                                                            value="{{ old('family_member', optional($tenant)->family_member) }}">
                                                    </div>
                                                    <div class="col-md-4 mb-25">
                                                        <label class="label-text-title color-heading font-medium mb-2">{{ __('Age') }}</label>
                                                        <input type="number" class="form-control" name="age"
                                                            min="0" max="120" placeholder="{{ __('Age') }}"
                                                            value="{{ old('age', optional($tenant)->age) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Save button --}}
                                    <div class="row">
                                        <div class="col-12 mb-25 d-flex justify-content-end gap-2">
                                            <a href="{{ route(getLayout() . '.dashboard') }}" class="theme-btn-back">
                                                {{ __('Cancel') }}
                                            </a>
                                            <button type="submit" class="theme-btn" id="saveProfileBtn">
                                                <span id="saveBtnText">{{ __('Save Changes') }}</span>
                                                <span id="saveBtnSpinner" class="spinner-border spinner-border-sm ms-1 d-none" role="status"></span>
                                            </button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete account request modal --}}
@if(in_array(auth()->user()->role, [USER_ROLE_TENANT, USER_ROLE_MAINTAINER]))
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <div>
                    <h5 class="modal-title mb-0">{{ __('Request Account Deletion') }}</h5>
                    <small class="text-muted">{{ __('An admin will review your request before the account is removed.') }}</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="ajax" action="{{ route('delete-my-account') }}" method="POST"
                autocomplete="off" data-handler="getShowMessage">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning mb-3">
                        <i class="ri-information-line me-2"></i>
                        {{ __('Your account will not be deleted immediately. A deletion request will be sent to the admin for approval. You will be logged out after submitting.') }}
                    </div>
                    <div class="mb-3">
                        <label class="label-text-title color-heading font-medium mb-2">
                            {{ __('Confirm Email') }} <strong class="text-danger">*</strong>
                        </label>
                        <p class="text-muted small mb-2">{{ __('Type') }} <strong>{{ auth()->user()->email }}</strong> {{ __('to confirm.') }}</p>
                        <input type="text" class="form-control" name="email" autocomplete="off"
                               placeholder="{{ auth()->user()->email }}">
                    </div>
                    <div class="mb-3">
                        <label class="label-text-title color-heading font-medium mb-2">
                            {{ __('Password') }} <strong class="text-danger">*</strong>
                        </label>
                        <input type="password" class="form-control" name="password"
                               placeholder="{{ __('Your current password') }}">
                    </div>
                    <div class="mb-3">
                        <label class="label-text-title color-heading font-medium mb-2">
                            {{ __('Reason') }} <span class="text-muted">({{ __('optional') }})</span>
                        </label>
                        <textarea name="reason" class="form-control" rows="2"
                                  placeholder="{{ __('Why do you want to delete your account?') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-start">
                    <button type="button" class="theme-btn-back me-3" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="theme-btn-red">
                        <i class="ri-send-plane-line me-1"></i>{{ __('Submit Deletion Request') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('script')
<script src="{{ asset('/') }}assets/js/pages/profile-setting.init.js"></script>
<script src="{{ asset('/') }}assets/js/pages/default-profile-setting.init.js"></script>
<script src="{{ asset('assets/js/custom/delete-my-account.js') }}"></script>
<script>
"use strict";

// Live preview for profile photo
document.getElementById('default-profile-img-file-input')?.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const preview = document.getElementById('profilePhotoPreview');
    if (preview) {
        preview.src = URL.createObjectURL(file);
        preview.onload = () => URL.revokeObjectURL(preview.src);
    }
});

// Live preview for company logo
document.getElementById('profile-img-file-input')?.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const preview = document.getElementById('logoPreview');
    if (preview) {
        preview.src = URL.createObjectURL(file);
        preview.onload = () => URL.revokeObjectURL(preview.src);
    }
});

// Show spinner on submit
document.getElementById('profileForm')?.addEventListener('submit', function () {
    document.getElementById('saveBtnText')?.classList.add('d-none');
    document.getElementById('saveBtnSpinner')?.classList.remove('d-none');
    document.getElementById('saveProfileBtn').disabled = true;
});
</script>
@endpush
