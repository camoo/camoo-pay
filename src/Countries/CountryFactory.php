<?php

declare(strict_types=1);

namespace CamooPay\Countries;

use CamooPay\Exception\CamooPayException;

class CountryFactory
{
    public static function getInstance(string $country): ?CountryInterface
    {
        if (!class_exists(__NAMESPACE__ . '\\' . $country)) {
            throw new CamooPayException(sprintf('Country Class %s,Not Found !', __NAMESPACE__ . '\\' . $country));
        }

        return match ($country) {
            CountryInterface::CM => new CM(),
            default => null
        };
    }
}
