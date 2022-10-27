<?php

declare(strict_types=1);

namespace CamooPay\Validators;

final class QuoteValidation extends AppValidation
{
    public function isValid(): bool
    {
        $this->validator
            ->decimal('amount')
            ->requirePresence('amount');
        $this->validator
            ->scalar('payItemId')
            ->requirePresence('payItemId')
            ->notEmptyString('payItemId');

        return $this->validate();
    }
}
