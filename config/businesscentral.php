<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Business Central API Settings
    |--------------------------------------------------------------------------
    | Tenant, environment, and company settings for Business Central OData.
    | Base URL is built dynamically from tenant and environment values.
    */

    'tenant'  => env('BUSINESS_CENTRAL_TENANT_ID', 'c472639a-a5ae-4142-a893-48bc62fe1eeb'),
    'env'     => env('BUSINESS_CENTRAL_ENVIRONMENT', 'sandbox'),
    'company' => env('BUSINESS_CENTRAL_COMPANY', 'Powershift'),
    'client_id'     => env('BUSINESS_CENTRAL_CLIENT_ID'),
    'client_secret' => env('BUSINESS_CENTRAL_CLIENT_SECRET'),
    'scope'         => env('BUSINESS_CENTRAL_SCOPE', 'https://api.businesscentral.dynamics.com/.default'),

    'base_url' => sprintf(
        'https://api.businesscentral.dynamics.com/v2.0/%s/%s',
        env('BUSINESS_CENTRAL_TENANT_ID', 'c472639a-a5ae-4142-a893-48bc62fe1eeb'),
        env('BUSINESS_CENTRAL_ENVIRONMENT', 'sandbox')
    ),

];
