<?php

namespace Botble\Hotel\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FormAbstract;
use Botble\Hotel\Http\Requests\CustomerCreateRequest;
use Botble\Hotel\Models\Customer;

class CustomerForm extends FormAbstract
{
    public function buildForm(): void
    {
        $this
            ->setupModel(new Customer())
            ->setValidatorClass(CustomerCreateRequest::class)
            ->withCustomFields()
            ->add('first_name', 'text', [
                'label' => trans('plugins/hotel::customer.form.first_name'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('plugins/hotel::customer.form.first_name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('last_name', 'text', [
                'label' => trans('plugins/hotel::customer.form.last_name'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('plugins/hotel::customer.form.last_name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('email', 'text', [
                'label' => trans('plugins/hotel::customer.form.email'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('plugins/hotel::customer.form.email_placeholder'),
                    'data-counter' => 60,
                ],
            ])
            ->add('is_change_password', 'checkbox', [
                'label' => trans('plugins/hotel::customer.change_password'),
                'value' => 1,
            ])
            ->add('password', 'password', [
                'label' => trans('plugins/hotel::customer.password'),
                'required' => true,
                'attr' => [
                    'data-counter' => 60,
                ],
                'wrapper' => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ($this->getModel(
                    )->id ? ' hidden' : null),
                ],
            ])
            ->add('password_confirmation', 'password', [
                'label' => trans('plugins/hotel::customer.password_confirmation'),
                'required' => true,
                'attr' => [
                    'data-counter' => 60,
                ],
                'wrapper' => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ($this->getModel(
                    )->id ? ' hidden' : null),
                ],
            ])
            ->add('phone', 'text', [
                'label' => trans('plugins/hotel::customer.form.phone'),
                'attr' => [
                    'placeholder' => trans('plugins/hotel::customer.form.phone'),
                    'data-counter' => 20,
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
            ->add('avatar', 'mediaImage')
            ->setBreakFieldPoint('status');
    }
}
