<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\ValueObject\Uuid;
use Core\UseCase\Video\Update\Dto\{
    InputDto,
    OutputDto
};
use Core\UseCase\Video\Update\UseCase;
use Mockery;

class UpdateVideoUseCaseUnitTest extends BaseVideoUseCaseTestBase
{
    public function test_execute_input_output()
    {
        $this->createUseCase();
        $useCase = $this->useCase;
        $output = $useCase->execute(
            input: $this->createMockInputDto(),
        );
        $this->assertInstanceOf(OutputDto::class, $output);
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
        return Mockery::mock(InputDto::class, [
            Uuid::random(),
            'New Video Title',
            'Description with new video',
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
        return 'update';
    }

    protected function getNameClassUseCase(): string
    {
        return UseCase::class;
    }
}
