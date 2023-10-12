<?php

namespace App\Repositories\Eloquent;

use App\Enums\ImageTypes;
use App\Enums\MediaTypes;
use Core\Domain\Entity\Entity;
use Core\Domain\Entity\Video;
use App\Models\Video as VideoModel;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Enum\MediaStatus;
use Core\Domain\Enum\Rating;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\{
    VideoRepositoryInterface,
    PaginationInterface
};
use Core\Domain\ValueObject\Media;
use Core\Domain\ValueObject\Uuid;
use Illuminate\Database\Eloquent\Model;

class VideoEloquentRepository implements VideoRepositoryInterface
{

    public function __construct(
        protected VideoModel $repository,
    ) {}

    public function insert(Entity $entity): Entity
    {
        $videoDb = $this->repository->create([
            'id' => $entity->id(),
            'title' => $entity->title,
            'description' => $entity->description,
            'year_launched' => $entity->yearLaunched,
            'opened' => $entity->opened,
            'rating' => $entity->rating->value,
            'duration' => $entity->duration,
        ]);

        $this->syncRelationships($videoDb, $entity);

        return $this->convertObjectToEntity($videoDb);
    }

    public function update(Entity $entity): Entity
    {
        if (!$entityDb = $this->repository->find($entity->id())) {
            throw new NotFoundException('Video not founded');
        }
        $entityDb->update([
            'title'         => $entity->title,
            'description'   => $entity->description,
            'year_launched' => $entity->yearLaunched,
            'rating'        => $entity->rating->value,
            'duration'      => $entity->duration,
            'opened'        => $entity->opened,
        ]);

        $this->syncRelationships($entityDb, $entity);

        return $this->convertObjectToEntity($entityDb);
    }

    public function delete(string $uuid): bool
    {
        if (!$entityDb = $this->repository->find($uuid)) {
            throw new NotFoundException('Video not found', 404);
        }
        $entityDb->delete();
        return true;
    }

    public function findById(string $uuid): Entity
    {
        if (!$entityDb = $this->repository->find($uuid)) {
            throw new NotFoundException('Video not found', 404);
        }
        return $this->convertObjectToEntity($entityDb);
    }

    public function findAll(string $filter = '', $order = 'DESC'): array
    {
        $videosDb = $this->repository
            ->where(function($query) use ($filter) {
                if($filter)
                        $query->where('title', 'LIKE', "%{$filter}%");
                })
            ->orderBy('title', $order)
            ->get();
        $result = array();
        foreach ($videosDb as $item) {
            array_push($result, $this->convertObjectToEntity($item));
        }
        return $result;
    }

    public function paginate(string $filter = '', $order = 'DESC', $page = '1', $totalPerPage = 15): PaginationInterface
    {
        $paginationVideosDb = $this->repository
            ->where(function($query) use ($filter) {
                if($filter)
                        $query->where('title', 'LIKE', "%{$filter}%");
                })
            ->orderBy('title', $order)
            ->paginate($totalPerPage, ['*'], 'page', $page);
        
        return new PaginationPresenter($paginationVideosDb);
    }

    public function updateMedia(Video $entity): Video
    {
        if (!$entityDb = $this->repository->find($entity->id())) {
            throw new NotFoundException('Video not found', 404);
        }

        if ($trailer = $entity->trailerFile()) {
            $action = $entityDb->trailer()->first() ? 'update' : 'create';

            $entityDb->trailer()->{$action}([
                'file_path'    => $trailer->path(),
                'media_status' => $trailer->mediaStatus()->value,
                'encoded_path' => $trailer->encodedPath(),
                'type'         => MediaTypes::TRAILER->value,
            ]);
        }

        if($banner = $entity->bannerFile()) {
            $action = $entityDb->banner()->first() ? 'update' : 'create';
            $entityDb->banner()->{$action}([
                'file_path' => $banner->path(),
                'type'      => ImageTypes::BANNER->value,
            ]);
        }

        return $this->convertObjectToEntity($entityDb);
    }

    protected function syncRelationships(VideoModel $model, Entity $entity)
    {
        $model->categories()->sync($entity->categoriesId);
        $model->genres()->sync($entity->genresId);
        $model->castMembers()->sync($entity->castMembersId);
    }

    protected function convertObjectToEntity(VideoModel $object): Entity
    {
        $entity = new Video(
            title: $object->title,
            description: $object->description,
            yearLaunched: (int) $object->year_launched,
            duration: (int) $object->duration,
            opened: $object->opened,
            rating: Rating::from($object->rating),
            id: new Uuid($object->id),
            publish: (bool) $object->publish,
            createdAt: $object->created_at,
        );

        foreach ($object->categories as $item) {
            $entity->addCategoryId($item->id);
        }
        foreach ($object->genres as $item) {
            $entity->addGenreId($item->id);
        }
        foreach ($object->castMembers as $item) {
            $entity->addCastMemberId($item->id);
        }
        
        if ($trailerFile = $object->trailer) {
            $entity->setTrailerFile(new Media(
                path: $trailerFile->file_path,
                mediaStatus: MediaStatus::from($trailerFile->media_status),
                encodedPath: $trailerFile->encoded_path,
            ));
        }

        return $entity;
    }
}
