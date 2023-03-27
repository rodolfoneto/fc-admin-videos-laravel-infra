<?php

namespace Core\Domain\ValueObject;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid as RamseyUuid;

class Uuid
{
    public function __construct(
        private string $value
    ) {
        $this->isValid($value);
    }
    
    public static function random(): self
    {
        return new self(RamseyUuid::uuid4()->toString());
    }

    private function isValid($value)
    {
        if (!RamseyUuid::isValid($value)) {
            throw new InvalidArgumentException(sprintf('<%s> does not allow the value <%s>.',
             static::class,
             $value));
        }
    }

    public function __toString()
    {
        return $this->value;
    }
}
