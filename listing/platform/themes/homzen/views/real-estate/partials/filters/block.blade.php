@if (theme_option('real_estate_enable_filter_by_block', 'yes') == 'yes')
    @php
        $rawBlocks = request()->input('blocks', '');
        $selectedBlocks = array_filter(is_array($rawBlocks) ? $rawBlocks : explode(',', (string) $rawBlocks));
    @endphp
    <div @class(['box-select form-search-block', $class ?? null])>
        <label class="title-select fw-5">{{ __('Blocks') }}</label>
        <div class="multi-select-dropdown" data-all-label="{{ __('All') }}">
            <input type="hidden" name="blocks" value="{{ implode(',', $selectedBlocks) }}" />
            <div class="multi-select-trigger">
                <span class="multi-select-label">{{ $selectedBlocks ? implode(', ', $selectedBlocks) : __('All') }}</span>
                <span class="multi-select-arrow"></span>
            </div>
            <div class="multi-select-options">
                @foreach(range(1, 5) as $i)
                    <label class="multi-select-option">
                        <input type="checkbox" value="{{ $i }}" class="tf-checkbox style-1" @checked(in_array($i, $selectedBlocks)) />
                        <span>
                            @if($i < 5)
                                {{ $i === 1 ? __('1 Block') : __(':number Blocks', ['number' => $i]) }}
                            @else
                                {{ __(':number+ Blocks', ['number' => $i]) }}
                            @endif
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
@endif
