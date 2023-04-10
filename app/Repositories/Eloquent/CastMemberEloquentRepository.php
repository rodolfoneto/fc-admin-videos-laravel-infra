<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Entity\CastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\PaginationInterface;
use App\Models\CastMember as Model;
use Core\Domain\ValueObject\Uuid;
use function PHPUnit\Framework\assertGreaterThanOrEqual;

class CastMemberEloquentRepository implements \Core\Domain\Repository\CastMemberRepositoryInterface
{

    public function __construct(
        protected Model $model,
    ) {
    }

    public function insert(CastMember $castMember): CastMember
    {
        $response = $this->model->create([
            'id' => $castMember->id(),
            'name' => $castMember->name,
            'type' => $castMember->type->value,
            'created_at' => $castMember->createdAt(),
        ]);
        return $this->toEntity($response);
    }

    public function update(CastMember $castMember): CastMember
    {
        if(!$castMemberDb = $this->model->find($castMember->id())) {
            throw new NotFoundException("CastMember {$castMember->id()} not founded");
        }
        $castMemberDb->update([
            'name' => $castMember->name,
            'type' => $castMember->type->value,
        ]);
        $castMemberDb->refresh();
        return $this->toEntity($castMemberDb);
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
        return $this->toEntity($model);
    }

    public function findAll(string $filter = '', $order = 'DESC'): array
    {
        return $this->model->where(function($query) use ($filter){
            if(!empty($filter)) {
                $query->where('name', 'LIKE', "%{$filter}%");
            }
        })->orderBy('name', $order)
            ->get()
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

    protected function toEntity(Model $input): CastMember
    {
        return new CastMember(
            name: $input->name,
            type: CastMemberType::from($input->type),
            id: new Uuid($input->id),
            createdAt: $input->created_at,
        );
    }
}
