@php
    use Botble\Hotel\Models\Room;

    Theme::set('pageTitle', __('Rooms and stays'));

    $rooms = Room::query()
        ->with(['category', 'currency'])
        ->where('status', 'published')
        ->orderByDesc('is_featured')
        ->orderBy('order')
        ->paginate(12);
@endphp

<section class="tf-section">
    <div class="container">
        <div class="box-title-listing">
            <div class="box-left">
                <div class="text-subtitle text-primary">{{ __('Rooms and stays') }}</div>
                <h1 class="page-title">{{ __('Book rooms, suites, and serviced stays') }}</h1>
            </div>
            <div class="box-filter-tab">
                <a href="{{ route('public.hardware-quote') }}" class="tf-btn">{{ __('Request hardware quote') }}</a>
            </div>
        </div>

        <div class="grid-layout-3">
            @forelse ($rooms as $room)
                @include(Theme::getThemeNamespace('views.hotel.includes.room-item'), compact('room'))
            @empty
                <div class="alert alert-info">{{ __('No rooms are published yet. Add rooms from Admin -> Hotel -> Rooms.') }}</div>
            @endforelse
        </div>

        @if ($rooms->hasPages())
            <div class="mt-5">
                {{ $rooms->links(Theme::getThemeNamespace('partials.pagination')) }}
            </div>
        @endif
    </div>
</section>
