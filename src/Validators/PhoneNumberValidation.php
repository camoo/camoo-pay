<?php

declare(strict_types=1);

namespace CamooPay\Validators;

use CamooPay\Countries\CM;
use CamooPay\Exception\CamooPayException;

final class PhoneNumberValidation
{
    protected array $hMobilNetworksValidation = [
        'CM' => [
            'mtn' => [CM::class, 'isMTN'],
            'orange' => [CM::class, 'isOrange'],
        ],
    ];

    private string $phoneNumber;

    private string $carrier;

    private string $country;

    public function __construct(string $phoneNumber, string $carrier, string $county = 'CM')
    {
        $this->phoneNumber = $phoneNumber;
        $this->carrier = $carrier;
        $this->country = $county;
    }

    public function isValid(): bool
    {
        if (!array_key_exists($this->country, $this->hMobilNetworksValidation)) {
            throw new CamooPayException(sprintf('Country %s not supported', $this->country));
        }
        $validator = $this->hMobilNetworksValidation[$this->country][$this->carrier];

        return call_user_func($validator, $this->phoneNumber);
    }
}
