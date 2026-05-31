<?php

return [
    'name' => 'PesaPal',
    'payment_description' => 'Pay securely via :name',
    'consumer_key' => 'Consumer Key',
    'consumer_key_helper' => 'Your PesaPal merchant consumer key',
    'consumer_secret' => 'Consumer Secret',
    'consumer_secret_helper' => 'Your PesaPal merchant consumer secret',
    'mode' => 'Mode',
    'sandbox' => 'Sandbox (Testing)',
    'live' => 'Live (Production)',
    'mode_helper' => 'Select sandbox for testing or live for production',
    'ipn_url' => 'IPN URL',
    'ipn_url_helper' => 'Copy this URL and configure it in your PesaPal account under IPN Settings',
    'register_account' => 'Register an account with :name',
    'after_registration' => 'After registration, you will receive your Consumer Key and Consumer Secret via email',
    'enter_keys' => 'Enter your Consumer Key and Consumer Secret in the fields above',
    'configure_ipn' => 'Configure the IPN URL in your PesaPal account settings',
    'learn_more' => 'Learn more about supported currencies',
    'missing_credentials' => 'PesaPal credentials are missing. Please configure Consumer Key and Consumer Secret.',
    'failed_to_generate_payment_url' => 'Failed to generate PesaPal payment URL. Please try again.',
    'invalid_callback' => 'Invalid payment callback. Missing required parameters.',
    'failed_to_query_status' => 'Failed to query payment status from PesaPal.',
];

