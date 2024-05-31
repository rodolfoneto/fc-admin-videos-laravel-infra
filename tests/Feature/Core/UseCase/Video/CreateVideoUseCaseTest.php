<?php

namespace Tests\Feature\Core\UseCase\Video;

use App\Models\{
    CastMember,
    Category,
    Genre,
};
use Core\Domain\Enum\Rating;
use Core\Domain\Repository\{
    CastMemberRepositoryInterface,
    CategoryRepositoryInterface,
    GenreRepositoryInterface,
    VideoRepositoryInterface,
};
use Core\UseCase\Interface\{
    FileStorageInterface,
    TransactionInterface
};
use Core\UseCase\Video\{
    Create\CreateVideoUseCase,
    Interfaces\VideoEventManagerInterface,
};
use Core\UseCase\Video\Create\Dto\CreateVideoInputDto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CreateVideoUseCaseTest extends TestCase
{
    use RefreshDatabase;


    #[DataProvider('dataProvider')]
    public function test_storage(
        $qtyCategories,
        $qtyGenres,
        $qtyCastMembers,
    ):void {
        $useCase = new CreateVideoUseCase(
            $this->app->make(VideoRepositoryInterface::class),
            $this->app->make(TransactionInterface::class),
            $this->app->make(FileStorageInterface::class),
            $this->app->make(VideoEventManagerInterface::class),
    
            $this->app->make(CategoryRepositoryInterface::class),
            $this->app->make(GenreRepositoryInterface::class),
            $this->app->make(CastMemberRepositoryInterface::class),
        );

        $categoriesIds  = Category::factory()->count($qtyCategories)->create()->pluck('id')->toArray();
        $genresIds      = Genre::factory()->count($qtyGenres)->create()->pluck('id')->toArray();
        $castMembersIds = CastMember::factory()->count($qtyCastMembers)->create()->pluck('id')->toArray();

        $fakeFile = UploadedFile::fake()->create('video.mp4', 1, 'video/mp4');
        $file = [
            'tmp_name' => $fakeFile->getPathname(),
            'name' => $fakeFile->getFilename(),
            'type' => $fakeFile->getMimeType(),
            'error' => $fakeFile->getError(),
        ];

        $inputDTO = new CreateVideoInputDto(
            title: 'teste',
            description: 'teste',
            yearLaunched: 2022,
            rating: Rating::RATE12,
            duration: 10,
            opened: true,
            categories: $categoriesIds,
            genres: $genresIds ,
            castMembers: $castMembersIds,
            videoFile: $file,
        );

        $response = $useCase->execute($inputDTO);
        $this->assertEquals($inputDTO->title, $response->title);
        $this->assertEquals($inputDTO->description, $response->description);
        $this->assertEquals($inputDTO->yearLaunched, $response->yearLaunched);
        $this->assertEquals($inputDTO->rating, $response->rating);
        $this->assertEquals($inputDTO->duration, $response->duration);
        $this->assertEquals($inputDTO->opened, $response->opened);

        $this->assertEquals($inputDTO->categories, $response->categories);
        $this->assertEquals($inputDTO->genres, $response->genres);
        $this->assertEquals($inputDTO->castMembers, $response->castMembers);

        $this->assertNotNull($response->videoFile);
        $this->assertNull($response->trailerFile);
        $this->assertNull($response->bannerFile);
        $this->assertNull($response->thumbFile);
        $this->assertNull($response->thumbHalfFile);
    }

    public static function dataProvider()
    {
        return [
            'test_categories_and_genrer_and_castmenbers' => [
                [
                    'categories' => 3,
                    'genres' => 3,
                    'castMembers' => 3,
                ],
            ]
        ];
    }
}
