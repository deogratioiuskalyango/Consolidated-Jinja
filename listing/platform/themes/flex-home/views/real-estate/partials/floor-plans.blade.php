<div class="row">
    <div class="col-sm-12">
        <h5 class="headifhouse">{{ __('Floor plans') }}</h5>
        @foreach ($property->formatted_floor_plans as $floorPlan)
            <p><strong>{{ $floorPlan['name'] }}</strong>: @if ($floorPlan['bedrooms']) <x-core::icon name="ti ti-bed" /> {{ $floorPlan['bedrooms'] }} - @endif @if ($floorPlan['bathrooms']) <x-core::icon name="ti ti-bath" />
                {{ $floorPlan['bathrooms'] }}@endif</p>

            @if ($floorPlan['description'])
                <div class="box-description">
                    {!! BaseHelper::clean($floorPlan['description']) !!}
                </div>
            @endif

            @if ($floorPlan['image'])
                <div class="box-img">
                    {{ RvMedia::image($floorPlan['image'], $floorPlan['name']) }}
                </div>
            @endif
        @endforeach
    </div>
</div>

<br>
