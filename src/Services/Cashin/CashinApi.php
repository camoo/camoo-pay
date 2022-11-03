<?php

declare(strict_types=1);

namespace CamooPay\Services\Cashin;

use CamooPay\Http\Client;
use CamooPay\Http\ResponseInterface;
use Maviance\S3PApiClient\ApiClient;
use Maviance\S3PApiClient\Configuration;

class CashinApi
{
    private Client $client;

    public function __construct(ApiClient $apiClient, Configuration $config, ?string $model = null)
    {
        $model = $model ?? 'Cashin';
        $this->client = new Client($apiClient, $config, $model);
    }

    public function getProviders(): ResponseInterface
    {
        return $this->client->get('/cashin')->getResult();
    }

    public function requestQuote(float $amount, string $paymentId): ResponseInterface
    {
        $data = ['amount' => $amount, 'payItemId' => $paymentId];

        return $this->client->post('/quotestd', $data)->getResult();
    }

    public function requestCharge(array $payload): ResponseInterface
    {
        return $this->client->post('/collectstd', $payload)->getResult();
    }
}
