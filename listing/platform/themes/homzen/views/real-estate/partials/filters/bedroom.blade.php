@if (theme_option('real_estate_enable_filter_by_bedroom', 'yes') == 'yes')
    @php
        $rawBedroom = request()->input('bedroom', '');
        $selectedBedrooms = array_filter(is_array($rawBedroom) ? $rawBedroom : explode(',', (string) $rawBedroom));
    @endphp
    <div @class(['box-select form-search-bedroom', $class ?? null])>
        <label class="title-select text-variant-1">{{ __('Bedrooms') }}</label>
        <div class="multi-select-dropdown" data-all-label="{{ __('All') }}">
            <input type="hidden" name="bedroom" value="{{ implode(',', $selectedBedrooms) }}" />
            <div class="multi-select-trigger">
                <span class="multi-select-label">{{ $selectedBedrooms ? implode(', ', $selectedBedrooms) : __('All') }}</span>
                <span class="multi-select-arrow"></span>
            </div>
            <div class="multi-select-options">
                @foreach(range(1, 5) as $i)
                    <label class="multi-select-option">
                        <input type="checkbox" value="{{ $i }}" class="tf-checkbox style-1" @checked(in_array($i, $selectedBedrooms)) />
                        <span>
                            @if($i < 5)
                                {{ $i === 1 ? __('1 Bedroom') : __(':number Bedrooms', ['number' => $i]) }}
                            @else
                                {{ __(':number+ Bedrooms', ['number' => $i]) }}
                            @endif
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
@endif
