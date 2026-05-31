<div class="mb-3">
    <label class="form-label">{{ __('Title') }}</label>
    <input
        class="form-control"
        name="title"
        value="{{ Arr::get($attributes, 'title') }}"
    >
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Subtitle') }}</label>
    <textarea
        class="form-control"
        name="subtitle"
        rows="3"
    >{{ Arr::get($attributes, 'subtitle') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">{{ __('City') }}</label>
    <input
        class="form-control list-tagify"
        name="city"
        data-list="{{ json_encode($cities) }}"
        value="{{ Arr::get($attributes, 'city') }}"
        placeholder="{{ __('Select city from the list') }}"
    >
</div>

<div class="mb-3">
    <label class="form-label">{{ __('State') }}</label>
    <input
        class="form-control list-tagify"
        name="state"
        data-list="{{ json_encode($states) }}"
        value="{{ Arr::get($attributes, 'state') }}"
        placeholder="{{ __('Select state from the list') }}"
    >
</div>
