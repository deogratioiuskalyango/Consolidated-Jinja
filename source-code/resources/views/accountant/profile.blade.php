@php
    $navProfileActiveClass   = 'active';
    $navProfileMMShowClass   = 'mm-active';
@endphp
@extends('accountant.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">

                {{-- Page Title & Breadcrumb --}}
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between border-bottom mb-20">
                            <div class="page-title-left">
                                <h3 class="mb-sm-0">{{ __('My Profile') }}</h3>
                            </div>
                            <div class="page-title-right">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                    <li class="breadcrumb-item active">{{ __('My Profile') }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Left: Profile Card --}}
                    <div class="col-md-4 mb-4">
                        <div class="card border h-100">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    @if(auth()->user()->image)
                                        <img src="{{ asset(auth()->user()->image) }}"
                                             alt="{{ auth()->user()->name }}"
                                             class="rounded-circle"
                                             style="width:120px;height:120px;object-fit:cover;border:3px solid #dee2e6;">
                                    @else
                                        <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center"
                                             style="width:120px;height:120px;font-size:48px;color:#fff;">
                                            <i class="ri-user-line"></i>
                                        </div>
                                    @endif
                                </div>
                                <h5 class="fw-bold mb-1">{{ auth()->user()->name }}</h5>
                                <p class="text-muted mb-1">
                                    <i class="ri-mail-line me-1"></i>{{ auth()->user()->email }}
                                </p>
                                @if(auth()->user()->accountant_id ?? false)
                                <p class="text-muted mb-1">
                                    <i class="ri-profile-line me-1"></i>
                                    {{ __('ID') }}: <strong>{{ auth()->user()->accountant_id }}</strong>
                                </p>
                                @endif
                                @if(auth()->user()->designation ?? false)
                                <p class="text-muted mb-1">
                                    <i class="ri-briefcase-line me-1"></i>
                                    {{ auth()->user()->designation }}
                                </p>
                                @endif
                                <div class="mt-2">
                                    @if(auth()->user()->status == 1)
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right: Edit Form --}}
                    <div class="col-md-8 mb-4">
                        <div class="card border h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="ri-edit-line me-1"></i> {{ __('Edit Profile') }}</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('accountant.profile.update') }}" method="POST"
                                      enctype="multipart/form-data" class="ajax" data-handler="profileShowMessage">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('First Name') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="first_name" class="form-control"
                                                   value="{{ auth()->user()->first_name ?? '' }}" required
                                                   placeholder="{{ __('Enter first name') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="last_name" class="form-control"
                                                   value="{{ auth()->user()->last_name ?? '' }}" required
                                                   placeholder="{{ __('Enter last name') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('Contact Number') }}</label>
                                            <input type="text" name="contact_number" class="form-control"
                                                   value="{{ auth()->user()->contact_number ?? '' }}"
                                                   placeholder="{{ __('Enter contact number') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('Profile Photo') }}</label>
                                            <input type="file" name="image" class="form-control" accept="image/*">
                                            @if(auth()->user()->image)
                                            <small class="text-muted">{{ __('Leave empty to keep current photo') }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ri-save-line me-1"></i> {{ __('Save Changes') }}
                                        </button>
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
@endsection

@push('script')
<script>
    window.profileShowMessage = function (response) {
        if (response && response.status === true) {
            toastr.success(response.message || '{{ __("Profile updated successfully.") }}');
            setTimeout(function () { location.reload(); }, 800);
        } else {
            commonHandler(response);
        }
    };
</script>
@endpush
