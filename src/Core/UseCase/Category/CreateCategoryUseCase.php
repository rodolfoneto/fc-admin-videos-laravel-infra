<?php

namespace Core\UseCase\Category;

use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\DTO\Category\{
    CategoryCreateInputDto,
    CategoryCreateOutputDto,
};

class CreateCategoryUseCase
{
    protected $repository;

    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(CategoryCreateInputDto $input): CategoryCreateOutputDto
    {
        $category = new Category(
            name:        $input->name,
            description: $input->description,
            isActive:    $input->is_active
        );

        $categoryInserted = $this->repository->insert($category);
        
        return new CategoryCreateOutputDto(
            id: $categoryInserted->id(),
            name: $categoryInserted->name,
            description: $categoryInserted->description,
            is_active: $categoryInserted->isActive,
            created_at: $categoryInserted->createdAt(),
        );
    }
}
