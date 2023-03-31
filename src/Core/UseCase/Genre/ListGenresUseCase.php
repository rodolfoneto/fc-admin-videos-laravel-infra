<?php

namespace Core\UseCase\Genre;

use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\UseCase\DTO\Genre\{
    GenresListOutputDto,
    GenresListInputDto,
};

class ListGenresUseCase
{
    public function __construct(
        protected GenreRepositoryInterface $repository
    ) {

    }

    public function execute(GenresListInputDto $input): GenresListOutputDto
    {
        $genresPaginated = $this->repository->paginate(
            filter: $input->filter,
            order: $input->order,
            page: $input->page,
            totalPerPage: $input->totalPerPage,
        );
        return new GenresListOutputDto(
            items: $genresPaginated->items(),
            total: $genresPaginated->total(),
            last_page: $genresPaginated->lastPage(),
            first_page: $genresPaginated->firstPage(),
            current_page: $genresPaginated->currentPage(),
            per_page: $genresPaginated->perPage(),
            to: $genresPaginated->to(),
            from: $genresPaginated->from(),
        );
    }
}
