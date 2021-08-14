<?php
declare(strict_types=1);

namespace CamooPay\Http;

use Maviance\S3PApiClient\ApiClient;
use Maviance\S3PApiClient\Configuration;

class ApiClientFactory
{

    public static function create(string $token, string $secret): ApiClient
    {
        return new ApiClient($token, $secret, ['verify' => true]);
    }
}