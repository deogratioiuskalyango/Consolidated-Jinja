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
                                <h2 class="mb-sm-0">{{ __('My Profile') }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    {{-- Left: Profile Picture & Info --}}
                    <div class="col-lg-4 mb-4">
                        <div class="card border-0 shadow-sm text-center">
                            <div class="card-body py-4">
                                <div class="mb-3">
                                    <img src="{{ $user->image }}" alt="{{ $user->name }}"
                                         class="rounded-circle shadow"
                                         style="width:120px;height:120px;object-fit:cover;">
                                </div>
                                <h5 class="mb-1">{{ $user->name }}</h5>
                                <p class="text-muted small mb-1">{{ $user->email }}</p>
                                @if($user->phone)
                                    <p class="text-muted small mb-1">
                                        <i class="ri-phone-line me-1"></i>{{ $user->phone }}
                                    </p>
                                @endif
                                <span class="badge bg-primary mt-2">{{ __('Shareholder') }}</span>

                                @if($shareholder)
                                    <hr class="my-3">
                                    <div class="text-start">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted small">{{ __('Shareholder Since') }}</span>
                                            <strong class="small">{{ $shareholder->created_at->format('M d, Y') }}</strong>
                                        </div>
                                        @if(isset($shareholder->total_shares))
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted small">{{ __('Total Shares') }}</span>
                                                <strong class="small">{{ number_format($shareholder->total_shares, 4) }}</strong>
                                            </div>
                                        @endif
                                        @if(isset($shareholder->shareholder_number))
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted small">{{ __('Shareholder #') }}</span>
                                                <strong class="small">{{ $shareholder->shareholder_number }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Right: Edit Form --}}
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">{{ __('Edit Profile') }}</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('shareholder.profile.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <label for="first_name" class="form-label fw-semibold">{{ __('First Name') }}</label>
                                            <input type="text"
                                                   class="form-control @error('first_name') is-invalid @enderror"
                                                   id="first_name" name="first_name"
                                                   value="{{ old('first_name', $user->first_name ?? '') }}"
                                                   placeholder="{{ __('First name') }}">
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="last_name" class="form-label fw-semibold">{{ __('Last Name') }}</label>
                                            <input type="text"
                                                   class="form-control @error('last_name') is-invalid @enderror"
                                                   id="last_name" name="last_name"
                                                   value="{{ old('last_name', $user->last_name ?? '') }}"
                                                   placeholder="{{ __('Last name') }}">
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="phone" class="form-label fw-semibold">{{ __('Phone') }}</label>
                                            <input type="text"
                                                   class="form-control @error('phone') is-invalid @enderror"
                                                   id="phone" name="phone"
                                                   value="{{ old('phone', $user->phone ?? '') }}"
                                                   placeholder="{{ __('Phone number') }}">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="email" class="form-label fw-semibold">{{ __('Email') }}</label>
                                            <input type="email" class="form-control bg-light"
                                                   id="email" value="{{ $user->email }}" disabled>
                                            <small class="text-muted">{{ __('Email cannot be changed here.') }}</small>
                                        </div>

                                        <div class="col-12">
                                            <label for="address" class="form-label fw-semibold">{{ __('Address') }}</label>
                                            <input type="text"
                                                   class="form-control @error('address') is-invalid @enderror"
                                                   id="address" name="address"
                                                   value="{{ old('address', $user->address ?? '') }}"
                                                   placeholder="{{ __('Street address') }}">
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="city" class="form-label fw-semibold">{{ __('City') }}</label>
                                            <input type="text"
                                                   class="form-control @error('city') is-invalid @enderror"
                                                   id="city" name="city"
                                                   value="{{ old('city', $user->city ?? '') }}"
                                                   placeholder="{{ __('City') }}">
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="state" class="form-label fw-semibold">{{ __('State / Province') }}</label>
                                            <input type="text"
                                                   class="form-control @error('state') is-invalid @enderror"
                                                   id="state" name="state"
                                                   value="{{ old('state', $user->state ?? '') }}"
                                                   placeholder="{{ __('State or province') }}">
                                            @error('state')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="zip_code" class="form-label fw-semibold">{{ __('ZIP / Postal Code') }}</label>
                                            <input type="text"
                                                   class="form-control @error('zip_code') is-invalid @enderror"
                                                   id="zip_code" name="zip_code"
                                                   value="{{ old('zip_code', $user->zip_code ?? '') }}"
                                                   placeholder="{{ __('ZIP code') }}">
                                            @error('zip_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="country" class="form-label fw-semibold">{{ __('Country') }}</label>
                                            <input type="text"
                                                   class="form-control @error('country') is-invalid @enderror"
                                                   id="country" name="country"
                                                   value="{{ old('country', $user->country ?? '') }}"
                                                   placeholder="{{ __('Country') }}">
                                            @error('country')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label for="profile_image" class="form-label fw-semibold">{{ __('Profile Picture') }}</label>
                                            <input type="file"
                                                   class="form-control @error('profile_image') is-invalid @enderror"
                                                   id="profile_image" name="profile_image"
                                                   accept="image/*">
                                            @error('profile_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="ri-save-line me-1"></i>{{ __('Save Changes') }}
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
