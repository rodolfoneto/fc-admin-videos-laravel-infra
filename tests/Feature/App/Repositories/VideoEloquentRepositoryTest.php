<?php

namespace Tests\Feature\App\Repositories;

use App\Events\VideoEvent;
use App\Models\{
    CastMember,
    Category,
    Genre,
    Video as Model,
};
use App\Repositories\Eloquent\VideoEloquentRepository;
use Core\Domain\Entity\Video as VideoEntity;
use Core\Domain\Enum\Rating;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
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
        Model::factory()->create(['title' => 'Z Z Z Z Z Z Z Z Z Z']);

        $response = $this->repository->findAll(order: "ASC");
        $this->assertCount(32, $response);
        $this->assertEquals('AAAAAAAAAA', $response[0]->title);
        $this->assertEquals('Z Z Z Z Z Z Z Z Z Z', $response[31]->title);
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

        $response = $this->repository->update($entity);

        $this->assertInstanceOf(VideoEntity::class, $response);
        $this->assertSame($entity->title, $response->title);
        $this->assertSame($entity->description, $response->description);
        $this->assertSame($entity->yearLaunched, $response->yearLaunched);
        $this->assertSame($entity->rating, $response->rating);
        $this->assertSame($entity->duration, $response->duration);
        $this->assertSame($entity->opened, $response->opened);

        $this->assertDatabaseHas('videos', [
            'id' => $entity->id(),
            'title' => $entity->title,
            'description' => $entity->description,
            'year_launched' => $entity->yearLaunched,
            'rating' => $entity->rating,
            'duration' => $entity->duration,
            'opened' => $entity->opened,
        ]);
    }
}
