<div class="form-group">
    <label class="control-label">{{ __('About us') }}</label>
    <input
        class="form-control"
        name="about_us"
        type="text"
        value="{{ Arr::get($attributes, 'about_us') }}"
        placeholder="{{ __('About us') }}"
    >
</div>

<div class="form-group">
    <label class="control-label">{{ __('Hotline') }}</label>
    <input
        class="form-control"
        name="hotline"
        type="text"
        value="{{ Arr::get($attributes, 'hotline') }}"
        placeholder="{{ __('Hotline') }}"
    >
</div>

<div class="form-group">
    <label class="control-label">{{ __('Address') }}</label>
    <input
        class="form-control"
        name="address"
        type="text"
        value="{{ Arr::get($attributes, 'address') }}"
        placeholder="{{ __('Address') }}"
    >
</div>

<div class="form-group">
    <label class="control-label">{{ __('Email') }}</label>
    <input
        class="form-control"
        name="email"
        type="text"
        value="{{ Arr::get($attributes, 'email') }}"
        placeholder="{{ __('Email') }}"
    >
</div>
