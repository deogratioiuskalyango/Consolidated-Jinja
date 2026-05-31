<?php

namespace Botble\Hotel\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FormAbstract;
use Botble\Hotel\Http\Requests\FoodRequest;
use Botble\Hotel\Models\Food;
use Botble\Hotel\Models\FoodType;

class FoodForm extends FormAbstract
{
    public function buildForm(): void
    {
        Assets::addScripts(['input-mask'])
            ->addStylesDirectly('vendor/core/plugins/hotel/css/hotel.css');

        $foodTypes = FoodType::query()->pluck('name', 'id')->all();

        $this
            ->setupModel(new Food())
            ->setValidatorClass(FoodRequest::class)
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
            ->add('price', 'text', [
                'label' => trans('plugins/hotel::food.form.price'),
                'required' => true,
                'attr' => [
                    'id' => 'price-number',
                    'placeholder' => trans('plugins/hotel::food.form.price'),
                    'class' => 'form-control input-mask-number',
                ],
            ])
            ->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'required' => true,
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => BaseStatusEnum::labels(),
            ])
            ->add('food_type_id', 'customSelect', [
                'label' => trans('plugins/hotel::food.form.food_type'),
                'required' => true,
                'choices' => $foodTypes,
            ])
            ->add('image', 'mediaImage')
            ->setBreakFieldPoint('status');
    }
}
