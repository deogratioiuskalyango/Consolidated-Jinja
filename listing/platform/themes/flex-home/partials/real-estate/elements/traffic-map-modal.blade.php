<div id="zxcvbnm">
    <h5 class="headifhouse">{{ __('Location') }}</h5>
    @if (!empty($location)) <p class="d-print-none">{{ $location }}</p> @endif

    @if (theme_option('real_estate_show_map_on_single_detail_page', 'yes') === 'yes')
        <div class="traffic-map-container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div id="trafficMap" class="w-100 h-100 d-print-none"></div>
                </div>
            </div>
        </div>
    @endif
</div>
