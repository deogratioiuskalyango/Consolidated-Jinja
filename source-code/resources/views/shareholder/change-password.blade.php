@extends('shareholder.layouts.app')
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">

                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-flex flex-column flex-sm-row align-items-sm-center justify-content-between g-20">
                            <div class="page-title-left">
                                <h2 class="mb-sm-0">{{ __('Change Password') }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3 justify-content-center">
                    <div class="col-lg-6 col-md-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">
                                    <i class="ri-lock-password-line me-2 text-primary"></i>{{ __('Update Your Password') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('shareholder.change-password.update') }}" method="POST">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="current_password" class="form-label fw-semibold">
                                            {{ __('Current Password') }} <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password"
                                                   class="form-control @error('current_password') is-invalid @enderror"
                                                   id="current_password" name="current_password"
                                                   placeholder="{{ __('Enter your current password') }}"
                                                   autocomplete="current-password" required>
                                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                                    data-target="#current_password">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            @error('current_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label fw-semibold">
                                            {{ __('New Password') }} <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password"
                                                   class="form-control @error('password') is-invalid @enderror"
                                                   id="password" name="password"
                                                   placeholder="{{ __('Enter new password') }}"
                                                   autocomplete="new-password" required>
                                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                                    data-target="#password">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="text-muted">{{ __('Minimum 8 characters.') }}</small>
                                    </div>

                                    <div class="mb-4">
                                        <label for="password_confirmation" class="form-label fw-semibold">
                                            {{ __('Confirm New Password') }} <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password"
                                                   class="form-control @error('password_confirmation') is-invalid @enderror"
                                                   id="password_confirmation" name="password_confirmation"
                                                   placeholder="{{ __('Confirm new password') }}"
                                                   autocomplete="new-password" required>
                                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                                    data-target="#password_confirmation">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            @error('password_confirmation')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="ri-save-line me-1"></i>{{ __('Update Password') }}
                                    </button>
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
$(document).ready(function () {
    $('.toggle-password').on('click', function () {
        var target = $($(this).data('target'));
        var icon = $(this).find('i');
        if (target.attr('type') === 'password') {
            target.attr('type', 'text');
            icon.removeClass('ri-eye-line').addClass('ri-eye-off-line');
        } else {
            target.attr('type', 'password');
            icon.removeClass('ri-eye-off-line').addClass('ri-eye-line');
        }
    });
});
</script>
@endpush
