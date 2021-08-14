<?php
declare(strict_types=1);

namespace CamooPay\Collection;

use ArrayIterator;
use IteratorAggregate;
use Maviance\S3PApiClient\Model\ModelInterface;
use Maviance\S3PApiClient\ObjectSerializer;
use CamooPay\Exception\CamooPayResponseNotFoundException;
use CamooPay\Http\ResponseInterface;
use stdClass;

class ResponseCollection implements IteratorAggregate, ResponseInterface
{
    private array $values = [];

    private function __construct(array $items, string $returnType)
    {
        foreach ($items as $value) {
            $this->add($value, $returnType);
        }
    }

    public static function create(array $items, string $returnType): ResponseCollection
    {
        return new self($items, $returnType);
    }

    public function add(StdClass $item, string $returnType): void
    {
        $this->values[] = ObjectSerializer::deserialize($item, $returnType, []);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->values);
    }

    public function first(): ?ModelInterface
    {
        if (empty($this->values))
        {
            return null;
        }
        return $this->values[0];
    }

    public function get(int $position)
    {
        if(!array_key_exists($position, $this->values)) {
            return null;
        }
        return $this->values[$position];
    }

    public function firstOrFail(): ModelInterface
    {
        if (empty($this->values))
        {
            throw new CamooPayResponseNotFoundException('Entity at position "0" Not found');
        }
        return $this->values[0];
    }

    public function getOrFail(int $position): ModelInterface
    {
        if(!array_key_exists($position, $this->values)) {
            throw new CamooPayResponseNotFoundException(sprintf('Entity at position "%d" Not found', $position));
        }
        return $this->values[$position];
    }

    public function isEmpty(): bool
    {
        return empty($this->values);
    }

    public function count(): int
    {
        return count($this->values);
    }

    public function toArray(): array
    {
        return $this->values;
    }
}