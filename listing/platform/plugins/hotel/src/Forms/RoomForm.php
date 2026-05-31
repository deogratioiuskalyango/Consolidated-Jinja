<?php

namespace Botble\Hotel\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FormAbstract;
use Botble\Hotel\Http\Requests\RoomRequest;
use Botble\Hotel\Models\Amenity;
use Botble\Hotel\Models\Room;
use Botble\Hotel\Models\RoomCategory;
use Botble\Hotel\Models\Tax;

class RoomForm extends FormAbstract
{
    public function buildForm(): void
    {
        Assets::addScripts(['input-mask', 'moment'])
            ->addScriptsDirectly([
                'vendor/core/plugins/hotel/libraries/full-calendar-6.1.8/main.min.js',
                'vendor/core/plugins/hotel/js/room-availability.js',
            ])
            ->addStylesDirectly([
                'vendor/core/plugins/hotel/css/hotel.css',
            ]);

        Assets::usingVueJS();

        $roomCategories = RoomCategory::query()->pluck('name', 'id')->all();

        $taxes = Tax::query()->pluck('title', 'id')->all();

        $amenities = Amenity::query()->select(['name', 'id'])->get();

        $selectedAmenities = [];
        if ($this->getModel()) {
            $selectedAmenities = $this->getModel()->amenities()->pluck('ht_amenities.id')->all();
        }

        $this
            ->setupModel(new Room())
            ->setValidatorClass(RoomRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label' => trans('core/base::forms.name'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('description', 'textarea', [
                'label' => trans('core/base::forms.description'),
                'attr' => [
                    'rows' => 4,
                    'placeholder' => trans('core/base::forms.description_placeholder'),
                    'data-counter' => 400,
                ],
            ])
            ->add('is_featured', 'onOff', [
                'label' => trans('core/base::forms.is_featured'),
                'default_value' => false,
            ])
            ->add('content', 'editor', [
                'label' => trans('core/base::forms.content'),
                'attr' => [
                    'rows' => 4,
                    'placeholder' => trans('core/base::forms.description_placeholder'),
                    'with-short-code' => true,
                ],
            ])
            ->add('rowOpen1', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('order', 'number', [
                'label' => trans('core/base::forms.order'),
                'attr' => [
                    'placeholder' => trans('core/base::forms.order_by_placeholder'),
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
                'default_value' => 0,
            ])
            ->add('price', 'text', [
                'label' => trans('plugins/hotel::room.form.price'),
                'required' => true,
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
                'attr' => [
                    'id' => 'price-number',
                    'placeholder' => trans('plugins/hotel::room.form.price'),
                    'class' => 'form-control input-mask-number',
                ],
            ])
            ->add('rowClose1', 'html', [
                'html' => '</div>',
            ])
            ->add('rowOpen2', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('number_of_rooms', 'text', [
                'label' => trans('plugins/hotel::room.form.number_of_rooms'),
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
                'attr' => [
                    'id' => 'number-of-rooms-number',
                    'placeholder' => trans('plugins/hotel::room.form.number_of_rooms'),
                    'class' => 'form-control input-mask-number',
                ],
                'default_value' => 1,
            ])
            ->add('number_of_beds', 'text', [
                'label' => trans('plugins/hotel::room.form.number_of_beds'),
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
                'attr' => [
                    'id' => 'number-of-beds-number',
                    'placeholder' => trans('plugins/hotel::room.form.number_of_beds'),
                    'class' => 'form-control input-mask-number',
                ],
                'default_value' => 0,
            ])
            ->add('rowClose2', 'html', [
                'html' => '</div>',
            ])
            ->add('rowOpen3', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('max_adults', 'text', [
                'label' => trans('plugins/hotel::room.form.max_adults'),
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attr' => [
                    'id' => 'max-adults-number',
                    'placeholder' => trans('plugins/hotel::room.form.max_adults'),
                    'class' => 'form-control input-mask-number',
                ],
                'default_value' => 1,
            ])
            ->add('max_children', 'text', [
                'label' => trans('plugins/hotel::room.form.max_children'),
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attr' => [
                    'id' => 'max-children-number',
                    'placeholder' => trans('plugins/hotel::room.form.max_children'),
                    'class' => 'form-control input-mask-number',
                ],
                'default_value' => 0,
            ])
            ->add('size', 'text', [
                'label' => trans('plugins/hotel::room.form.size'),
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attr' => [
                    'id' => 'size-number',
                    'placeholder' => trans('plugins/hotel::room.form.size'),
                    'class' => 'form-control input-mask-number',
                ],
                'default_value' => 0,
            ])
            ->add('rowClose3', 'html', [
                'html' => '</div>',
            ])
            ->add('images[]', 'mediaImages', [
                'label' => trans('plugins/hotel::room.images'),
                'values' => $this->getModel()->id ? $this->getModel()->images : [],
            ])
            ->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'required' => true,
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => BaseStatusEnum::labels(),
            ])
            ->add('room_category_id', 'customSelect', [
                'label' => trans('plugins/hotel::room.form.category'),
                'required' => true,
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => $roomCategories,
            ])
            ->add('tax_id', 'customSelect', [
                'label' => trans('plugins/hotel::room.form.tax'),
                'required' => true,
                'wrapper' => [
                    'class' => 'form-group col-md-4',
                ],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => $taxes,
            ])
            ->addMetaBoxes([
                'amenities' => [
                    'title' => trans('plugins/hotel::room.amenities'),
                    'content' => view(
                        'plugins/hotel::forms.amenities',
                        compact('selectedAmenities', 'amenities')
                    )->render(),
                    'priority' => 1,
                ],
            ])
            ->setBreakFieldPoint('status');

        if ($this->getModel()->id) {
            $this->addMetaBoxes([
                'room-availability' => [
                    'title' => trans('plugins/hotel::room.form.room_availability'),
                    'content' => view(
                        'plugins/hotel::forms.room-availability',
                        ['object' => $this->getModel()]
                    )->render(),
                    'priority' => 2,
                ],
            ]);
        }
    }
}
