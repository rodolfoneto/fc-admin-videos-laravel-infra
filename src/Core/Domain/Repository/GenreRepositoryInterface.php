<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\Genre;

interface GenreRepositoryInterface
{
    public function insert(Genre $genre): Genre;
    public function update(Genre $genre): Genre;
    public function delete(string $uuid): bool;

    public function findById(string $uuid): Genre;
    public function findAll(string $filter = '', $order = 'DESC'): array;
    public function paginate(string $filter = '', $order = 'DESC', $page = '1', $totalPerPage = 15): PaginationInterface;
}
