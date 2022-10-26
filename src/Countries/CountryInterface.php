<?php

declare(strict_types=1);

namespace CamooPay\Countries;

interface CountryInterface
{
    public function getMerchants(): array;

    public function getMerchantNameByCarrier(string $carrier): string;
}
