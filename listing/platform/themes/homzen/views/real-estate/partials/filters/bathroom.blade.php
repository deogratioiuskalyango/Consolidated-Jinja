@if (theme_option('real_estate_enable_filter_by_bathroom', 'yes') == 'yes')
    @php
        $rawBathroom = request()->input('bathroom', '');
        $selectedBathrooms = array_filter(is_array($rawBathroom) ? $rawBathroom : explode(',', (string) $rawBathroom));
    @endphp
    <div @class(['box-select form-search-bathroom', $class ?? null])>
        <label class="title-select text-variant-1">{{ __('Bathrooms') }}</label>
        <div class="multi-select-dropdown" data-all-label="{{ __('All') }}">
            <input type="hidden" name="bathroom" value="{{ implode(',', $selectedBathrooms) }}" />
            <div class="multi-select-trigger">
                <span class="multi-select-label">{{ $selectedBathrooms ? implode(', ', $selectedBathrooms) : __('All') }}</span>
                <span class="multi-select-arrow"></span>
            </div>
            <div class="multi-select-options">
                @foreach(range(1, 5) as $i)
                    <label class="multi-select-option">
                        <input type="checkbox" value="{{ $i }}" class="tf-checkbox style-1" @checked(in_array($i, $selectedBathrooms)) />
                        <span>
                            @if($i < 5)
                                {{ $i === 1 ? __('1 Bathroom') : __(':number Bathrooms', ['number' => $i]) }}
                            @else
                                {{ __(':number+ Bathrooms', ['number' => $i]) }}
                            @endif
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
@endif
