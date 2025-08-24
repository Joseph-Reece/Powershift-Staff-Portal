<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BusinessCentralAuthService
{
    private string $tenantId;
    private string $clientId;
    private string $clientSecret;
    private string $scope;
    private Client $client;

    public function __construct()
    {
        $this->tenantId = config('businesscentral.tenant');
        $this->clientId = config('businesscentral.client_id');
        $this->clientSecret = config('businesscentral.client_secret');
        $this->scope = config('businesscentral.scope', 'https://api.businesscentral.dynamics.com/.default');
        $this->client = new Client();
    }

    /**
     * Get a valid Bearer Token (cached automatically).
     */
    public function getToken(): ?string
    {
        return Cache::remember('business_central_token', 300, function () {
            return $this->fetchToken();
        });
    }

    /**
     * Fetch a new token from Microsoft Identity Platform.
     */
    private function fetchToken(): ?string
    {
        $url = "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token";

        try {
            $response = $this->client->post($url, [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'scope' => $this->scope,
                ],
                'headers' => ['Accept' => 'application/json'],
            ]);

            $data = json_decode($response->getBody(), true);
            $token = $data['access_token'] ?? null;
            $expiresIn = $data['expires_in'] ?? 0;

            if ($token) {
                // Cache token slightly shorter than expiry
                Cache::put('business_central_token', $token, $expiresIn - 60);
            }

            return $token;
        } catch (\Exception $e) {
            Log::error("BC Auth Error: {$e->getMessage()}");
            return null;
        }
    }
}
