<?php

declare(strict_types=1);

namespace CamooPay\Http;

use CamooPay\Collection\ResponseCollection;

class Response
{
    private int $code;

    private array $headers;

    private ResponseCollection $collection;

    public function __construct(ResponseCollection $collection, int $code, array $headers)
    {
        $this->code = $code;
        $this->headers = $headers;
        $this->collection = $collection;
    }

    public function getStatusCode(): int
    {
        return $this->code;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getResult(): ResponseCollection
    {
        return $this->collection;
    }
}
