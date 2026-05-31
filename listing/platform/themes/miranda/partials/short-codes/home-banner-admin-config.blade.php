<section>

    <div class="form-group">
        <label
            class="control-label">{{ __('Use theme option sliders or use shortcode slider option in below?') }}</label>
        <select
            class="select-full"
            name="option"
            required
        >
            <option
                value="theme"
                @selected(Arr::get($attributes, 'option') == 'theme')
            >{{ __('Theme option sliders') }}</option>
            <option
                value="code"
                @selected(Arr::get($attributes, 'option') == 'code')
            >{{ __('Shortcode slider option in below') }}</option>
        </select>
    </div>

    @php
        $fields = [
            'title' => [
                'title' => __('Title'),
            ],
            'image' => [
                'type' => 'image',
                'title' => __('Image'),
            ],
            'description' => [
                'title' => __('Description'),
            ],
            'primary_button_text' => [
                'title' => __('Slider action text'),
                'placeholder' => __('Slider action text'),
            ],
            'primary_slider_button_url' => [
                'title' => __('Slider learn more URL'),
                'placeholder' => __('Learn more link'),
            ],
            'secondary_button_text' => [
                'title' => __('Slider secondary button text'),
                'placeholder' => __('Slider secondary button text'),
            ],
            'secondary_slider_button_url' => [
                'title' => __('Slider secondary-button URL'),
                'placeholder' => __('Learn more link'),
            ],
        ];
    @endphp

    {!! Theme::partial('short-codes.partials.tabs', compact('fields', 'attributes')) !!}

</section>
