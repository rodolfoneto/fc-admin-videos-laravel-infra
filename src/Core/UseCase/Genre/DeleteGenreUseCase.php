<?php

namespace Core\UseCase\Genre;

use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\{
    GenreDeleteInputDto,
    GenreDeleteOutputDto
};

class DeleteGenreUseCase
{
    public function __construct(
        protected GenreRepositoryInterface $repository
    ) {}

    public function execute(GenreDeleteInputDto $input): GenreDeleteOutputDto
    {
        if(!$genre = $this->repository->findById(uuid: $input->id)) {
            throw new NotFoundException('Genre not found');
        }
        $response = $this->repository->delete($genre->id());
        return new GenreDeleteOutputDto(
            success: $response,
        );
    }
}
