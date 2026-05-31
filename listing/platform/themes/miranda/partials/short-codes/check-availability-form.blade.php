<section class="booking-form boxed-layout">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-11">
                <div class="booking-form-inner">
                    <form action="{{ route('public.rooms') }}">
                        <div class="row align-items-end">
                            <div class="col-lg-3 col-md-6">
                                <div class="inputs-filed mt-30">
                                    <label for="arrival-date">{{ __('Check-In') }}</label>
                                    <div class="icon"><i class="fal fa-calendar-alt"></i></div>
                                    <input
                                        id="arrival-date"
                                        name="start_date"
                                        type="text"
                                        value="{{ now()->format('d-m-Y') }}"
                                        placeholder="{{ now()->format('d-m-Y') }}"
                                    >
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="inputs-filed mt-30">
                                    <label for="departure-date">{{ __('Check-Out') }}</label>
                                    <div class="icon"><i class="fal fa-calendar-alt"></i></div>
                                    <input
                                        id="departure-date"
                                        name="end_date"
                                        type="text"
                                        value="{{ now()->addDay()->format('d-m-Y') }}"
                                        placeholder="{{ now()->addDay()->format('d-m-Y') }}"
                                    >
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="inputs-filed mt-30">
                                    <label for="adults">{{ __('Guests') }}</label>
                                    <select
                                        id="adults"
                                        name="adults"
                                    >
                                        @for ($i = 1; $i <= 10; $i++)
                                            <option
                                                value="{{ $i }}"
                                                @if ($i == 1) selected @endif
                                            >{{ $i }} {{ $i == 1 ? __('Guest') : __('Guests') }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="inputs-filed mt-30">
                                    <button type="submit">{{ __('Check Availability') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
