{!! apply_filters('before_consult_form', null, $data) !!}

<div class="head">{{ __('Contact') }}</div>

{!!
    \Botble\RealEstate\Forms\Fronts\ConsultForm::create()
        ->setFormOption('class', 'generic-form')
        ->setFormInputWrapperClass('form-group')
        ->modify('submit', 'submit', ['attr' => ['class' => 'btn btn-lg btn-orange btn-block']])
        ->add('type', 'hidden', ['attr' => ['value' => $type]])
        ->add('data_id', 'hidden', ['attr' => ['value' => $data->getKey()]])
        ->addBefore('content', 'data_name', 'text', ['label' => false, 'attr' => ['value' => $data->name, 'disabled' => true]])
        ->renderForm()
!!}

{!! apply_filters('after_consult_form', null, $data) !!}
