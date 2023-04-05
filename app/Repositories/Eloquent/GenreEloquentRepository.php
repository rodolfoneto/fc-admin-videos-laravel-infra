<?php

namespace App\Repositories\Eloquent;

use App\Models\Genre as GenreModel;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Entity\Genre;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\Interface\TransactionInterface;
use Core\Domain\Repository\{
    GenreRepositoryInterface,
    PaginationInterface,
};

class GenreEloquentRepository implements GenreRepositoryInterface
{

    protected GenreModel $model;

    public function __construct(GenreModel $model, TransactionInterface $transaction)
    {
        $this->model = $model;
    }

    public function insert(Genre $genre): Genre
    {
        try {
            $genreDb = $this->model->create([
                'id' => $genre->id(),
                'name' => $genre->name,
                'is_active' => $genre->isActive,
                'created_at' => $genre->createdAt(),
            ]);

            if(count($genre->categoriesId) > 0) {
                $genreDb->categories()->sync($genre->categoriesId);
            }

            return $this->toGenre($genreDb);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update(Genre $genre): Genre
    {
        if (!$modelGenre = $this->model->find($genre->id())) {
            throw new NotFoundException();
        }

        $modelGenre->update([
            'name' => $genre->name,
        ]);

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
