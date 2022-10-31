<?php

declare(strict_types=1);

namespace CamooPay\Services\Subscription;

use CamooPay\Http\Client;
use CamooPay\Http\ResponseInterface;
use CamooPay\Services\ClientApiInterface;
use Maviance\S3PApiClient\ApiClient;
use Maviance\S3PApiClient\Configuration;

final class SubscriptionApi implements ClientApiInterface
{
    private Client $client;

    public function __construct(ApiClient $client, Configuration $config, ?string $model = null)
    {
        $model = $model ?? 'Subscription';
        $this->client = new Client($client, $config, $model);
    }

    public function get(string $merchant, int $serviceId): ResponseInterface
    {
        return $this->client->get('/subscription', [
            'merchant' => $merchant,
            'serviceid' => $serviceId,
        ])->getResult();
    }
}
