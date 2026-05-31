<div id="contact">
    <div class="row">
        <div class="col-md-6">
            <div class="wrapper">
                <h2 class="h2">{{ __('Contact information') }}</h2>
                <div class="contact-main">

                    @if ($companyName = theme_option('company_name'))
                            <div
                                class="contact-name"
                                style="text-transform: uppercase"
                            >{{ $companyName }}</div>
                    @endif

                    @if($address = theme_option('address'))
                        <div class="contact-address">{{ __('Address') }}: {{ $address }}</div>
                    @endif

                    @if ($hotline = theme_option('hotline'))
                        <div class="contact-phone">{{ __('Hotline') }}: <a href="tel:{{ $hotline }}">{{ $hotline }}</a> </div>
                    @endif

                    @if($email = theme_option('email'))
                        <div class="contact-email">{{ __('Email') }}: <a href="mailto:{{ $email }}">{{ $email }}</a></div>
                    @endif

                    @if ($aboutUs = theme_option('about-us'))
                        <div class="mt-20">{!! BaseHelper::clean($aboutUs) !!}</div>
                    @endif

                </div>
            </div>
        </div>
        <div class="col-md-6">
                <div class="wrapper">
                    <h2 class="h2">{{ __('HOW WE CAN HELP YOU?') }}</h2>
                    {!!
                        \Botble\Contact\Forms\Fronts\ContactForm::create()
                            ->setFormOption('class', 'contact-form')
                            ->setFormInputClass('form-control')
                            ->setFormInputWrapperClass('form-group')
                            ->setFormLabelClass('sr-only')
                            ->modify('name', 'text', ['attr' => ['placeholder' => __('Name')]])
                            ->modify('email', 'email', ['attr' => ['placeholder' => __('Email')]])
                            ->modify('phone', 'text', ['attr' => ['placeholder' => __('Phone')]])
                            ->modify('address', 'text', ['attr' => ['placeholder' => __('Address')]])
                            ->modify(
                                'open_phone_wrapper_column_wrapper',
                                'html',
                                ['html' => sprintf('<div class="contact-column-%s col-md-%s contact-field-%s">', 12, 12, 'phone')],
                                true
                            )
                            ->modify(
                                'open_address_wrapper_column_wrapper',
                                'html',
                                ['html' => sprintf('<div class="contact-column-%s col-md-%s contact-field-%s">', 12, 12, 'address')],
                                true
                            )
                            ->modify('subject', 'text', ['attr' => ['placeholder' => __('Subject')]])
                            ->modify('content', 'textarea', ['attr' => ['placeholder' => __('Message')]])
                            ->modify(
                                'submit',
                                'submit',
                                Botble\Base\Forms\FieldOptions\ButtonFieldOption::make()
                                    ->cssClass('btn-special')
                                    ->label(__('Send message'))
                                    ->wrapperAttributes(['class' => 'form-actions mt-20'])
                                    ->toArray(),
                                true
                            )
                            ->addAsteriskToMandatoryFields()
                            ->renderForm()
                    !!}
                </div>
        </div>
    </div>
</div>
