<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Receives incoming messages from OpenWA and logs them.
 *
 * Point your OpenWA session webhook to: /api/whatsapp/webhook
 *
 * Payload shape (OpenWA sends):
 *   { "type": "message", "data": { "from": "628xxx@c.us", "body": "...", ... } }
 */
class WhatsAppWebhookController extends Controller
{
    public function receive(Request $request)
    {
        $payload = $request->all();
        $type    = $payload['type'] ?? 'unknown';

        if ($type === 'message') {
            $data   = $payload['data'] ?? [];
            $from   = $data['from'] ?? 'unknown';
            $body   = $data['body'] ?? '';
            Log::channel('stack')->info("WhatsApp incoming [{$from}]: {$body}", $data);
        } else {
            // Log non-message events at debug level only
            Log::debug('WhatsApp webhook event: ' . $type, $payload);
        }

        // Always return 200 to acknowledge delivery
        return response()->json(['status' => 'ok']);
    }
}
