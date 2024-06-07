<?php

namespace Tests\Feature\Core\UseCase\Video;

use App\Models\Video;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\Delete\DeleteVideoUseCase;
use Core\UseCase\Video\Delete\Dto\DeleteVideoInputDto;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteVideoUseCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete()
    {
        $videoDb = Video::factory()->create();

        $useCase = new DeleteVideoUseCase(
            $this->app->make(VideoRepositoryInterface::class),
        );

        $response = $useCase->execute(new DeleteVideoInputDto(
            id: $videoDb->id,
        ));

        $this->assertTrue($response->success);
    }

    public function test_delete_not_found()
    {
        $useCase = new DeleteVideoUseCase(
            $this->app->make(VideoRepositoryInterface::class),
        );

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Video not found");
        
        $useCase->execute(new DeleteVideoInputDto(
            id: 'not_found',
        ));
    }
}
