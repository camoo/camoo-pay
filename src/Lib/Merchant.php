<?php

declare(strict_types=1);

namespace CamooPay\Lib;

use CamooPay\Collection\ResponseCollection;
use CamooPay\Services\CamooPayServiceLocatorTrait;
use CamooPay\Services\Merchant\MerchantApi;

final class Merchant
{
    use CamooPayServiceLocatorTrait;

    private const SERVICE_NAME = 'Merchant';

    private MerchantApi $merchantApi;

    public function __construct(string $token, string $secret)
    {
        $this->merchantApi = $this->getCamooPayLocator()->get(self::SERVICE_NAME, $token, $secret);
    }

    public function get(): ResponseCollection
    {
        return $this->merchantApi->get();
    }
}
