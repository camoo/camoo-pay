<?php

declare(strict_types=1);

namespace CamooPay\Validators;

use Cake\Validation\Validator;

class AppValidation
{
    protected Validator $validator;

    private array $errors = [];

    private array $data;

    public function __construct(array $data, ?Validator $validator = null)
    {
        $this->validator = $validator ?? new Validator();
        $this->data = $data;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    protected function validate(): bool
    {
        $this->errors = $this->validator->validate($this->data);

        return empty($this->errors);
    }
}
