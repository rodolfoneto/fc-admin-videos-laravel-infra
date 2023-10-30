<?php

namespace App\Repositories\Eloquent\Trait;

use App\Enums\ImageTypes;
use App\Enums\MediaTypes;
use App\Models\Video as ModelsVideo;
use Core\Domain\Entity\Video;

trait VideoTrait
{

    public function updateMediaVideo(Video $entity, ModelsVideo $entityDb): void
    {
        if ($video = $entity->videoFile()) {
            $action = $entityDb->media()->first() ? 'update' : 'create';

            $entityDb->media()->{$action}([
                'file_path'    => $video->path(),
                'media_status' => $video->mediaStatus()->value,
                'encoded_path' => $video->encodedPath(),
                'type'         => MediaTypes::VIDEO->value,
            ]);
        }
    }

    public function updateMediaTrailer(Video $entity, ModelsVideo $entityDb): void
    {
        if ($trailer = $entity->trailerFile()) {
            $action = $entityDb->trailer()->first() ? 'update' : 'create';

            $entityDb->trailer()->{$action}([
                'file_path'    => $trailer->path(),
                'media_status' => $trailer->mediaStatus()->value,
                'encoded_path' => $trailer->encodedPath(),
                'type'         => MediaTypes::TRAILER->value,
            ]);
        }
    }

    public function updateImageBanner(Video $entity, ModelsVideo $entityDb): void
    {
        if($banner = $entity->bannerFile()) {
            $action = $entityDb->banner()->first() ? 'update' : 'create';
            $entityDb->banner()->{$action}([
                'file_path' => $banner->path(),
                'type'      => ImageTypes::BANNER->value,
            ]);
        }
    }
}