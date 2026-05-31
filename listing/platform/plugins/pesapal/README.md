# PesaPal Payment Gateway Plugin

This plugin integrates PesaPal payment gateway into your Botble CMS e-commerce system.

## Features

- Secure payment processing via PesaPal
- Support for multiple currencies (KES, UGX, TZS, USD)
- Sandbox and Live mode support
- IPN (Instant Payment Notification) support
- Automatic payment status updates

## Installation

1. The plugin is already installed in `platform/plugins/pesapal`
2. Activate the plugin from the admin panel: Settings > Plugins
3. Configure your PesaPal credentials

## Configuration

### For Sandbox/Testing:

1. Register a test account at [https://cybqa.pesapal.com](https://cybqa.pesapal.com)
2. Get your Consumer Key and Consumer Secret from the merchant dashboard
3. In admin panel, go to Settings > Payment Methods
4. Find PesaPal and configure:
   - Consumer Key: Your test consumer key
   - Consumer Secret: Your test consumer secret
   - Mode: Select "Sandbox (Testing)"
   - IPN URL: Copy the provided URL

### For Production/Live:

1. Register a live account at [https://www.pesapal.com](https://www.pesapal.com)
2. Get your Consumer Key and Consumer Secret from the merchant dashboard
3. In admin panel, go to Settings > Payment Methods
4. Find PesaPal and configure:
   - Consumer Key: Your live consumer key
   - Consumer Secret: Your live consumer secret
   - Mode: Select "Live (Production)"
   - IPN URL: Copy the provided URL

### IPN Configuration

1. Log in to your PesaPal merchant account
2. Go to IPN Settings
3. Enter the IPN URL provided in the plugin settings
4. Save the settings

The IPN URL format is: `https://yourdomain.com/pesapal/payment/ipn`

## Usage

1. Customers can select PesaPal as a payment method during checkout
2. They will be redirected to PesaPal's payment page
3. After payment, they will be redirected back to your site
4. Payment status is automatically updated via IPN

## Supported Currencies

- KES (Kenyan Shilling)
- UGX (Ugandan Shilling)
- TZS (Tanzanian Shilling)
- USD (US Dollar)

## Troubleshooting

### Payment URL not generating
- Check that Consumer Key and Consumer Secret are correctly entered
- Verify the mode (Sandbox/Live) matches your credentials
- Check server logs for detailed error messages

### IPN not working
- Verify the IPN URL is correctly configured in PesaPal account
- Ensure your server can receive POST requests
- Check that the route is accessible (not blocked by firewall)

### Payment status not updating
- Verify IPN is configured correctly
- Check that the IPN URL is accessible from PesaPal servers
- Review server logs for IPN requests

## Support

For PesaPal API documentation, visit: [https://developer.pesapal.com](https://developer.pesapal.com)

