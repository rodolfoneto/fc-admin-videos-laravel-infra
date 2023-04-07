<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\CastMember;

interface CastMemberRepositoryInterface
{
    public function insert(CastMember $castMember): CastMember;
    public function update(CastMember $castMember): CastMember;
    public function delete(string $uuid): bool;

    public function findById(string $uuid): CastMember;
    public function findAll(string $filter = '', $order = 'DESC'): array;
    public function paginate(string $filter = '', $order = 'DESC', $page = '1', $totalPerPage = 15): PaginationInterface;
}
