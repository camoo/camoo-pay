<?php

declare(strict_types=1);

namespace CamooPay\Services;

use CamooPay\Constant\Config;
use CamooPay\Exception\CamooPayMissingServiceException;
use CamooPay\Http\ApiClientFactory;
use CamooPay\Http\ConfigurationFactory;
use Maviance\S3PApiClient\ApiClient;
use Maviance\S3PApiClient\Configuration;

class ServiceFactory
{
    /** Avoid new instance of the factory */
    private function __construct()
    {
    }

    public static function create(): ServiceFactory
    {
        return new self();
    }

    public function get(string $serviceName, string $token, string $secret, ?string $model = null, ?string $url = null)
    {
        $className = $this->getClassName($serviceName, true);

        if ($this->modelExists($className) === false) {
            $className = $this->getClassName($serviceName);
            if ($this->modelExists($className) === false) {
                throw new CamooPayMissingServiceException(sprintf('Service %s is cannot be found', $serviceName));
            }
        }

        $apiUrl = $_ENV['SMOBIL_PAY_API_URL'] ?? Config::API_URL;
        $url = $url ?? $apiUrl;

        $client = ApiClientFactory::create($token, $secret);
        $config = ConfigurationFactory::create($url);

        return $this->generateObject($className, $client, $config, $model);
    }

    private function getResourceName(string $serviceName): string
    {
        $suffix = substr($serviceName, -3);
        if ($suffix === 'Api') {
            return substr($serviceName, 0, -3);
        }

        return $serviceName;
    }

    private function getClassName(string $serviceName, bool $fallback = false): string
    {
        $resourceName = $this->getResourceName($serviceName);
        $apiName = $resourceName . 'Api';

        $resource = $resourceName . '\\';

        $nameSpace = $fallback === false ? '\\Maviance\\S3PApiClient\\Service\\' : __NAMESPACE__ . '\\' . $resource;

        return $nameSpace . $apiName;
    }

    private function modelExists(string $model): bool
    {
        return class_exists($model);
    }

    private function generateObject(
        string $className,
        ApiClient $client,
        Configuration $configuration,
        ?string $modelName = null
    ) {
        if ($modelName === null) {
            return new $className($client, $configuration);
        }

        return new $className($client, $configuration, $modelName);
    }
}
