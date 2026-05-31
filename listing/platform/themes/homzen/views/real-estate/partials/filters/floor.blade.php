@if (theme_option('real_estate_enable_filter_by_floor', 'yes') == 'yes')
    @php
        $rawFloor = request()->input('floor', '');
        $selectedFloors = array_filter(is_array($rawFloor) ? $rawFloor : explode(',', (string) $rawFloor));
    @endphp
    <div @class(['box-select form-search-floor', $class ?? null])>
        <label class="title-select fw-5">{{ __('Floors') }}</label>
        <div class="multi-select-dropdown" data-all-label="{{ __('All') }}">
            <input type="hidden" name="floor" value="{{ implode(',', $selectedFloors) }}" />
            <div class="multi-select-trigger">
                <span class="multi-select-label">{{ $selectedFloors ? implode(', ', $selectedFloors) : __('All') }}</span>
                <span class="multi-select-arrow"></span>
            </div>
            <div class="multi-select-options">
                @foreach(range(1, 5) as $i)
                    <label class="multi-select-option">
                        <input type="checkbox" value="{{ $i }}" class="tf-checkbox style-1" @checked(in_array($i, $selectedFloors)) />
                        <span>
                            @if($i < 5)
                                {{ $i === 1 ? __('1 Floor') : __(':number Floors', ['number' => $i]) }}
                            @else
                                {{ __(':number+ Floors', ['number' => $i]) }}
                            @endif
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
@endif
