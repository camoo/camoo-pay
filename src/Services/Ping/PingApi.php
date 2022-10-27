<?php

declare(strict_types=1);

namespace CamooPay\Services\Ping;

use CamooPay\Http\Client;
use CamooPay\Http\ResponseInterface;
use Maviance\S3PApiClient\ApiClient;
use Maviance\S3PApiClient\Configuration;

/**
 * Check on the availability of the api.
 * This endpoint simply checks the existence and validity of the request on the server by returning a valid
 * response object or an error message. Its primary purpose is to provide a feedback on whether the API is available.
 * It also provides the current server time and timezone
 */
class PingApi
{
    private Client $client;

    public function __construct(ApiClient $client, Configuration $config, ?string $model = null)
    {
        $model = $model ?? 'Ping';
        $this->client = new Client($client, $config, $model);
    }

    public function get(): ResponseInterface
    {
        return $this->client->get('/ping')->getResult();
    }
}
