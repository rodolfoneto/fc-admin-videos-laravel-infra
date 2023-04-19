<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Entity\CastMember;
use Core\Domain\Entity\Entity;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\PaginationInterface;
use App\Models\CastMember as Model;
use Core\Domain\ValueObject\Uuid;
use Core\Domain\Repository\CastMemberRepositoryInterface;

class CastMemberEloquentRepository implements CastMemberRepositoryInterface
{

    public function __construct(
        protected Model $model,
    ) {
    }

    public function insert(Entity $entity): CastMember
    {
        $response = $this->model->create([
            'id' => $entity->id(),
            'name' => $entity->name,
            'type' => $entity->type->value,
            'created_at' => $entity->createdAt(),
        ]);
        return $this->toCastMember($response);
    }

    public function update(Entity $entity): CastMember
    {
        if(!$castMemberDb = $this->model->find($entity->id())) {
            throw new NotFoundException("CastMember {$entity->id()} not founded");
        }
        $castMemberDb->update([
            'name' => $entity->name,
            'type' => $entity->type->value,
        ]);
        $castMemberDb->refresh();
        return $this->toCastMember($castMemberDb);
    }

    public function delete(string $uuid): bool
    {
        if(!$castMember = $this->model->find($uuid)) {
            throw new NotFoundException("CastMember {$uuid} not founded");
        }
        return $castMember->delete();
    }

    public function findById(string $uuid): CastMember
    {
        if(!$model = $this->model->find($uuid)) {
            throw new NotFoundException("CastMember {$uuid} not founded");
        }
        return $this->toCastMember($model);
    }

    public function findAll(string $filter = '', $order = 'DESC'): array
    {
        $query = $this->model;
        if(!empty($filter)) {
            $query = $query->where('name', 'LIKE', "%%filter%");
        }
        $query = $query->orderBy('name', $order);
        return $query
            ->get()
            ->toArray();
    }

    public function getIdsListIds(array $castMembersId = []): array
    {
        return $this->model
            ->whereIn('id', $castMembersId)
            ->pluck('id')
            ->toArray();
    }

    public function paginate(string $filter = '', $order = 'DESC', $page = '1', $totalPerPage = 15): PaginationInterface
    {
        $paginator = $this->model->where(function($query) use ($filter){
                if(!empty($filter)) {
                    $query->where('name', 'LIKE', "%{$filter}%");
                }
            })->orderBy('name', $order)
            ->paginate($totalPerPage);
        return new PaginationPresenter($paginator);
    }

    protected function toCastMember(Model $input): CastMember
    {
        return new CastMember(
            name: $input->name,
            type: CastMemberType::from($input->type),
            id: new Uuid($input->id),
            createdAt: $input->created_at,
        );
    }
}
