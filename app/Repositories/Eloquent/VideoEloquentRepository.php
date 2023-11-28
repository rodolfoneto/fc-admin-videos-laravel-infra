<?php

namespace App\Repositories\Eloquent;

use App\Enums\ImageTypes;
use App\Enums\MediaTypes;
use Core\Domain\Entity\Entity;
use Core\Domain\Entity\Video;
use App\Models\Video as VideoModel;
use App\Repositories\Eloquent\Trait\VideoTrait;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Builder\Video\UpdateVideoBuilder;
use Core\Domain\Enum\MediaStatus;
use Core\Domain\Enum\Rating;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\{
    VideoRepositoryInterface,
    PaginationInterface
};
use Core\Domain\ValueObject\{
    Uuid,
    Media as ValueObjectMedia,
    Image as ValueObjectImage,
};
use Illuminate\Database\Eloquent\Model;

class VideoEloquentRepository implements VideoRepositoryInterface
{
    use VideoTrait;

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

        $this->updateMediaVideo($entity, $entityDb);
        $this->updateMediaTrailer($entity, $entityDb);
        $this->updateImageBanner($entity, $entityDb);
        $this->updateImageThumb($entity, $entityDb);
        $this->updateImageThumbHalf($entity, $entityDb);
        
        return $this->convertObjectToEntity($entityDb);
    }

    protected function syncRelationships(VideoModel $model, Entity $entity)
    {
        $model->categories()->sync($entity->categoriesId);
        $model->genres()->sync($entity->genresId);
        $model->castMembers()->sync($entity->castMembersId);
    }

    protected function convertObjectToEntity(VideoModel $model): Entity
    {
        $entity = new Video(
            title: $model->title,
            description: $model->description,
            yearLaunched: (int) $model->year_launched,
            duration: (int) $model->duration,
            opened: $model->opened,
            rating: Rating::from($model->rating),
            id: new Uuid($model->id),
            publish: (bool) $model->publish,
            createdAt: $model->created_at,
        );

        foreach ($model->categories as $item) {
            $entity->addCategoryId($item->id);
        }

        foreach ($model->genres as $item) {
            $entity->addGenreId($item->id);
        }

        foreach ($model->castMembers as $item) {
            $entity->addCastMemberId($item->id);
        }

        $builder = (new UpdateVideoBuilder)
                        ->setEntity($entity);
        
        if ($trailerFile = $model->trailer) {
            $builder->addTrailer(
                path: $trailerFile->file_path,
                mediaStatus: MediaStatus::from($trailerFile->media_status),
                encodedPath: $trailerFile->encoded_path,
            );
        }

        if ($mediaFile = $model->media) {
            $builder->addMediaVideo(
                path: $mediaFile->file_path,
                mediaStatus: MediaStatus::from($mediaFile->media_status),
                encodedPath: $mediaFile->encoded_path,
            );
        }

        if ($banner = $model->banner) {
            $builder->addBanner($banner->file_path);
        }

        if ($thumb = $model->thumb) {
            $builder->addThumb($thumb->file_path);
        }

        if ($thumbHalf = $model->thumbHalf) {
            $builder->addThumbHalf($thumbHalf->file_path);
        }

        if($banner = $model->banner) {
            $builder->addBanner($banner->file_path);
        }

        return $builder->getEntity();
    }
}
