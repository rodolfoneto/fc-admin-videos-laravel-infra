<?php

namespace Tests\Feature\App\Repositories;

use App\Enums\ImageTypes;
use App\Enums\MediaTypes;
use App\Models\{
    CastMember,
    Category,
    Genre,
    Media,
    Video as Model,
};
use App\Repositories\Eloquent\VideoEloquentRepository;
use Core\Domain\Entity\Video as VideoEntity;
use Core\Domain\Enum\MediaStatus;
use Core\Domain\Enum\Rating;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\Domain\ValueObject\{
    Media as ValueObjectMedia,
    Image as ValueObjectImage,
    Uuid,
};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VideoEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new VideoEloquentRepository(new Model());
    }

    public function test_instance_video_eloquent_repository()
    {
        $this->assertInstanceOf(
            VideoRepositoryInterface::class,
            $this->repository
        );
    }

    public function test_insert()
    {
        $video = new VideoEntity(
            title: 'Video 001',
            description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas eget',
            yearLaunched: 2026,
            duration: 5,
            rating: Rating::RATE10,
            opened: true,
        );

        $this->repository->insert($video);

        $this->assertDatabaseHas('videos', [
            'id' => $video->id(),
            'title' => 'Video 001',
            'year_launched' => 2026,
            'duration' => 5,
            'rating' => Rating::RATE10->value,
        ]);
    }

    public function test_insert_with_relationships()
    {
        $categories  = Category::factory()->count(4)->create();
        $genres      = Genre::factory()->count(4)->create();
        $castMembers = CastMember::factory()->count(4)->create();

        $video = new VideoEntity(
            title: 'Video 001',
            description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas eget',
            yearLaunched: 2026,
            duration: 5,
            rating: Rating::RATE10,
            opened: true,
        );

        foreach ($categories as $item) {
            $video->addCategoryId($item->id);
        }
        
        foreach ($genres as $item) {
            $video->addGenreId($item->id);
        }

        foreach ($castMembers as $item) {
            $video->addCastMemberId($item->id);
        }

        $entityInDb = $this->repository->insert($video);

        $this->assertDatabaseHas('videos', [
            'id' => $video->id(),
            'title' => 'Video 001',
            'year_launched' => 2026,
            'duration' => 5,
            'rating' => Rating::RATE10->value,
        ]);

        $this->assertDatabaseCount('category_video', 4);
        $this->assertDatabaseCount('genre_video', 4);
        $this->assertDatabaseCount('cast_member_video', 4);

        $this->assertEquals($categories->pluck('id')->toArray(), $entityInDb->categoriesId);
        $this->assertEquals($genres->pluck('id')->toArray(), $entityInDb->genresId);
        $this->assertEquals($castMembers->pluck('id')->toArray(), $entityInDb->castMembersId);
    }

    public function test_not_found_video()
    {
        $this->expectException(NotFoundException::class);
        $this->repository->findById('INVALID_ID');
    }

    public function test_find_by_id()
    {
        $videoDb = Model::factory()->create();
        $entity = $this->repository->findById($videoDb->id);

        $this->assertInstanceOf(VideoEntity::class, $entity);
        $this->assertEquals($videoDb->id, $entity->id());
        $this->assertEquals($videoDb->title, $entity->title);
    }

    public function test_find_all()
    {
        $videos = Model::factory()->count(10)->create();

        $response = $this->repository->findAll();
        $this->assertCount(10, $response);
    }

    public function test_find_all_with_filters()
    {
        $videos = Model::factory()->count(30)->create();
        $videos = Model::factory()->count(8)->create(['title' => 'Title To Search']);

        $response = $this->repository->findAll(filter: 'Title To Search');
        $this->assertCount(8, $response);
        $this->assertDatabaseCount('videos', 38);
    }

    public function test_find_all_with_order_asc()
    {
        Model::factory()->count(30)->create();
        Model::factory()->create(['title' => 'AAAAAAAAAA']);
        Model::factory()->create(['title' => 'Zzz Z Z Z Z Z Z Z']);

        $response = $this->repository->findAll(order: "ASC");
        $this->assertCount(32, $response);
        $this->assertEquals('AAAAAAAAAA', $response[0]->title);
        $this->assertEquals('Zzz Z Z Z Z Z Z Z', $response[31]->title);
    }

    public function test_find_all_with_filters_and_order()
    {
        $videos = Model::factory()->count(30)->create();
        $videos = Model::factory()->count(8)->create(['title' => 'Title To Search']);
        Model::factory()->create(['title' => 'AAAAAAAAAA Title To Search']);
        Model::factory()->create(['title' => 'ZZZZZZZZZZ Title To Search']);

        $response = $this->repository->findAll(filter: 'Title To Search', order: "ASC");
        $this->assertCount(10, $response);
        $this->assertEquals('AAAAAAAAAA Title To Search', $response[0]->title);
        $this->assertEquals('ZZZZZZZZZZ Title To Search', $response[9]->title);
        $this->assertDatabaseCount('videos', 40);
    }

    /**
     * @dataProvider dataProviderPagination
     */
    public function test_pagination(
        int $qtdTotalItems,
        int $page,
        int $totalPerPage,
        int $qtdItems,
        int $lastPage,
    ) {
        Model::factory()->count($qtdTotalItems)->create();

        $response = $this->repository->paginate(
            page: $page,
            totalPerPage: $totalPerPage,
        );

        $this->assertCount($qtdItems, $response->items());
        $this->assertEquals($qtdTotalItems, $response->total());
        $this->assertEquals($page, $response->currentPage());
        $this->assertEquals($totalPerPage, $response->perPage());
        $this->assertEquals($lastPage, $response->lastPage());
    }

    public function dataProviderPagination(): array
    {
        return [
            [50, 1, 10, 10, 5],
            [45, 5, 10, 5, 5],
            [3, 1, 10, 3, 1],
            [0, 1, 15, 0, 1],
        ];
    }

    public function test_update_not_found_id()
    {
        $this->expectException(NotFoundException::class);

        $entity = new VideoEntity(
            title: 'AAAAAAAAAA Title To Search',
            description: 'AAAAAAAAAA Description To Search',
            yearLaunched: 2026,
            rating: Rating::L,
            duration: 100,
            opened: true,
        );

        $this->repository->update($entity);
    }

    public function test_update()
    {
        $categories  = Category::factory()->count(10)->create();
        $genres      = Genre::factory()->count(4)->create();
        $castMembers = CastMember::factory()->count(4)->create();

        $entityDb = Model::factory()->create();

        $entity = new VideoEntity(
            id: new Uuid($entityDb->id),
            title: 'AAAAAAAAAA Title To Search',
            description: 'AAAAAAAAAA Description To Search',
            yearLaunched: 2026,
            rating: Rating::L,
            duration: 100,
            opened: true,
        );

        foreach ($categories as $item) {
            $entity->addCategoryId($item->id);
        }
        
        foreach ($genres as $item) {
            $entity->addGenreId($item->id);
        }

        foreach ($castMembers as $item) {
            $entity->addCastMemberId($item->id);
        }

        $entityUpdated = $this->repository->update($entity);

        $this->assertInstanceOf(VideoEntity::class, $entityUpdated);
        $this->assertSame($entity->title, $entityUpdated->title);
        $this->assertSame($entity->description, $entityUpdated->description);
        $this->assertSame($entity->yearLaunched, $entityUpdated->yearLaunched);
        $this->assertSame($entity->rating, $entityUpdated->rating);
        $this->assertSame($entity->duration, $entityUpdated->duration);
        $this->assertSame($entity->opened, $entityUpdated->opened);

        $this->assertDatabaseHas('videos', [
            'id' => $entity->id(),
            'title' => $entity->title,
            'description' => $entity->description,
            'year_launched' => $entity->yearLaunched,
            'rating' => $entity->rating,
            'duration' => $entity->duration,
            'opened' => $entity->opened,
        ]);

        $this->assertEquals($categories->pluck('id')->toArray(), $entityUpdated->categoriesId);
        $this->assertEquals($genres->pluck('id')->toArray(), $entityUpdated->genresId);
        $this->assertEquals($castMembers->pluck('id')->toArray(), $entityUpdated->castMembersId);
    }

    public function test_delete_not_found(): void
    {
        $this->expectException(NotFoundException::class);

        $this->repository->delete('FAKE_ID');
    }

    public function test_delete(): void
    {
        $entityDb = Model::factory()->create();

        $response = $this->repository->delete($entityDb->id);

        $this->assertTrue($response);

        $this->assertSoftDeleted('videos', [
            'id' => $entityDb->id,
        ]);
    }

    public function test_insert_with_media_trailer(): void
    {
        $entity = new VideoEntity(
            title: 'AAAAAAAAAA Title To Search',
            description: 'AAAAAAAAAA Description To Search',
            yearLaunched: 2026,
            rating: Rating::L,
            duration: 100,
            opened: true,
            trailerFile: new ValueObjectMedia(
                path: 'test.mp4',
                mediaStatus: MediaStatus::PROCESSING
            ),
        );

        $this->repository->insert($entity);
        $this->assertDatabaseCount('medias_video', 0);
        $this->repository->updateMedia($entity);
        
        $this->assertDatabaseHas('medias_video', [
            'video_id'     => $entity->id(),
            'file_path'    => 'test.mp4',
            'media_status' => MediaStatus::PROCESSING->value,
        ]);
        $this->assertDatabaseCount('medias_video', 1);
    }

    public function test_set_media_trailer_method(): void
    {
        $entity = new VideoEntity(
            title: 'AAAAAAAAAA Title To Search',
            description: 'AAAAAAAAAA Description To Search',
            yearLaunched: 2026,
            rating: Rating::L,
            duration: 100,
            opened: true,
            trailerFile: new ValueObjectMedia(
                path: 'test.mp4',
                mediaStatus: MediaStatus::PROCESSING
            ),
        );

        $this->repository->insert($entity);
        $this->assertDatabaseCount('medias_video', 0);
        $this->repository->updateMedia($entity);
        
        $entity->setTrailerFile(new ValueObjectMedia(
            path: 'test2.mp4',
            mediaStatus: MediaStatus::PENDING,
            encodedPath: 'h43g45f3g45f.mp4',
        ));

        $entityUpdated = $this->repository->updateMedia($entity);

        $this->assertDatabaseHas('medias_video', [
            'video_id'     => $entity->id(),
            'file_path'    => 'test2.mp4',
            'media_status' => MediaStatus::PENDING->value,
            'encoded_path' => 'h43g45f3g45f.mp4'
        ]);
        $this->assertDatabaseCount('medias_video', 1);

        $this->assertNotNull($entityUpdated->trailerFile());
        $this->assertInstanceOf(ValueObjectMedia::class, $entityUpdated->trailerFile());
        $this->assertEquals($entityUpdated->trailerFile()->encodedPath(), 'h43g45f3g45f.mp4');
        $this->assertEquals($entityUpdated->trailerFile()->mediaStatus(), MediaStatus::PENDING);
        $this->assertEquals($entityUpdated->trailerFile()->path(), 'test2.mp4');
    }

    public function test_insert_with_image_banner()
    {
        $entity = new VideoEntity(
            title: 'AAAAAAAAAA Title To Search',
            description: 'AAAAAAAAAA Description To Search',
            yearLaunched: 2026,
            rating: Rating::L,
            duration: 100,
            opened: true,
            bannerFile: new ValueObjectImage(
                path: 'test.jpg',
            ),
        );

        $this->repository->insert($entity);

        $this->assertDatabaseCount('images_video', 0);

        $this->repository->updateMedia($entity);

        $this->assertDatabaseHas('images_video', [
            'video_id'  => $entity->id(),
            'file_path' => $entity->bannerFile()->path(),
            'type'      => ImageTypes::BANNER->value,
        ]);

        $this->assertDatabaseCount('images_video', 1);
    }

    public function test_update_with_image_banner()
    {
        $entity = new VideoEntity(
            title: 'AAAAAAAAAA Title To Search',
            description: 'AAAAAAAAAA Description To Search',
            yearLaunched: 2026,
            rating: Rating::L,
            duration: 100,
            opened: true,
        );

        $this->repository->insert($entity);

        $entity->setBannerFile(new ValueObjectImage(
            path: 'test2.jpg',
        ));
        
        $entity = $this->repository->updateMedia($entity);
        
        $this->assertDatabaseHas('images_video', [
            'video_id'  => $entity->id(),
            'file_path' => 'test2.jpg',
            'type'      => ImageTypes::BANNER->value,
        ]);

        $this->assertDatabaseCount('images_video', 1);

        $this->assertNotNull($entity->bannerFile());
    }

    public function test_insert_with_media_video()
    {
        $entity = new VideoEntity(
            title: 'AAAAAAAAAA Title To Search',
            description: 'AAAAAAAAAA Description To Search',
            yearLaunched: 2026,
            rating: Rating::L,
            duration: 100,
            opened: true,
            videoFile: new ValueObjectMedia(
                path: 'test.mp4',
                mediaStatus: MediaStatus::PROCESSING,
                encodedPath: 'teste.mp4'
            ),
        );

        $this->repository->insert($entity);

        $this->assertDatabaseCount('medias_video', 0);

        $entity = $this->repository->updateMedia($entity);

        $this->assertDatabaseHas('medias_video', [
            'video_id'     => $entity->id(),
            'file_path'    => $entity->videoFile()->path(),
            'type'         => MediaTypes::VIDEO->value,
            'encoded_path' => $entity->videoFile()->encodedPath(),
            'media_status' => MediaStatus::PROCESSING->value,
        ]);

        $this->assertDatabaseCount('medias_video', 1);
        $this->assertNotNull($entity->videoFile());
    }

    public function test_update_with_media_video()
    {
        $entity = new VideoEntity(
            title: 'AAAAAAAAAA Title To Search',
            description: 'AAAAAAAAAA Description To Search',
            yearLaunched: 2026,
            rating: Rating::L,
            duration: 100,
            opened: true,
            videoFile: new ValueObjectMedia(
                path: 'test.mp4',
                mediaStatus: MediaStatus::PROCESSING,
                encodedPath: 'teste.mp4'
            ),
        );

        $this->repository->insert($entity);
        $entity = $this->repository->updateMedia($entity);

        $entity->setVideoFile(new ValueObjectMedia(
            path: 'test3.mp4',
            mediaStatus: MediaStatus::COMPLETE,
            encodedPath: 'teste3.xpto',
        ));

        $entity = $this->repository->updateMedia($entity);

        $this->assertDatabaseHas('medias_video', [
            'video_id'     => $entity->id(),
            'file_path'    => 'test3.mp4',
            'type'         => MediaTypes::VIDEO->value,
            'encoded_path' => 'teste3.xpto',
            'media_status' => MediaStatus::COMPLETE->value,
        ]);

        $this->assertDatabaseCount('medias_video', 1);
        $this->assertNotNull($entity->videoFile());
    }
}
