<?php
declare(strict_types=1);

namespace CamooPay\Validators;

final class ChargeValidation extends AppValidation
{

    public function isValid(): bool
    {
        $this->validator
            ->requirePresence('quoteId')
            ->scalar('quoteId')
            ->notEmptyString('quoteId');

        $this->validator
            ->integer('customerPhonenumber')
            ->requirePresence('customerPhonenumber')
            ->notEmptyString('customerPhonenumber');
        $this->validator
            ->email('customerEmailaddress')
            ->requirePresence('customerEmailaddress')
            ->notEmptyString('customerEmailaddress');

        $this->validator
            ->scalar('trid')
            ->requirePresence('trid')
            ->notEmptyString('trid');

        $this->validator
            ->scalar('customerName')
            ->allowEmptyString('customerName');

        $this->validator
            ->scalar('customerAddress')
            ->allowEmptyString('customerAddress');

        $this->validator
            ->scalar('customerNumber')
            ->allowEmptyString('customerNumber');

        $this->validator
            ->scalar('serviceNumber')
            ->requirePresence('serviceNumber')
            ->notEmptyString('serviceNumber');
        return $this->validate();
    }
}