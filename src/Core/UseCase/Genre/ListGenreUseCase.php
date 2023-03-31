<?php

namespace Core\UseCase\Genre;

use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\{
    GenreOutputDto,
    GenreInputDto,
};

class ListGenreUseCase
{
    public function __construct(
        protected GenreRepositoryInterface $repository
    ) {

    }

    public function execute(GenreInputDto $input): GenreOutputDto
    {
        $genre = $this->repository->findById($input->id);
        return new GenreOutputDto(
            id: $genre->id(),
            name: $genre->name,
            is_active: $genre->isActive,
            created_at: $genre->createdAt(),
        );
    }
}
