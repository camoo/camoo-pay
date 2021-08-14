<?php
declare(strict_types=1);

namespace CamooPay\Countries;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberToCarrierMapper;
use libphonenumber\PhoneNumberUtil;

class CM implements CountryInterface
{
    public function getMerchants(): array
    {
        return [
            'mtn' => 'MTNMOMO',
            'orange' => 'CMORANGEOM',
            'expressU' => 'EUCASHOUT',
        ];
    }

    public function getMerchantNameByCarrier(string $carrier) : string
    {
        $merchants = $this->getMerchants();
        return $merchants[$carrier];
    }

    private static function getPhoneInstance(string $phoneNumber, ?string $countryCode = null): ?PhoneNumber
    {
        if (empty($phoneNumber)) {
            return null;
        }

        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $numberProto = $phoneUtil->parse($phoneNumber, $countryCode);
        } catch (NumberParseException $exception) {
            return null;
        }

        return $numberProto;
    }

    public static function isMobile(string $phoneNumber): bool
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        /** @var null|PhoneNumber $phoneInstance */
        $phoneInstance = self::getPhoneInstance($phoneNumber, 'CM');
        return null !== $phoneInstance &&
            $phoneUtil->isValidNumber($phoneInstance) &&
            !empty($phoneUtil->getNumberType($phoneInstance)) &&
            $phoneInstance->getCountryCode() === 237;
    }

    public static function isOrange(string $phoneNumber): bool
    {
        $isSNMobile = self::isMobile($phoneNumber);
        if ($isSNMobile === false) {
            return false;
        }

        return (new CM)->getPhoneCarrier(self::getPhoneInstance($phoneNumber, 'CM')) === 'ORANGE';
    }

    public static function isMTN(string $phoneNumber): bool
    {
        $isSNMobile = self::isMobile($phoneNumber);
        if ($isSNMobile === false) {
            return false;
        }

        return (new CM)->getPhoneCarrier(self::getPhoneInstance($phoneNumber, 'CM')) === 'MTN';
    }

    private function getPhoneCarrier(PhoneNumber $phoneInstance): ?string
    {
        $oCarrierMapper = PhoneNumberToCarrierMapper::getInstance();
        $sCarrier = $oCarrierMapper->getNameForNumber($phoneInstance, 'en');
        if (empty($sCarrier)) {
            return null;
        }
        $asCarrier = explode(' ', $sCarrier);
        return strtoupper($asCarrier[0]);
    }

}