<?php

namespace Tests\Feature\Core\UseCase\Video;

use App\Models\Video;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\Get\Dto\GetVideoInputDto;
use Core\UseCase\Video\Get\GetVideoUseCase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetVideoUseCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_list(): void
    {
        $videoDb = Video::factory()->create();

        $useCase = new GetVideoUseCase(
            $this->app->make(VideoRepositoryInterface::class),
        );

        $inputDTO = new GetVideoInputDto(id: $videoDb->id);

        $response = $useCase->execute($inputDTO);

        $this->assertNotNull($response);
        $this->assertEquals($videoDb->id, $response->id);
    }

    public function test_not_found(): void
    {
        $useCase = new GetVideoUseCase(
            $this->app->make(VideoRepositoryInterface::class),
        );

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Video not found");
        $inputDTO = new GetVideoInputDto(id: 'FAKE_ID');

        $response = $useCase->execute($inputDTO);
    }
}
