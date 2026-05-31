<section>
    <div class="form-group">
        <label class="control-label">{{ __('Name') }}</label>
        <input
            class="form-control"
            name="name"
            type="text"
            value="{{ Arr::get($attributes, 'name') }}"
            placeholder="{{ __('Name') }}"
        >
    </div>

    <div class="form-group">
        <label class="control-label">{{ __('Title') }}</label>
        <input
            class="form-control"
            name="title"
            type="text"
            value="{{ Arr::get($attributes, 'title') }}"
            placeholder="{{ __('Title') }}"
        >
    </div>

    <div class="form-group">
        <label class="control-label">{{ __('Subtitle') }}</label>
        <input
            class="form-control"
            name="description"
            type="text"
            value="{{ Arr::get($attributes, 'description') }}"
            placeholder="{{ __('Description') }}"
        >
    </div>

    <div class="form-group">
        <label class="control-label">{{ __('Image') }}</label>
        {!! Form::mediaImage('image', Arr::get($attributes, 'image')) !!}
    </div>

</section>
