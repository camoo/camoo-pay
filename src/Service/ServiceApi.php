<?php
declare(strict_types=1);

namespace CamooPay\Service;

use Maviance\S3PApiClient\ApiClient;
use Maviance\S3PApiClient\Configuration;
use CamooPay\Http\Client;
use CamooPay\Http\ResponseInterface;

class ServiceApi
{

    private Client $client;

    public function __construct(ApiClient $client, Configuration $config, ?string $model = null)
    {
        $model = $model ?? 'Service';
        $this->client = new Client($client, $config, $model);
    }

    public function getById(int $id): ResponseInterface
    {
        return $this->client->get('/service/' . $id)->getResult();
    }

}