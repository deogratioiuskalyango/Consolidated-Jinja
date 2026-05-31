@php
    use Botble\Hotel\Models\Room;
    use Botble\Slug\Facades\SlugHelper;

    $slug = DB::table('slugs')
        ->where('reference_type', Room::class)
        ->where('reference_id', $room->id)
        ->value('key');

    $roomUrl = $slug ? url((SlugHelper::getPrefix(Room::class, 'rooms') ?: 'rooms') . '/' . $slug) : route('public.rooms');
@endphp

<div class="homeya-box">
    <div class="archive-top">
        <a href="{{ $roomUrl }}" class="images-group">
            {{ RvMedia::image($room->image, $room->name, 'medium-rectangle') }}
        </a>
    </div>
    <div class="content">
        <div class="text-subtitle text-primary">{{ $room->category->name ?: __('Stay') }}</div>
        <h6 class="title">
            <a href="{{ $roomUrl }}">{{ $room->name }}</a>
        </h6>
        <p class="mt-8">{{ Str::limit(strip_tags($room->description), 110) }}</p>
        <ul class="meta-list mt-12">
            <li>{{ __(':count beds', ['count' => $room->number_of_beds ?: 1]) }}</li>
            <li>{{ __(':count adults', ['count' => $room->max_adults ?: 1]) }}</li>
            @if ($room->size)
                <li>{{ $room->size }}</li>
            @endif
        </ul>
    </div>
    <div class="archive-bottom d-flex justify-content-between align-items-center">
        <span class="text-primary fw-7">{{ format_price($room->price) }}</span>
        <a href="{{ $roomUrl }}" class="tf-btn">{{ __('View stay') }}</a>
    </div>
</div>
