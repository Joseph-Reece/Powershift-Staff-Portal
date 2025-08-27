<?php

namespace App\Services;

use App\Models\HREmployee;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class BusinessCentralService
{
    protected Client $client;
    protected string $baseUrl;
    protected string $company;
    private BusinessCentralAuthService $authService;

    public function __construct(BusinessCentralAuthService $authService)
    {
        $this->client = new Client();
        $this->baseUrl = rtrim(config('businesscentral.base_url'), '/');
        $this->authService = $authService;
        $this->company = config('businesscentral.company');
    }

    public function findEmployeeByStaffNo(string $staffNo): ?object
    {
        $result = $this->callPage(HREmployee::wsName(), [
            '$filter' => "No eq '{$staffNo}'"
        ]);
        return !empty($result->value) ? (object)$result->value[0] : null;
    }

    /**
     * Call a Business Central Codeunit Action via OData (POST)
     */
    public function callCodeunitAction(
        string $serviceName,
        string $procedureName,
        array $parameters = []
    ): ?object {
        return $this->sendRequest('POST', "/ODataV4/{$serviceName}_{$procedureName}", $parameters);
    }

    /**
     * Call a Business Central Page via OData (GET)
     *
     * Example: List employees, filter, etc.
     *
     * @param string $pageName   Published page name in BC
     * @param string $company    Company name
     * @param array  $filters    OData filters/query params
     * @return object|null
     */
    public function callPage(
        string $pageName,
        array $filters = []
    ): ?object {
        // Build query string for filters
        $queryString = '';
        if (!empty($filters)) {
            $queryString = '&' . http_build_query($filters);
        }

        return $this->sendRequest('GET', "/ODataV4/{$pageName}", [], $queryString);
    }

    /**
     * Internal reusable request method
     */
    private function sendRequest(
        string $method,
        string $endpoint,
        array $parameters = [],
        string $queryString = ''
    ): ?object {
        $token = $this->authService->getToken();

        if (!$token) {
            throw new \Exception('Unable to get Business Central token');
        }

        $companyEncoded = rawurlencode($this->company);
        $url = $this->baseUrl . $endpoint . "?company={$companyEncoded}{$queryString}";

        $options = [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ],
        ];

        if ($method === 'POST') {
            $options['json'] = $this->castResponseToObject($parameters);
        }

        try {
            $response = $this->client->request($method, $url, $options);
            $responseData = json_decode($response->getBody()->getContents(), true);
            return $this->castResponseToObject($responseData);
        } catch (RequestException $e) {
            $errorMessage = $e->hasResponse()
                ? $e->getResponse()->getBody()->getContents()
                : $e->getMessage();

            Log::error('Business Central API Error', [
                'method'     => $method,
                'url'        => $url,
                'parameters' => $parameters,
                'error'      => $errorMessage,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Unexpected Business Central API Error', [
                'method'     => $method,
                'url'        => $url,
                'parameters' => $parameters,
                'error'      => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Utility: convert arrays to objects recursively
     */
    private function castResponseToObject($data): object
    {
        return json_decode(json_encode($data), false);
    }
}
