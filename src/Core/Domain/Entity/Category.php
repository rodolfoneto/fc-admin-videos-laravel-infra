<?php

namespace Core\Domain\Entity;

use Core\Domain\Entity\Traits\MethodsMagicTrait;
use Core\Domain\Validation\DomainValidation;
use Core\Domain\ValueObject\Uuid;
use DateTime;

class Category
{

    use MethodsMagicTrait;

    public function __construct(
        protected Uuid|string $id = '',
        protected string $name = "",
        protected string $description = '',
        protected bool $isActive = true,
        protected DateTime|string $createdAt = '',
        protected DateTime|string $updatedAt = '',
    ) {
        $this->id = $this->id ? new Uuid($this->id) : Uuid::random();
        $this->createdAt = $this->createdAt ? new DateTime($this->createdAt) : new DateTime();
        $this->updatedAt = $this->updatedAt ? new DateTime($this->updatedAt) : new DateTime();
        $this->validate();
    }

    public function activate(): void
    {
        $this->isActive = true;
    }

    public function deactivate(): void
    {
        $this->isActive = false;
    }

    public function update(string $name, string $description = null, bool $isActive = true): void
    {
        $this->name = $name;
        $this->description = $description ?? $this->description;
        $this->isActive = $isActive;

        $this->validate();
    }

    private function validate()
    {
        DomainValidation::notNull($this->name);
        DomainValidation::strMinLength($this->name, 2);
        DomainValidation::strMaxLength($this->name);
        DomainValidation::strCanNullOrMaxLength($this->description);
    }
}
