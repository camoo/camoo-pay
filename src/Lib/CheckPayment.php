<?php
declare(strict_types=1);

namespace CamooPay\Lib;

use CamooPay\Services\CamooPayServiceLocatorTrait;
use CamooPay\Services\VerifyApi;

class CheckPayment
{
    use CamooPayServiceLocatorTrait;

    private const SERVICE_NAME = 'Verify';
    private const MODEL_NAME = 'object';

    private VerifyApi $verifyApi;

    public function __construct(string $token, string $secret)
    {
        $this->verifyApi = $this->getCamooPayLocator()->get(self::SERVICE_NAME, $token, $secret, self::MODEL_NAME);
    }

    public function check(string $transactionNumber): ?array
    {
        $result = $this->verifyApi->verify($transactionNumber);
        return $result->get(0);
    }
}