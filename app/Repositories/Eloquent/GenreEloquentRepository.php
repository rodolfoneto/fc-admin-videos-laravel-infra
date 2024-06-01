<?php

namespace App\Repositories\Eloquent;

use App\Models\Genre as GenreModel;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Entity\Entity;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Entity\Genre;
use Core\Domain\ValueObject\Uuid;
use Core\Domain\Repository\{
    GenreRepositoryInterface,
    PaginationInterface,
};

class GenreEloquentRepository implements GenreRepositoryInterface
{

    protected GenreModel $model;

    public function __construct(GenreModel $model)
    {
        $this->model = $model;
    }

    public function insert(Entity $entity): Genre
    {
        $genreDb = $this->model->create([
            'id' => $entity->id(),
            'name' => $entity->name,
            'is_active' => $entity->isActive,
            'created_at' => $entity->createdAt(),
        ]);

        if(count($entity->categoriesId) > 0) {
            $genreDb->categories()->sync($entity->categoriesId);
        }

        return $this->toGenre($genreDb);
    }

    public function update(Entity $entity): Genre
    {
        if (!$modelGenre = $this->model->find($entity->id())) {
            throw new NotFoundException();
        }

        $modelGenre->update([
            'name' => $entity->name,
        ]);

        if(count($entity->categoriesId) > 0) {
            $modelGenre->categories()->sync($entity->categoriesId);
        }

        $modelGenre->refresh();

        return $this->toGenre($modelGenre);
    }

    public function delete(string $uuid): bool
    {
        if (!$modelGenre = $this->model->find($uuid)) {
            throw new NotFoundException('Entity not founded');
        }
        return $modelGenre->delete();
    }

    public function findById(string $uuid): Genre
    {
        if(!$genre = $this->model->find($uuid)) {
            throw new NotFoundException('Entity not founded');
        }
        return $this->toGenre($genre);
    }

    public function findAll(string $filter = '', $order = 'DESC'): array
    {
        $genres = $this->model
            ->where(function($query) use ($filter) {
                if($filter)
                        $query->where('name', 'LIKE', "%{$filter}%");
                })
            ->orderBy('name', $order)
            ->get();
        $result = array();
        foreach ($genres as $genre) {
            array_push($result, $this->toGenre($genre));
        }
        return $result;
    }

    public function paginate(string $filter = '', $order = 'DESC', $page = '1', $totalPerPage = 15): PaginationInterface
    {
        $paginator = $this->model
            ->where(function($query) use ($filter) {
                if ($filter) {
                    $query->where('name', 'LIKE', "%{$filter}%");
                }
            })
            ->orderBy('id', $order)
            ->paginate($totalPerPage);
        return new PaginationPresenter($paginator);
    }

    public function getIdsListIds(array $genresId = []): array
    {
        return $this->model
            ->whereIn('id', $genresId)
            ->pluck('id')
            ->toArray();
    }

    private function toGenre(object $input): Genre
    {
        $entity = new Genre(
            name: $input->name,
            id: new Uuid($input->id),
            createdAt: new \DateTime($input->created_at),
        );
        if(count($input->categories) > 0 ) {
            foreach ($input->categories as $category) {
                $entity->addCategory($category->id);
            }
        }
        ((bool) $input->is_active) ? $entity->activate() : $entity->deactivate();
        return $entity;
    }
}
