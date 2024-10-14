<?php

declare(strict_types=1);

namespace CamooPay\Jobs;

use CamooPay\Services\CamooPayServiceLocatorTrait;
use CamooPay\Services\CashIn\CashinApi;
use CamooPay\Validators\ChargeValidation;
use Throwable;

class CashInChargeJob
{
    use CamooPayServiceLocatorTrait;

    private const SERVICE_NAME = 'Cashin';

    private const MODEL_NAME = 'CollectionResponse';

    private CashinApi $cashInApi;

    public function __construct(private readonly string $token, private readonly string $secret)
    {
        $this->cashInApi = $this->getCamooPayLocator()->get(self::SERVICE_NAME, $token, $secret, self::MODEL_NAME);
    }

    public function handle(string $referenceId, string $quoteId, string $phoneNumber, string $email): ?array
    {
        try {
            $phoneNumber = !preg_match('/^(237)\s*/', $phoneNumber) ? '237' . $this->cleanPone($phoneNumber) :
                $this->cleanPone($phoneNumber);

            $charge = [
                'quoteId' => $quoteId,
                'customerPhonenumber' => $phoneNumber,
                'customerEmailaddress' => $email,
                'trid' => $referenceId,
                'serviceNumber' => $phoneNumber,
            ];
            $chargeValidation = new ChargeValidation($charge);
            if ($chargeValidation->isValid() === false) {
                return null;
            }

            return $this->cashInApi->requestCharge($charge)->toArray();
        } catch (Throwable $exception) {
            echo $exception->getMessage();
        }

        return null;
    }

    private function cleanPone(string|int $xTel): ?string
    {
        if (empty($xTel)) {
            return null;
        }

        return preg_replace('/[^\dxX]/', '', (string)$xTel);
    }
}
