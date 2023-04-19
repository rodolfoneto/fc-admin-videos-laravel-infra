<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\Entity;

interface EntityRepositoryInterface
{
    public function insert(Entity $entity): Entity;
    public function update(Entity $entity): Entity;
    public function delete(string $uuid): bool;

    public function findById(string $uuid): Entity;
    public function findAll(string $filter = '', $order = 'DESC'): array;
    public function paginate(
        string $filter = '',
        $order = 'DESC',
        $page = '1',
        $totalPerPage = 15): PaginationInterface;
}
