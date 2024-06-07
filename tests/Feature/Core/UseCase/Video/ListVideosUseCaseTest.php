<?php

namespace Tests\Feature\Core\UseCase\Video;

use App\Models\Video;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\List\Dto\ListVideosInputDto;
use Core\UseCase\Video\List\ListVideosUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ListVideosUseCaseTest extends TestCase
{

    use RefreshDatabase;
    
    public function test_list_input_default(): void {
        Video::factory()->count(30)->create();

        $useCase = new ListVideosUseCase(
            $this->app->make(VideoRepositoryInterface::class),
        );

        $inputDTO = new ListVideosInputDto();

        $response = $useCase->execute($inputDTO);

        $this->assertCount(15, $response->items);
        $this->assertEquals(30, $response->total);
        $this->assertEquals(1, $response->current_page);
        $this->assertEquals(1, $response->first_page);
        $this->assertEquals(2, $response->last_page);
        $this->assertEquals(15, $response->per_page);
        $this->assertEquals(15, $response->from);
        $this->assertEquals(1, $response->to);
    }

    #[DataProvider('dataProviderListVideos')]
    public function test_list_input_defult_params(
        $totalVideos,
        $currentPage,
        $firstPage,
        $lastPage,
        $perPage,
        $from,
        $to,
    ): void {
        Video::factory()->count($totalVideos)->create();

        $useCase = new ListVideosUseCase(
            $this->app->make(VideoRepositoryInterface::class),
        );

        $inputDTO = new ListVideosInputDto(
            filter: '',
            order: 'DESC',
            page: $currentPage,
            totalPerPage: $perPage,
        );

        $response = $useCase->execute($inputDTO);

        $this->assertCount($perPage < $totalVideos ? $perPage : $totalVideos, $response->items);
        $this->assertEquals($totalVideos, $response->total);
        $this->assertEquals($currentPage, $response->current_page);
        $this->assertEquals($firstPage, $response->first_page);
        $this->assertEquals($lastPage, $response->last_page);
        $this->assertEquals($perPage, $response->per_page);
        $this->assertEquals($from, $response->from);
        $this->assertEquals($to, $response->to);
    }

    public static function dataProviderListVideos(): array
    {
        return [
            'Total videos 10, all defult' => [10, 1, 1, 1, 15, 10, 1],
            'Total 30 Videos, Pagina 1'   => [30, 1, 1, 2, 15, 15, 1],
            'Total 30 Videos Pagina 2'    => [30, 2, 16, 2, 15, 30, 16],
        ];
    }
}
