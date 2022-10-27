<?php

declare(strict_types=1);

namespace CamooPay\Services\Bill;

use CamooPay\Http\Client;
use CamooPay\Http\ResponseInterface;
use Maviance\S3PApiClient\ApiClient;
use Maviance\S3PApiClient\Configuration;

class BillApi
{
    private Client $client;

    public function __construct(ApiClient $client, Configuration $config, ?string $model = null)
    {
        $model = $model ?? 'object';
        $this->client = new Client($client, $config, $model);
    }

    public function get(string $serviceNumber, string $merchant, int $serviceId): ResponseInterface
    {
        return $this->client->get('/bill', [
            'serviceNumber' => $serviceNumber,
            'merchant' => $merchant,
            'serviceid' => $serviceId,
        ])->getResult();
    }

    public function applyPay(array $payload): ResponseInterface
    {
        return $this->client->post('/collectstd', $payload)->getResult();
    }
}
