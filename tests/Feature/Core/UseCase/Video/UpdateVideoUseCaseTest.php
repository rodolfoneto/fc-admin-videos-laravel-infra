<?php

namespace Tests\Feature\Core\UseCase\Video;

use App\Models\Video;
use Core\UseCase\Video\Update\Dto\InputDto as UpdateVideoInputDto;
use Core\UseCase\Video\Update\UseCase as UpdateVideoUseCase;

class UpdateVideoUseCaseTest extends BaseVideoUseCase
{
    protected function getUseCaseClass(): string
    {
        return UpdateVideoUseCase::class;
    }

    protected function getInputDTO(
        array $categories    = [],
        array $genres        = [],
        array $castMembers   = [],
        ?array $videoFile     = null,
        ?array $trailerFile   = null,
        ?array $bannerFile    = null,
        ?array $thumbFile     = null,
        ?array $thumbHalfFile = null,
    ): object {
        $videoDb = Video::factory()->create();
        $inputDTO = new UpdateVideoInputDto(
            id:           $videoDb->id,
            title:        'Test',
            description:  'Descrição de Test',
            categories:    $categories,
            genres:        $genres,
            castMembers:   $castMembers,
            videoFile:     $videoFile,
            trailerFile:   $trailerFile,
            bannerFile:    $bannerFile,
            thumbFile:     $thumbFile,
            thumbHalfFile: $thumbHalfFile,
        );
        return $inputDTO;
    }

}
