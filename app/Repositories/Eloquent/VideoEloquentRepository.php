<?php

namespace App\Repositories\Eloquent;

use Core\Domain\Entity\Entity;
use Core\Domain\Entity\Video;
use App\Models\Video as VideoModel;
use Core\Domain\Repository\{
    VideoRepositoryInterface,
    PaginationInterface
};

class VideoEloquentRepository implements VideoRepositoryInterface
{

    public function __construct(
        protected VideoModel $repository,
    ) {}

    public function insert(Entity $entity): Entity
    {
        // TODO: Implement insert() method.
    }

    public function update(Entity $entity): Entity
    {
        // TODO: Implement update() method.
    }

    public function delete(string $uuid): bool
    {
        // TODO: Implement delete() method.
    }

    public function findById(string $uuid): Entity
    {
        // TODO: Implement findById() method.
    }

    public function findAll(string $filter = '', $order = 'DESC'): array
    {
        // TODO: Implement findAll() method.
    }

    public function paginate(string $filter = '', $order = 'DESC', $page = '1', $totalPerPage = 15): PaginationInterface
    {
        // TODO: Implement paginate() method.
    }

    public function updateMedia(Video $entity): Video
    {
        // TODO: Implement updateMedia() method.
    }
}
