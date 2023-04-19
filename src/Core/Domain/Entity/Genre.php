<?php

namespace Core\Domain\Entity;

use Core\Domain\Entity\Traits\MethodsMagicTrait;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Factory\GenreValidatorFactory;
use Core\Domain\Notification\NotificationException;
use Core\Domain\Validation\DomainValidation;
use Core\Domain\ValueObject\Uuid;
use DateTime;

class Genre extends Entity
{
    public function __construct(
        protected string $name,
        protected ?Uuid $id = null,
        protected bool $isActive = true,
        protected array $categoriesId = [],
        protected ?DateTime $createdAt = null,
    ) {
        parent::__construct();
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
       GenreValidatorFactory::create()->validate($this);
        if($this->notification->hasErrors()) {
            throw new NotificationException($this->notification->getMessage('genre'));
        }
    }
}
