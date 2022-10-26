<?php

declare(strict_types=1);

namespace CamooPay\Lib;

use CamooPay\Collection\ResponseCollection;
use CamooPay\Services\CamooPayServiceLocatorTrait;
use CamooPay\Services\Service\ServiceApi;

final class Service
{
    use CamooPayServiceLocatorTrait;

    private const SERVICE_NAME = 'Service';

    private ServiceApi $serviceApi;

    public function __construct(string $token, string $secret)
    {
        $this->serviceApi = $this->getCamooPayLocator()->get(self::SERVICE_NAME, $token, $secret);
    }

    public function get(): ResponseCollection
    {
        return $this->serviceApi->get();
    }
}
