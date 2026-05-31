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
    <label class="form-label">{{ __('Limit') }}</label>
    <input
        class="form-control"
        name="limit"
        value="{{ Arr::get($attributes, 'limit', 4) }}"
    />
</div>
