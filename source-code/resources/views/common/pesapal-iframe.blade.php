<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ getOption('app_name') }} — Pesapal Payment</title>
    <link href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        body { background: #f5f6fa; display: flex; flex-direction: column; min-height: 100vh; margin: 0; font-family: 'Poppins', sans-serif; }
        .pesapal-header { background: #fff; padding: 14px 24px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 16px; }
        .pesapal-header img { height: 40px; }
        .pesapal-header h5 { margin: 0; color: #1a2e25; font-size: 16px; font-weight: 600; }
        .iframe-wrap { flex: 1; padding: 24px; max-width: 900px; margin: 0 auto; width: 100%; }
        .iframe-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,.08); overflow: hidden; }
        .iframe-card-header { background: #2D6A4F; color: #fff; padding: 16px 24px; }
        .iframe-card-header h6 { margin: 0; font-size: 15px; font-weight: 600; }
        .iframe-card-header p { margin: 4px 0 0; font-size: 13px; opacity: .8; }
        iframe { display: block; width: 100%; border: 0; min-height: 650px; }
        .back-link { text-align: center; padding: 16px; font-size: 13px; color: #6b7c75; }
        .back-link a { color: #2D6A4F; text-decoration: none; font-weight: 500; }
        .loading-overlay { position: absolute; inset: 0; background: #fff; display: flex; align-items: center; justify-content: center; z-index: 10; border-radius: 0 0 10px 10px; }
    </style>
</head>
<body>

    <div class="pesapal-header">
        <img src="{{ getSettingImage('app_logo') }}" alt="{{ getOption('app_name') }}">
        <h5>{{ getOption('app_name') }} — Secure Payment</h5>
    </div>

    <div class="iframe-wrap">
        <div class="iframe-card">
            <div class="iframe-card-header">
                <h6>Complete Your Payment via Pesapal</h6>
                <p>Select your preferred payment method — Mobile Money, Card, or Bank Transfer</p>
            </div>

            <div style="position:relative;">
                <div class="loading-overlay" id="loadingOverlay">
                    <div class="text-center">
                        <div class="spinner-border text-success" role="status" style="width:3rem;height:3rem;"></div>
                        <p class="mt-3 text-muted" style="font-size:14px;">Loading payment page…</p>
                    </div>
                </div>
                <iframe
                    src="{{ $iframeSrc }}"
                    scrolling="no"
                    allowtransparency="true"
                    id="pesapalIframe"
                    onload="document.getElementById('loadingOverlay').style.display='none'">
                    <p>Your browser does not support iframes. Please <a href="{{ $iframeSrc }}">click here</a> to pay.</p>
                </iframe>
            </div>
        </div>

        <div class="back-link">
            <a href="{{ route('tenant.invoice.index') }}">← Return to invoices</a>
            &nbsp;|&nbsp;
            <span>Order reference: <strong>#{{ $orderId }}</strong></span>
        </div>
    </div>

    <script>
        // Auto-height: expand iframe to avoid scroll bars inside it
        function resizeIframe(obj) {
            try { obj.style.height = obj.contentWindow.document.documentElement.scrollHeight + 'px'; } catch(e) {}
        }
        document.getElementById('pesapalIframe').addEventListener('load', function() {
            resizeIframe(this);
        });
    </script>
</body>
</html>
