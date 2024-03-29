<?php

declare(strict_types=1);

namespace CamooPay\Jobs;

use CamooPay\Services\Bill\BillApi;
use CamooPay\Services\CamooPayServiceLocatorTrait;
use CamooPay\Validators\ChargeValidation;
use Maviance\S3PApiClient\Model\CollectionResponse;
use Throwable;

class PayBillJob
{
    use CamooPayServiceLocatorTrait;

    private const SERVICE_NAME = 'Bill';

    private const MODEL_NAME = 'CollectionResponse';

    private BillApi $billApi;

    public function __construct(string $token, string $secret)
    {
        $this->billApi = $this->getCamooPayLocator()->get(self::SERVICE_NAME, $token, $secret, self::MODEL_NAME);
    }

    public function handle(
        string $referenceId,
        string $quoteId,
        string $phoneNumber,
        string $email,
        string $serviceNumber
    ): ?CollectionResponse {
        try {
            $phoneNumber = !preg_match('/^(237)\s*/', $phoneNumber) ? '237' . $this->cleanUpNumber($phoneNumber) :
                $this->cleanUpNumber($phoneNumber);
            $charge = [
                'quoteId' => $quoteId,
                'customerPhonenumber' => $phoneNumber,
                'customerEmailaddress' => $email,
                'trid' => $referenceId,
                'serviceNumber' => $serviceNumber,
            ];
            $chargeValidation = new ChargeValidation($charge);
            if ($chargeValidation->isValid() === false) {
                return null;
            }

            $collection = $this->billApi->applyPay($charge);

            return $collection->first();
        } catch (Throwable $exception) {
            echo $exception->getMessage();
        }

        return null;
    }

    private function cleanUpNumber($xTel): ?string
    {
        if (empty($xTel)) {
            return null;
        }

        return preg_replace('/[^\dxX]/', '', $xTel);
    }
}
