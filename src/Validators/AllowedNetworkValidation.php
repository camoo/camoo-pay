<?php
declare(strict_types=1);

namespace CamooPay\Validators;

use Cake\Validation\Validation;
use CamooPay\Countries\CountryInterface;

final class AllowedNetworkValidation
{

    private string $carrier;
    private string $country;

    public function __construct(string $carrier, string $country)
    {
        $this->carrier = $carrier;
        $this->country = $country;
    }

    public function isValid(): bool
    {
        $countryClass = '\\CamooPay\\Countries\\'. $this->country;
        /** @var CountryInterface $oCountry */
        $oCountry = new $countryClass;
        $allowedNetworks = array_keys($oCountry->getMerchants());
        return Validation::inList($this->carrier, $allowedNetworks, true);
    }
}