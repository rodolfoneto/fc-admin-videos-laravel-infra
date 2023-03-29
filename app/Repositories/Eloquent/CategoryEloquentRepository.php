<?php

namespace App\Repositories\Eloquent;

use App\Models\Category as ModelCategory;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Entity\Category;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;

class CategoryEloquentRepository implements CategoryRepositoryInterface
{

    protected ModelCategory $model;

    public function __construct(ModelCategory $model)
    {
        $this->model = $model;
    }

    public function insert(Category $category): Category
    {
        $category = $this->model->create([
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
            'is_active' => $category->isActive,
            'created_at' => $category->createdAt(),
        ]);
        return $this->toCategory($category);
    }

    public function update(Category $category): Category
    {
        if (!$modelCategory = $this->model->find($category->id())) {
            throw new NotFoundException();
        }

        $modelCategory->update([
            'name' => $category->name,
            'description' => $category->description,
            'is_active' =>$category->isActive,
        ]);

        $modelCategory->refresh();

        return $this->toCategory($modelCategory);
    }

    public function delete(string $uuid): bool
    {
        if (!$modelCategory = $this->model->find($uuid)) {
            throw new NotFoundException('Entity not founded');
        }
        return $modelCategory->delete();
    }

    public function findById(string $uuid): Category
    {
        if(!$category = $this->model->find($uuid)) {
            throw new NotFoundException('Entity not founded');
        }
        return $this->toCategory($category);
    }

    public function findAll(string $filter = '', $order = 'DESC'): array
    {
        return $this->model
            ->where(function($query) use ($filter) {
                if($filter)
                        $query->where('name', 'LIKE', "%{$filter}%");
                })
            ->orderBy('id', $order)
            ->get()
            ->toArray();
    }

    public function paginate(string $filter = '', $order = 'DESC', $page = '1', $totalPerPage = 15): PaginationInterface
    {
        $query = $this->model;
        if ($filter) {
            $query->where('name', 'LIKE', "%{$filter}%");
        }
        $query->orderBy('id', $order);
        $paginator = $query->paginate($totalPerPage);
        return new PaginationPresenter($paginator);
    }

    private function toCategory(object $input): Category
    {
        $entity = new Category(
            id: $input->id,
            name: $input->name,
            description: $input->description,
            createdAt: $input->create_at ?? '',
            updatedAt: $input->updated_at ?? '',
        );
        ((bool) $input->is_active) ? $entity->activate() : $entity->deactivate();
        return $entity;
    }
}
