<?php

namespace Core\Domain\Entity;

use Core\Domain\Entity\Traits\MethodsMagicTrait;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Validation\DomainValidation;
use Core\Domain\ValueObject\Uuid;
use DateTime;

class Genre
{

    use MethodsMagicTrait;

    public function __construct(
        protected string $name,
        protected ?Uuid $id = null,
        protected bool $isActive = true,
        protected array $categoriesId = [],
        protected ?DateTime $createdAt = null,
    ) {
        $this->id = $this->id ? $this->id : Uuid::random();
        $this->createdAt = $this->createdAt ? $this->createdAt : new DateTime();

        $this->valid();
    }

    public function deactivate(): void
    {
        $this->isActive = false;
    }

    public function activate(): void
    {
        $this->isActive = true;
    }

    public function update(string $name): void
    {
        $this->name = $name;
        $this->valid();
    }

    public function addCategory(string $categoryId): void
    {
        array_push($this->categoriesId, $categoryId);
    }

    public function removeCategory(string $categoryId): void
    {
        $index = array_search($categoryId, $this->categoriesId);
        if ($index === false) {
            throw new NotFoundException('Category id not related with Genre');
        }
        array_splice($this->categoriesId, $index, 1);
    }

    protected function valid(): void
    {
        DomainValidation::notNull($this->name, 'The name field is required.');
        DomainValidation::strMinLength($this->name, 2, "The name must be at least 2 characters.");
        DomainValidation::strMaxLength($this->name, 255, "The name must not be greater than 255 characters.");
    }
}
