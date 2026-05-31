{!! Form::hidden('gallery', $value ? json_encode($value) : null, [
    'id' => 'gallery-data',
    'class' => 'form-control',
]) !!}
<div>
    <div class="list-photos-gallery">
        <div
            class="row"
            id="list-photos-items"
        >
            @if (!empty($value))
                @foreach ($value as $key => $item)
                    <div
                        class="col-md-2 col-sm-3 col-4 photo-gallery-item"
                        data-id="{{ $key }}"
                        data-img="{{ $imageUrl = Arr::get($item, 'img') }}"
                        data-description="{{ $description = Arr::get($item, 'description') }}"
                    >
                        <div class="gallery_image_wrapper">
                            {{ RvMedia::image($imageUrl, $description, 'thumb') }}
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="form-group mb-3">
        <a
            class="btn_select_gallery"
            href="#"
        >{{ trans('plugins/gallery::gallery.select_image') }}</a>&nbsp;
        <a
            class="text-danger reset-gallery @if (empty($value)) hidden @endif"
            href="#"
        >{{ trans('plugins/gallery::gallery.reset') }}</a>
    </div>
</div>

<x-core::modal
    id="edit-gallery-item"
    type="danger"
    :title="trans('plugins/gallery::gallery.update_photo_description')"
    button-id="confirm-remove-plugin-button"
    :button-label="trans('packages/plugin-management::plugin.remove_plugin_confirm_yes')"
>
    <p><input
            class="form-control"
            id="gallery-item-description"
            type="text"
            placeholder="{{ trans('plugins/gallery::gallery.update_photo_description_placeholder') }}"
        ></p>
    <x-slot name="footer">
        <button
            class="float-start btn btn-danger"
            id="delete-gallery-item"
            type="button"
        >{{ trans('plugins/gallery::gallery.delete_photo') }}</button>
        <button
            class="float-end btn btn-secondary"
            data-bs-dismiss="modal"
            type="button"
        >{{ trans('core/base::forms.cancel') }}</button>
        <button
            class="float-end btn btn-primary"
            id="update-gallery-item"
            type="button"
        >{{ trans('core/base::forms.update') }}</button>
    </x-slot>
</x-core::modal>
