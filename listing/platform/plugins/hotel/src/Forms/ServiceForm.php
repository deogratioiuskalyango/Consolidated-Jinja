<?php

namespace Botble\Hotel\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FormAbstract;
use Botble\Hotel\Enums\ServicePriceTypeEnum;
use Botble\Hotel\Http\Requests\ServiceRequest;
use Botble\Hotel\Models\Service;

class ServiceForm extends FormAbstract
{
    public function buildForm(): void
    {
        $this
            ->setupModel(new Service())
            ->setValidatorClass(ServiceRequest::class)
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
            ->add('content', 'editor', [
                'label' => trans('core/base::forms.content'),
                'attr' => [
                    'rows' => 4,
                    'placeholder' => trans('core/base::forms.description_placeholder'),
                    'with-short-code' => true,
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
            ->add('price', 'text', [
                'label' => trans('plugins/hotel::service.form.price'),
                'required' => true,
                'attr' => [
                    'id' => 'price-number',
                    'placeholder' => trans('plugins/hotel::service.form.price'),
                    'class' => 'form-control input-mask-number',
                ],
            ])
            ->add('price_type', 'customSelect', [
                'label' => trans('plugins/hotel::service.form.price_type'),
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => ServicePriceTypeEnum::labels(),
            ])
            ->add('image', 'mediaImage')
            ->setBreakFieldPoint('status');
    }
}
