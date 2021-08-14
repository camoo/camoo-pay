<?php
declare(strict_types=1);

namespace CamooPay\Jobs;

use CamooPay\Service\CashoutApi;
use CamooPay\Service\CamooPayServiceLocatorTrait;
use CamooPay\Validators\ChargeValidation;
use Throwable;

class CashoutChargeJob
{
    use CamooPayServiceLocatorTrait;

    private CashoutApi $cashoutApi;
    private const SERVICE_NAME = 'Cashout';
    private const MODEL_NAME = 'CollectionResponse';

    public function __construct(string $token, string $secret)
    {
        $this->cashoutApi = $this->getCamooPayLocator()->get(self::SERVICE_NAME, $token, $secret, self::MODEL_NAME);
    }

    public function handle(string $referenceId, string $quoteId, string $phoneNumber, string $email): ?array
    {
        try {
            $phoneNumber = !preg_match('/^(237)\s*/', $phoneNumber) ? '237' . $this->_cleanPone($phoneNumber) :
                $this->_cleanPone($phoneNumber);

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

            $collection = $this->cashoutApi->requestCharge($charge);

            return $collection->toArray();
        } catch (Throwable $exception) {
            echo $exception->getMessage();
        }
        return null;
    }

    private function _cleanPone($xTel)
    {
        if (empty($xTel)) {
            return null;
        }
        return preg_replace('/[^\dxX]/', '', $xTel);
    }
}
