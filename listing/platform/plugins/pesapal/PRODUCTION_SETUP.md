# PesaPal Production Setup Guide

This guide will help you configure PesaPal for your live production server.

## Step 1: Register Live Account

1. Visit [https://www.pesapal.com](https://www.pesapal.com)
2. Click "Sign Up" or "Register"
3. Complete the merchant registration process
4. Verify your account (follow PesaPal's verification process)

## Step 2: Get Your Credentials

1. Log in to your PesaPal merchant dashboard
2. Navigate to "API Settings" or "Integration"
3. Copy your:
   - **Consumer Key** (also called Merchant Key)
   - **Consumer Secret** (also called Merchant Secret)

**Important:** Keep these credentials secure and never share them publicly.

## Step 3: Configure Plugin in Admin Panel

1. Log in to your Botble CMS admin panel
2. Navigate to: **Settings** > **Payment Methods**
3. Find **PesaPal** in the list
4. Click to expand and configure:
   - **Name**: PesaPal (or your preferred display name)
   - **Description**: Payment via PesaPal
   - **Consumer Key**: Paste your live Consumer Key
   - **Consumer Secret**: Paste your live Consumer Secret
   - **Mode**: Select **"Live (Production)"**
   - **IPN URL**: Copy the URL shown (you'll need this for Step 4)
   - **Status**: Enable (turn on)
   - **Available Countries**: Select countries where PesaPal is available
5. Click **Save**

## Step 4: Configure IPN in PesaPal Dashboard

1. Log in to your PesaPal merchant dashboard
2. Go to **Settings** > **IPN Settings** (or **Integration** > **IPN**)
3. Enter the IPN URL from Step 3
   - Format: `https://yourdomain.com/pesapal/payment/ipn`
   - Replace `yourdomain.com` with your actual domain
4. Save the IPN URL

**Note:** The IPN URL must be:
- Accessible via HTTPS
- Publicly accessible (not behind firewall)
- Able to receive POST requests

## Step 5: Test Your Integration

### Test Transaction

1. Create a test order on your website
2. Select PesaPal as payment method
3. Complete the payment on PesaPal
4. Verify you're redirected back correctly
5. Check that payment status is updated in admin panel

### Verify IPN

1. Make a test payment
2. Check your server logs for IPN requests
3. Verify payment status updates automatically
4. Check PesaPal dashboard for transaction status

## Step 6: Security Checklist

- [ ] Using HTTPS for your website
- [ ] Consumer Secret is stored securely (not in code)
- [ ] IPN URL is HTTPS
- [ ] Server firewall allows incoming POST requests to IPN endpoint
- [ ] Regular monitoring of payment transactions
- [ ] SSL certificate is valid and not expired

## Step 7: Monitor Transactions

1. Regularly check PesaPal merchant dashboard for transactions
2. Monitor payment logs in Botble admin: **Payments** > **Transactions**
3. Set up email notifications for failed payments
4. Review IPN logs if payments aren't updating

## Common Issues

### Issue: "Invalid Consumer Key"
**Solution:** 
- Verify you're using Live credentials (not Sandbox)
- Check for extra spaces when copying/pasting
- Ensure Mode is set to "Live (Production)"

### Issue: IPN Not Receiving Updates
**Solution:**
- Verify IPN URL is correctly configured in PesaPal
- Check server can receive POST requests
- Review server error logs
- Test IPN URL accessibility: `curl -X POST https://yourdomain.com/pesapal/payment/ipn`

### Issue: Payment Status Not Updating
**Solution:**
- Verify IPN is working (check logs)
- Manually query payment status if needed
- Contact PesaPal support if IPN continues to fail

## Support Contacts

- **PesaPal Support**: [https://www.pesapal.com/support](https://www.pesapal.com/support)
- **PesaPal API Docs**: [https://developer.pesapal.com](https://developer.pesapal.com)
- **PesaPal Status Page**: Check for service status

## Important Notes

1. **Never use Sandbox credentials in Production mode**
2. **Always test in Sandbox first before going live**
3. **Keep your Consumer Secret secure** - treat it like a password
4. **Monitor your first few live transactions closely**
5. **Have a backup payment method** in case of issues

## Environment Variables (Optional)

You can also set credentials via environment variables:

```env
PESAPAL_CONSUMER_KEY=your_live_consumer_key
PESAPAL_CONSUMER_SECRET=your_live_consumer_secret
PESAPAL_MODE=live
```

Then update the plugin to read from these variables if preferred.

## Going Live Checklist

- [ ] Live PesaPal account registered and verified
- [ ] Live Consumer Key and Secret obtained
- [ ] Plugin configured with Live credentials
- [ ] Mode set to "Live (Production)"
- [ ] IPN URL configured in PesaPal dashboard
- [ ] HTTPS enabled on website
- [ ] Test transaction completed successfully
- [ ] IPN verified working
- [ ] Payment status updates confirmed
- [ ] Monitoring and logging set up

---

**Ready to go live?** Follow all steps above and test thoroughly before processing real customer payments.

