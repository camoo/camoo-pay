<?php
declare(strict_types=1);

namespace CamooPay\Services\Verify;

use CamooPay\Http\Client;
use CamooPay\Http\ResponseInterface;
use Maviance\S3PApiClient\ApiClient;
use Maviance\S3PApiClient\Configuration;

class VerifyApi
{
    private Client $client;

    public function __construct(ApiClient $client, Configuration $config, ?string $model = null)
    {
        $model = $model ?? 'object';
        $this->client = new Client($client, $config, $model);
    }

    public function verify(string $transactionNumber) : ResponseInterface
    {
        return $this->client->get('/verifytx', ['ptn' => $transactionNumber])->getResult();
    }
}
