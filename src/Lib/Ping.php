<?php
declare(strict_types=1);

namespace CamooPay\Lib;

use CamooPay\Http\ResponseInterface;
use CamooPay\Services\Account\AccountApi;
use CamooPay\Services\CamooPayServiceLocatorTrait;

class Ping
{
    use CamooPayServiceLocatorTrait;

    private const SERVICE_NAME = 'Ping';

    private AccountApi $accountApi;

    public function __construct(string $token, string $secret)
    {
        $this->accountApi = $this->getCamooPayLocator()->get(self::SERVICE_NAME, $token, $secret);
    }

    public function check(): ResponseInterface
    {
        return $this->accountApi->get();
    }
}
