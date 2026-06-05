@php
    $navChangePasswordActiveClass = 'active';
    $navProfileMMShowClass        = 'mm-active';
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
                                <h3 class="mb-sm-0">{{ __('Change Password') }}</h3>
                            </div>
                            <div class="page-title-right">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('accountant.profile') }}">{{ __('My Profile') }}</a></li>
                                    <li class="breadcrumb-item active">{{ __('Change Password') }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-5">
                        <div class="card border shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="ri-lock-password-line me-1"></i> {{ __('Update Your Password') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('accountant.change-password.update') }}" method="POST"
                                      class="ajax" data-handler="passwordShowMessage">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Current Password') }} <span class="text-danger">*</span></label>
                                        <input type="password" name="current_password" class="form-control" required
                                               placeholder="{{ __('Enter current password') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('New Password') }} <span class="text-danger">*</span></label>
                                        <input type="password" name="password" class="form-control" required
                                               placeholder="{{ __('Enter new password') }}">
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">{{ __('Confirm New Password') }} <span class="text-danger">*</span></label>
                                        <input type="password" name="password_confirmation" class="form-control" required
                                               placeholder="{{ __('Confirm new password') }}">
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ri-save-line me-1"></i> {{ __('Update Password') }}
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
    window.passwordShowMessage = function (response) {
        if (response && response.status === true) {
            toastr.success(response.message || '{{ __("Password updated successfully.") }}');
            $('form')[0].reset();
        } else {
            commonHandler(response);
        }
    };
</script>
@endpush
