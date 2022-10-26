<?php

declare(strict_types=1);

namespace CamooPay\Http;

use CamooPay\Constant\Config;
use Maviance\S3PApiClient\Configuration;

class ConfigurationFactory
{
    public static function create(?string $url = null): Configuration
    {
        $apiUrl = $_ENV['SMOBIL_PAY_API_URL'] ?? Config::API_URL;

        $url = $url ?? $apiUrl;
        $config = new Configuration();
        $config->setHost($url);
        $config->setUserAgent(Config::USER_AGENT);

        return $config;
    }
}
