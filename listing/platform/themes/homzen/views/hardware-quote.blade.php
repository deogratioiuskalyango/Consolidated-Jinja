@php
    Theme::set('pageTitle', __('Request a hardware quotation'));
@endphp

<section class="tf-section platform-quote-page">
    <div class="container">
        <div class="platform-quote-layout">
            <div class="platform-quote-copy">
                <span class="platform-eyebrow">{{ __('Hardware procurement') }}</span>
                <h1>{{ __('Request quotes for construction materials') }}</h1>
                <p>{{ __('Send your material list, delivery area, and timeline. The marketplace team can route the request to verified hardware vendors and suppliers.') }}</p>

                <div class="platform-quote-steps">
                    <div>{{ __('1. Share quantities') }}</div>
                    <div>{{ __('2. Compare supplier quotes') }}</div>
                    <div>{{ __('3. Buy through the marketplace') }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('public.hardware-quote.post') }}" class="platform-quote-form">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <label>{{ __('Name') }}</label>
                        <input type="text" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label>{{ __('Email') }}</label>
                        <input type="email" name="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label>{{ __('Phone') }}</label>
                        <input type="text" name="phone" value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-6">
                        <label>{{ __('Project type') }}</label>
                        <input type="text" name="project_type" value="{{ old('project_type') }}" placeholder="{{ __('Residential build, renovation, fit-out...') }}">
                    </div>
                    <div class="col-md-6">
                        <label>{{ __('Delivery location') }}</label>
                        <input type="text" name="delivery_location" value="{{ old('delivery_location') }}">
                    </div>
                    <div class="col-md-6">
                        <label>{{ __('Needed by') }}</label>
                        <input type="date" name="needed_by" value="{{ old('needed_by') }}">
                    </div>
                    <div class="col-md-12">
                        <label>{{ __('Materials / bill of quantities') }}</label>
                        <textarea name="materials" rows="8" required placeholder="{{ __('Example: 120 bags cement, 50 iron sheets, 2 trips aggregate, plumbing fixtures...') }}">{{ old('materials') }}</textarea>
                    </div>
                    <div class="col-md-12">
                        <label>{{ __('Budget range') }}</label>
                        <input type="text" name="budget" value="{{ old('budget') }}" placeholder="{{ __('Optional') }}">
                    </div>
                </div>

                <button type="submit" class="tf-btn primary">{{ __('Submit quotation request') }}</button>
            </form>
        </div>
    </div>
</section>
