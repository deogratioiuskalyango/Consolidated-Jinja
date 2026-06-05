<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $baseUrl;
    private string $apiKey;
    private string $sessionId;
    private bool   $enabled;

    public function __construct()
    {
        $this->baseUrl   = rtrim(config('services.openwa.url', ''), '/');
        $this->apiKey    = config('services.openwa.key', '');
        $this->sessionId = config('services.openwa.session', 'default');
        $this->enabled   = (bool) config('services.openwa.enabled', false);
    }

    // ── Public API ────────────────────────────────────────────────────────────

    /**
     * Send a plain-text WhatsApp message.
     * Returns true on success, false on any failure (never throws).
     */
    public function send(string $phone, string $message): bool
    {
        if (! $this->isReady($phone)) {
            return false;
        }

        $chatId = $this->normalizePhone($phone);

        try {
            $response = Http::timeout(10)
                ->withHeaders(['X-API-Key' => $this->apiKey])
                ->post("{$this->baseUrl}/api/sessions/{$this->sessionId}/messages/send-text", [
                    'chatId' => $chatId,
                    'text'   => $message,
                ]);

            if (! $response->successful()) {
                Log::warning('WhatsApp send failed', [
                    'phone'  => $phone,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('WhatsApp send error: ' . $e->getMessage(), ['phone' => $phone]);
            return false;
        }
    }

    /**
     * Send to multiple numbers. Silently skips empty numbers.
     */
    public function sendBulk(array $phones, string $message): void
    {
        foreach ($phones as $phone) {
            if ($phone) {
                $this->send($phone, $message);
            }
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function isReady(string $phone): bool
    {
        if (! $this->enabled) {
            return false;
        }
        if (! $this->baseUrl || ! $this->apiKey) {
            return false;
        }
        return (bool) trim($phone);
    }

    /**
     * Normalise any local/international number to WhatsApp chat ID format.
     * Examples:
     *   08123456789   → 628123456789@c.us   (Indonesia, leading 0 → country code)
     *   +628123456789 → 628123456789@c.us
     *   628123456789  → 628123456789@c.us
     *
     * Adjust the country-code prefix in .env as needed:
     *   OPENWA_COUNTRY_CODE=62
     */
    private function normalizePhone(string $phone): string
    {
        // Strip everything except digits
        $digits = preg_replace('/\D/', '', $phone);

        // Replace leading 0 with country code
        $countryCode = config('services.openwa.country_code', '62');
        if (str_starts_with($digits, '0')) {
            $digits = $countryCode . substr($digits, 1);
        }

        return $digits . '@c.us';
    }
}
