<?php

namespace Core\UseCase\Category;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\DTO\Category\{
    CategoriesListInputDto,
    CategoriesListOutputDto
};

class ListCategoriesUseCase
{
    protected CategoryRepositoryInterface $repository;

    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(CategoriesListInputDto $input): CategoriesListOutputDto
    {
        $result = $this->repository->paginate(
            filter: $input->filter, 
            order: $input->order,
            page: $input->page,
            totalPerPage: $input->totalPerPage
        );
        $responseOutputDto = new CategoriesListOutputDto(
            items: $result->items(),
            total: $result->total(),
            last_page: $result->lastPage(),
            first_page: $result->firstPage(),
            current_page: $result->currentPage(),
            per_page: $result->perPage(),
            to: $result->to(),
            from: $result->from(),
        );
        return $responseOutputDto;
    }
}