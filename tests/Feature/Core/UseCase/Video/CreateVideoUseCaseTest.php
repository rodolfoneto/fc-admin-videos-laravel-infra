<?php

namespace Tests\Feature\Core\UseCase\Video;

use Core\Domain\Enum\Rating;
use Core\UseCase\Video\Create\CreateVideoUseCase;
use Core\UseCase\Video\Create\Dto\CreateVideoInputDto;

class CreateVideoUseCaseTest extends BaseVideoUseCase
{
    protected function getUseCaseClass(): string
    {
        return CreateVideoUseCase::class;
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
    ): object
    {
        $inputDTO = new CreateVideoInputDto(
            title:        'Test',
            description:  'Descrição de Test',
            yearLaunched:  2022,
            rating:        Rating::RATE12,
            duration:      10,
            opened:        true,
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
