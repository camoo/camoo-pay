<?php

declare(strict_types=1);

namespace CamooPay\Http;

use Maviance\S3PApiClient\Model\ModelInterface;

interface ResponseInterface
{
    public function first(): ?ModelInterface;

    public function firstOrFail(): ModelInterface;

    public function get(int $position);

    public function getOrFail(int $position): ModelInterface;

    public function isEmpty(): bool;

    public function count(): int;

    public function toArray(): array;
}
