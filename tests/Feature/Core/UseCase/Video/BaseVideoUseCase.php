<?php

namespace Tests\Feature\Core\UseCase\Video;

use App\Models\{
    CastMember,
    Category,
    Genre,
};
use Core\Domain\Repository\{
    CastMemberRepositoryInterface,
    CategoryRepositoryInterface,
    GenreRepositoryInterface,
    VideoRepositoryInterface,
};
use Core\UseCase\Interface\{
    TransactionInterface
};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Stubs\UploadFilesStub;
use Tests\Stubs\VideoEventStub;
use Tests\TestCase;

abstract class BaseVideoUseCase extends TestCase
{
    use RefreshDatabase;

    protected abstract function getUseCaseClass(): string;
    protected abstract function getInputDTO(
        array $categories    = [],
        array $genres        = [],
        array $castMembers   = [],
        ?array $videoFile     = null,
        ?array $trailerFile   = null,
        ?array $bannerFile    = null,
        ?array $thumbFile     = null,
        ?array $thumbHalfFile = null,
    ): object;

    #[DataProvider('dataProviderBelongsTo')]
    public function test_storage(
        $qtyCategories,
        $qtyGenres,
        $qtyCastMembers,
        $withMediaVideo = false,
        $withTrailer = false,
        $withBanner = false,
        $withThumb = false,
        $withThumbHalf = false,
    ):void {
        $useCase = new ($this->getUseCaseClass()) (
            $this->app->make(VideoRepositoryInterface::class),
            $this->app->make(TransactionInterface::class),
            new UploadFilesStub(),// //Stub to simulate the file storage
            new VideoEventStub(),//Stub to simulate the file storage
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

        $inputDTO = $this->getInputDTO(
            categories:    $categoriesIds,
            genres:        $genresIds,
            castMembers:   $castMembersIds,
            videoFile:     $withMediaVideo    ? $file : null,
            trailerFile:   $withTrailer       ? $file : null,
            bannerFile:    $withBanner        ? $file : null,
            thumbFile:     $withThumb         ? $file : null,
            thumbHalfFile: $withThumbHalf     ? $file : null,
        );

        $response = $useCase->execute($inputDTO);
        $this->assertEquals($inputDTO->title, $response->title);
        $this->assertEquals($inputDTO->description, $response->description);
        // $this->assertEquals($inputDTO->yearLaunched, $response->yearLaunched);
        // $this->assertEquals($inputDTO->rating, $response->rating);
        // $this->assertEquals($inputDTO->duration, $response->duration);
        // $this->assertEquals($inputDTO->opened, $response->opened);

        $this->assertEquals($inputDTO->categories, $response->categories);
        $this->assertEquals($inputDTO->genres, $response->genres);
        $this->assertEquals($inputDTO->castMembers, $response->castMembers);
        $this->assertEqualsCanonicalizing($inputDTO->categories, $response->categories);
        $this->assertEqualsCanonicalizing($inputDTO->genres, $response->genres);
        $this->assertEqualsCanonicalizing($inputDTO->castMembers, $response->castMembers);

        $this->assertTrue($withMediaVideo ? $response->videoFile     !== null : $response->videoFile     === null);
        $this->assertTrue($withTrailer    ? $response->trailerFile   !== null : $response->trailerFile   === null);
        $this->assertTrue($withBanner     ? $response->bannerFile    !== null : $response->bannerFile    === null);
        $this->assertTrue($withThumb      ? $response->thumbFile     !== null : $response->thumbFile     === null);
        $this->assertTrue($withThumbHalf  ? $response->thumbHalfFile !== null : $response->thumbHalfFile === null);
    }

    public static function dataProviderBelongsTo(): array
    {
        return [
            'Test With All Ids and media video              ' => [3, 3, 3, true, true, false, false, false],
            'Test With Categories And Without Files         ' => [3, 0, 0, false, false, false, false, false],
            'Test With Genres With Banner                   ' => [0, 3, 0, false, false, true, false, false],
            'Test With CastMenbers                          ' => [0, 0, 3, false, false, false, false, false],
            'Test With Categories and Genres and All medias ' => [3, 3, 0, true, true, true, true, true],
            'Test With Genres And CastMenbers and All medias' => [0, 3, 10, true, true, true, true, true],
            'Test Without Ids and All medias                ' => [0, 0, 0, true, true, true, true, true],
        ];
    }
}