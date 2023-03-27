<?php

namespace Core\UseCase\Category;

use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\DTO\Category\CategoryDeleteInputDto;
use Core\UseCase\DTO\Category\CategoryDeleteOutputDto;

class DeleteCategoryUseCase
{
    protected CategoryRepositoryInterface $repository;

    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(CategoryDeleteInputDto $input): CategoryDeleteOutputDto
    {
        $isCategoryDeleted = $this->repository->delete($input->id);
        return new CategoryDeleteOutputDto(
            success: $isCategoryDeleted
        );
    }
}
