<?php

namespace CamooPay\Services\Merchant;

use CamooPay\Http\Client;
use CamooPay\Http\ResponseInterface;
use Maviance\S3PApiClient\ApiClient;
use Maviance\S3PApiClient\Configuration;

final class MerchantApi
{
    private Client $client;

    public function __construct(ApiClient $client, Configuration $config, ?string $model = null)
    {
        $model = $model ?? 'object';
        $this->client = new Client($client, $config, $model);
    }

    public function get(): ResponseInterface
    {
        return $this->client->get('/merchant')->getResult();
    }
}
