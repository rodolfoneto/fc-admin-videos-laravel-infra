<?php

namespace Core\Domain\Repository;


interface GenreRepositoryInterface extends EntityRepositoryInterface
{
    public function getIdsListIds(array $genresId): array;
}
