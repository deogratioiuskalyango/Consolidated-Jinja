<ol>
    <li>
        <p>
            <a
                href="https://www.pesapal.com"
                target="_blank"
            >
                {{ trans('plugins/pesapal::pesapal.register_account', ['name' => 'PesaPal']) }}
            </a>
        </p>
    </li>
    <li>
        <p>
            {{ trans('plugins/pesapal::pesapal.after_registration', ['name' => 'PesaPal']) }}
        </p>
    </li>
    <li>
        <p>
            {{ trans('plugins/pesapal::pesapal.enter_keys') }}
        </p>
    </li>
    <li>
        <p>
            {{ trans('plugins/pesapal::pesapal.configure_ipn') }}
        </p>
    </li>
</ol>

