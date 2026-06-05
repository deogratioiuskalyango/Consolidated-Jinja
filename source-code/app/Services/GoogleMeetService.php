<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use DateTimeImmutable;

/**
 * Google Meet REST API Service
 *
 * Authentication: OAuth2 service-account JWT (RS256).
 * Requires a Google Cloud service account with domain-wide delegation
 * and the "Google Meet API" (meetings.space.created scope) enabled.
 *
 * ENV keys:
 *   GOOGLE_MEET_SERVICE_ACCOUNT_JSON  — absolute path to the service-account JSON key file
 *   GOOGLE_MEET_IMPERSONATE_EMAIL     — the GSuite/Workspace user to impersonate (organizer email)
 */
class GoogleMeetService
{
    private Client $http;
    private string $tokenEndpoint = 'https://oauth2.googleapis.com/token';
    private string $meetEndpoint  = 'https://meet.googleapis.com/v2/spaces';
    private string $scope         = 'https://www.googleapis.com/auth/meetings.space.created';

    public function __construct()
    {
        $this->http = new Client(['timeout' => 15, 'connect_timeout' => 10]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Public API
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Create a Google Meet space and return summary data.
     *
     * @return array{name:string, meetingUri:string, meetingCode:string}
     * @throws \RuntimeException
     */
    public function createSpace(): array
    {
        $token = $this->getAccessToken();
        return $this->callCreateSpace($token);
    }

    /**
     * Check if the Google Meet integration is configured.
     */
    public static function isConfigured(): bool
    {
        $keyPath = config('services.google_meet.service_account_json');
        return !empty($keyPath) && file_exists($keyPath);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Internal — OAuth2
    // ──────────────────────────────────────────────────────────────────────────

    private function getAccessToken(): string
    {
        $keyPath = config('services.google_meet.service_account_json');
        if (empty($keyPath) || !file_exists($keyPath)) {
            throw new \RuntimeException('Google Meet: service account key file not configured or not found at path: ' . $keyPath);
        }

        $keyData = json_decode(file_get_contents($keyPath), true);
        if (!$keyData || !isset($keyData['private_key'], $keyData['client_email'])) {
            throw new \RuntimeException('Google Meet: invalid service account JSON (missing private_key or client_email).');
        }

        $impersonate = config('services.google_meet.impersonate_email');

        $jwt = $this->buildJwt(
            clientEmail: $keyData['client_email'],
            privateKey:  $keyData['private_key'],
            impersonate: $impersonate
        );

        try {
            $response = $this->http->post($this->tokenEndpoint, [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion'  => $jwt,
                ],
            ]);
            $body = json_decode($response->getBody()->getContents(), true);
            if (empty($body['access_token'])) {
                throw new \RuntimeException('Google Meet: token endpoint returned no access_token. Response: ' . json_encode($body));
            }
            return $body['access_token'];
        } catch (RequestException $e) {
            $msg = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            throw new \RuntimeException('Google Meet: failed to get access token — ' . $msg);
        }
    }

    private function buildJwt(string $clientEmail, string $privateKey, ?string $impersonate): string
    {
        $now    = new DateTimeImmutable();
        $expiry = $now->modify('+1 hour');

        $config = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::plainText($privateKey),
            InMemory::plainText($privateKey) // public key not needed for signing
        );

        $builder = $config->builder()
            ->issuedBy($clientEmail)
            ->permittedFor($this->tokenEndpoint)
            ->issuedAt($now)
            ->expiresAt($expiry)
            ->withClaim('scope', $this->scope);

        if (!empty($impersonate)) {
            $builder = $builder->relatedTo($impersonate);
        }

        return $builder->getToken($config->signer(), $config->signingKey())->toString();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Internal — Meet API
    // ──────────────────────────────────────────────────────────────────────────

    private function callCreateSpace(string $accessToken): array
    {
        try {
            $response = $this->http->post($this->meetEndpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type'  => 'application/json',
                ],
                'json' => new \stdClass(), // empty body creates a default space
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            if (empty($body['meetingUri'])) {
                throw new \RuntimeException('Google Meet: spaces.create returned unexpected response: ' . json_encode($body));
            }

            return [
                'name'        => $body['name']        ?? '',
                'meetingUri'  => $body['meetingUri']  ?? '',
                'meetingCode' => $body['meetingCode'] ?? '',
            ];
        } catch (RequestException $e) {
            $msg = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            throw new \RuntimeException('Google Meet: spaces.create failed — ' . $msg);
        }
    }
}
