@foreach ($rooms as $room)
    <div class="form-group">
        <label class="control-label">{{ __('Icon for :room', ['room' => $room->name]) }}</label>
        {!! Form::themeIcon('icon_room_' . $room->id, Arr::get($attributes, 'icon_room_' . $room->id)) !!}
    </div>
@endforeach
