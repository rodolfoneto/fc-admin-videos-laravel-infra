<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Enum\Rating;
use Core\UseCase\Video\Create\Dto\{
    CreateVideoInputDto,
    CreateVideoOutputDto
};
use Core\UseCase\Video\Create\CreateVideoUseCase;
use Mockery;

class CreateVideoUseCaseUnitTest extends BaseVideoUseCaseUnitTest
{
    public function test_execute_input_output()
    {
        $this->createUseCase();
        $useCase = $this->useCase;
        $output = $useCase->execute(
            input: $this->createMockInputDto(),
        );
        $this->assertInstanceOf(CreateVideoOutputDto::class, $output);
    }

    protected function createMockInputDto(
        array $categoriesId = [],
        array $genresId = [],
        array $castMembersId = [],
        array $thumbFile = null,
        array $thumbHalfFile = null,
        array $bannerFile = null,
        array $trailerFile = null,
        array $videoFile = null,
    ) {
        return Mockery::mock(CreateVideoInputDto::class, [
            'New Video Title',
            'Description with new video',
            2023,
            12,
            true,
            Rating::RATE10,
            $categoriesId,
            $genresId,
            $castMembersId,
            $thumbFile,
            $thumbHalfFile,
            $bannerFile,
            $trailerFile,
            $videoFile,
        ]);
    }

    protected function getActionRepository(): string
    {
        return 'insert';
    }

    protected function getNameClassUseCase(): string
    {
        return CreateVideoUseCase::class;
    }
}
