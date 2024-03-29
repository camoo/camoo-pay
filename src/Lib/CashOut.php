<?php

declare(strict_types=1);

namespace CamooPay\Lib;

use Cake\Utility\Text;
use CamooPay\Collection\ResponseCollection;
use CamooPay\Countries\CountryFactory;
use CamooPay\Countries\CountryInterface;
use CamooPay\Exception\CamooPayCashoutException;
use CamooPay\Jobs\CashoutQuoteJob;
use CamooPay\Services\CamooPayServiceLocatorTrait;
use CamooPay\Services\Cashout\CashoutApi;
use CamooPay\Validators\AllowedNetworkValidation;
use CamooPay\Validators\PhoneNumberValidation;

class CashOut
{
    use CamooPayServiceLocatorTrait;

    private const SERVICE_NAME = 'Cashout';

    private CashoutApi $cashoutApi;

    public function __construct(
        private string $token,
        private string $secret,
        private string $carrier,
        private string $country = CountryInterface::CM
    ) {
        $this->cashoutApi = $this->getCamooPayLocator()->get(self::SERVICE_NAME, $token, $secret);
    }

    public function charge(string $phoneNumber, float $amount, string $email): ?array
    {
        $allowedValidation = new AllowedNetworkValidation($this->carrier, $this->country);

        if ($allowedValidation->isValid() === false) {
            throw new CamooPayCashoutException(sprintf(
                'Carrier %s is not allowed for country %s',
                $this->carrier,
                $this->country
            ));
        }

        $phoneNumberValidation = new PhoneNumberValidation($phoneNumber, $this->carrier);
        if ($phoneNumberValidation->isValid() === false) {
            throw new CamooPayCashoutException('Invalid phone number');
        }

        $paymentId = $this->getPaymentId();
        $referenceId = Text::uuid();

        if ($paymentId === null) {
            throw new CamooPayCashoutException('Payment Id could not be not retrieved !');
        }

        return (new CashoutQuoteJob($this->token, $this->secret))
            ->handle($referenceId, $paymentId, $phoneNumber, $amount, $email);
    }

    private function getPaymentId(): ?string
    {
        $oCountry = CountryFactory::getInstance($this->country);
        if (!$oCountry instanceof CountryInterface) {
            return null;
        }
        $merchantName = $oCountry->getMerchantNameByCarrier($this->carrier);

        /** @var ResponseCollection|\Maviance\S3PApiClient\Model\Cashout[] $providers */
        $providers = $this->cashoutApi->getProviders();
        foreach ($providers as $provider) {
            if ($provider->getMerchant() === $merchantName) {
                return $provider->getPayItemId();
            }
        }

        return null;
    }
}
