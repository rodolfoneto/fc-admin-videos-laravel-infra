<?php

namespace Core\Domain\Entity;

use Core\Domain\Entity\Traits\MethodsMagicTrait;
use Core\Domain\Validation\DomainValidation;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\ValueObject\Uuid;
use DateTime;

class CastMember extends Entity
{
    public function __construct(
        protected string $name,
        protected CastMemberType $type,
        protected ?Uuid $id = null,
        protected ?DateTime $createdAt = null,
    ) {
        $this->id = $this->id ?? Uuid::random();
        $this->createdAt = $this->createdAt ?? new DateTime();
        $this->validate();
    }

    public function update(string $name): void
    {
        $this->name = $name;
    }

    protected function validate():void
    {
        DomainValidation::notNull($this->name, 'The name field is required.');
        DomainValidation::strMinLength($this->name, 2, "The name must be at least 2 characters.");
        DomainValidation::strMaxLength($this->name, 255, "The name must not be greater than 255 characters.");
    }
}
