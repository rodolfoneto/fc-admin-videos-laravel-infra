<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Entity\Video;
use Core\Domain\Enum\Rating;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\Delete\Dto\DeleteVideoInputDto;
use Core\UseCase\Video\Delete\Dto\DeleteVideoOutputDto;
use PHPUnit\Framework\TestCase;
use Core\UseCase\Video\Delete\DeleteVideoUseCase;
use Mockery;
use Ramsey\Uuid\Uuid;
use \Core\Domain\ValueObject\Uuid as VOUuid;

class DeleteVideoUseCaseUnitTest extends TestCase
{
    public function test_delete_video()
    {
        $uuid = Uuid::uuid4()->toString();
        $useCase = new DeleteVideoUseCase(repository: $this->createMockRepository(id: $uuid));
        $output = $useCase->execute(input: $this->createMockInputDto($uuid));
        $this->assertInstanceOf(DeleteVideoOutputDto::class, $output);
        $this->assertTrue($output->success);
    }

    protected function createMockRepository($id)
    {
        $repository = Mockery::mock(VideoRepositoryInterface::class);
        $repository->shouldReceive('findById')
            ->once()
            ->andReturn($this->createEntity($id));
        $repository->shouldReceive('delete')
            ->with($id)
            ->once()
            ->andReturn(true);
        return $repository;
    }

    protected function createMockInputDto(string $id)
    {
        return Mockery::mock(DeleteVideoInputDto::class, [$id]);
    }

    protected function createEntity($id)
    {
        return new Video(
            title: "title Video",
            description: "Description of video",
            yearLaunched: 2013,
            duration: 10,
            opened: true,
            rating: Rating::RATE12,
            id: new VOUuid($id)
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
