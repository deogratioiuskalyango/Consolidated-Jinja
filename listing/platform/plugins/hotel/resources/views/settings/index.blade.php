@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    {!! Form::open(['url' => route('hotel.settings'), 'class' => 'main-setting-form']) !!}
    <div class='max-width-1200'>
        <x-core-setting::section
            :title="trans('plugins/hotel::currency.currencies')"
            :description="trans('plugins/hotel::currency.setting_description')"
        >
            <x-core-setting::on-off
                name="hotel_enable_auto_detect_visitor_currency"
                :label="trans('plugins/hotel::currency.enable_auto_detect_visitor_currency')"
                :value="setting('hotel_enable_auto_detect_visitor_currency', false)"
            />
            <x-core-setting::on-off
                name="hotel_add_space_between_price_and_currency"
                :label="trans('plugins/hotel::currency.add_space_between_price_and_currency')"
                :value="setting('hotel_add_space_between_price_and_currency', false)"
            />

            <x-core-setting::select
                name="hotel_thousands_separator"
                :label="trans('plugins/hotel::currency.thousands_separator')"
                :options="[
                    ',' => trans('plugins/hotel::currency.separator_comma'),
                    '.' => trans('plugins/hotel::currency.separator_period'),
                    'space' => trans('plugins/hotel::currency.separator_space'),
                ]"
                :value="setting('hotel_thousands_separator', ',')"
            />

            <textarea
                class="hidden"
                id="currencies"
                name="currencies"
            >{!! json_encode($currencies) !!}</textarea>
            <textarea
                class="hidden"
                id="deleted_currencies"
                name="deleted_currencies"
            ></textarea>
            <div class="swatches-container">
                <div class="header clearfix">
                    <div class="swatch-item">
                        {{ trans('plugins/hotel::currency.name') }}
                    </div>
                    <div class="swatch-item">
                        {{ trans('plugins/hotel::currency.symbol') }}
                    </div>
                    <div class="swatch-item swatch-decimals">
                        {{ trans('plugins/hotel::currency.number_of_decimals') }}
                    </div>
                    <div class="swatch-item swatch-exchange-rate">
                        {{ trans('plugins/hotel::currency.exchange_rate') }}
                    </div>
                    <div class="swatch-item swatch-is-prefix-symbol">
                        {{ trans('plugins/hotel::currency.is_prefix_symbol') }}
                    </div>
                    <div class="swatch-is-default">
                        {{ trans('plugins/hotel::currency.is_default') }}
                    </div>
                    <div class="remove-item">{{ trans('plugins/hotel::currency.remove') }}</div>
                </div>
                <ul class="swatches-list">

                </ul>
                <div class="clearfix"></div>
                {!! Form::helper(trans('plugins/hotel::currency.instruction')) !!}
                <a
                    class="js-add-new-attribute"
                    href="#"
                >
                    {{ trans('plugins/hotel::currency.new_currency') }}
                </a>
            </div>
        </x-core-setting::section>

        <x-core-setting::section
            :title="trans('plugins/hotel::hotel.review_settings')"
            :description="trans('plugins/hotel::hotel.review_settings_description')"
        >
            <x-core-setting::form-group>
                <x-core-setting::on-off
                    name="hotel_enable_review_room"
                    :label="trans('plugins/hotel::hotel.enable_review_room')"
                    :value="setting('hotel_enable_review_room', true)"
                />
            </x-core-setting::form-group>

            <x-core-setting::form-group>
                <x-core-setting::text-input
                    name="hotel_reviews_per_page"
                    :label="trans('plugins/hotel::hotel.reviews_per_page')"
                    :value="setting('hotel_reviews_per_page', 10)"
                />
            </x-core-setting::form-group>
        </x-core-setting::section>

        <div
            class="flexbox-annotated-section"
            style="border: none"
        >
            <div class="flexbox-annotated-section-annotation">
                &nbsp;
            </div>
            <div class="flexbox-annotated-section-content">
                <button
                    class="btn btn-info"
                    type="submit"
                >{{ trans('plugins/hotel::currency.save_settings') }}</button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@push('footer')
    <script id="currency_template" type="text/x-custom-template">
        <li data-id="__id__" class="clearfix">
            <div class="swatch-item" data-type="title">
                <input type="text" class="form-control" value="__title__">
            </div>
            <div class="swatch-item" data-type="symbol">
                <input type="text" class="form-control" value="__symbol__">
            </div>
            <div class="swatch-item swatch-decimals" data-type="decimals">
                <input type="number" class="form-control" value="__decimals__">
            </div>
            <div class="swatch-item swatch-exchange-rate" data-type="exchange_rate">
                <input type="number" class="form-control" value="__exchangeRate__" step="0.00000001">
            </div>
            <div class="swatch-item swatch-is-prefix-symbol" data-type="is_prefix_symbol">
                <div class="ui-select-wrapper">
                    <select class="ui-select">
                        <option value="1" __isPrefixSymbolChecked__>{{ trans('plugins/hotel::currency.before_number') }}</option>
                        <option value="0" __notIsPrefixSymbolChecked__>{{ trans('plugins/hotel::currency.after_number') }}</option>
                    </select>
                    <svg class="svg-next-icon svg-next-icon-size-16">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                    </svg>
                </div>
            </div>
            <div class="swatch-is-default" data-type="is_default">
                <input type="radio" name="currencies_is_default" value="__position__" __isDefaultChecked__>
            </div>
            <div class="remove-item"><a href="#" class="font-red"><i class="fa fa-trash"></i></a></div>
        </li>
    </script>
@endpush
