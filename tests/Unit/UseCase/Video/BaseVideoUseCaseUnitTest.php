<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Entity\Entity;
use Core\Domain\Entity\Video;
use Core\Domain\Enum\Rating;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\{CastMemberRepositoryInterface,
    CategoryRepositoryInterface,
    GenreRepositoryInterface,
    VideoRepositoryInterface,};
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\Interface\{FileStorageInterface, TransactionInterface};
use Core\UseCase\Video\BaseVideoUseCase;
use Core\UseCase\Video\Interfaces\VideoEventManagerInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;
use DateTime;

abstract class BaseVideoUseCaseUnitTest extends TestCase
{
    protected BaseVideoUseCase $useCase;

    abstract protected function getActionRepository(): string;
    abstract protected function getNameClassUseCase(): string;

    abstract protected function createMockInputDto(
        array $categoriesId = [],
        array $genresId = [],
        array $castMembersId = [],
        array $thumbFile = null,
        array $thumbHalfFile = null,
        array $bannerFile = null,
        array $trailerFile = null,
        array $videoFile = null,
    );

    protected function createUseCase(
        int $timesCallMethodActionRepository = 1,
        int $timesCallMethodUpdateMedia = 1,
        int $timesCallTransactionCommit = 1,
        int $timesCallTransactionRollBack = 0,
        int $timesCallStoreStorage = 0,
        int $timesCallEventManagerDispatch = 0
    ): void {
        $nameClassUseCase = $this->getNameClassUseCase();
        $this->useCase = new $nameClassUseCase(
            repository: $this->createMockRepository(
                timesCallAction: $timesCallMethodActionRepository,
                timesCallUpdateMedia: $timesCallMethodUpdateMedia
            ),
            transaction: $this->createMockTransaction(
                timesCallCommit: $timesCallTransactionCommit,
                timesCallCallBack: $timesCallTransactionRollBack
            ),
            storage: $this->createMockFileStorage(
                timesCallStoreStorage: $timesCallStoreStorage
            ),
            eventManager: $this->createMockEventManager(timesCall: $timesCallEventManagerDispatch),

            categoryRepository: $this->createMockRepositoryCategory(),
            genreRepository: $this->createMockRepositoryGenre(),
            castMemberRepository: $this->createMockRepositoryCastMember(),
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @dataProvider dataProviderIds
     */
    public function test_exception_validate_categories_id(
        string $label,
        array $catIds,
        array $genIds,
        array $cmIds,
    ) {
        $this->createUseCase(
            timesCallMethodActionRepository: 0,
            timesCallMethodUpdateMedia: 0,
            timesCallTransactionCommit: 0,
            timesCallStoreStorage: 0,
        );
        $current = !empty($catIds) ? $catIds : (!empty($genIds) ? $genIds : $cmIds);
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(sprintf(
            '%s %s not found',
            $label,
            implode(', ', $current)
        ));
        $this->useCase->execute(
            input: $this->createMockInputDto(categoriesId: $catIds, genresId: $genIds, castMembersId: $cmIds),
        );
    }

    public function dataProviderIds(): array
    {
        return [
            ['Category', ['uuid-1'], [], []],
            ['Categories', ['CAT_ID', 'CAT_ID_001'], [], []],
            ['Categories', ['CAT_ID', 'CAT_ID_001', 'CAT_ID_002', 'CAT_ID_004'], [], []],
            ['Genre', [], ['GEN_ID'], []],
            ['Genres', [], ['GEN_ID', 'GEN_ID_2', 'GEN_ID_3'], []],
            ['CastMember', [], [], ['CM_001']],
            ['CastMembers', [], [], ['CM_001', 'CM_002', 'CM_003']],
            ['Category', ['uuid-1'], ['GEN_ID', 'GEN_ID_2', 'GEN_ID_3'], ['CM_001', 'CM_002', 'CM_003']],
        ];
    }

    /**
     * @dataProvider dataProviderFiles
     */
    public function test_upload_files(
        array $thumbFile,
        array $thumbHalfFile,
        array $bannerFile,
        array $trailerFile,
        array $videoFile,
        int $timesCallStorageStore,
        int $timesCallEventManagerDispatch,
    ): void {
        $this->createUseCase(
            timesCallStoreStorage: $timesCallStorageStore,
            timesCallEventManagerDispatch: $timesCallEventManagerDispatch
        );
        $output = $this->useCase->execute(
            input: $this->createMockInputDto(
                thumbFile: $thumbFile['value'],
                thumbHalfFile: $thumbHalfFile['value'],
                bannerFile: $bannerFile['value'],
                trailerFile: $trailerFile['value'],
                videoFile: $videoFile['value'],
            ),
        );
        $this->assertEquals($thumbFile['expect'], $output->thumbFile);
        $this->assertEquals($thumbHalfFile['expect'], $output->thumbHalfFile);
        $this->assertEquals($bannerFile['expect'], $output->bannerFile);
        $this->assertEquals($trailerFile['expect'], $output->trailerFile);
        $this->assertEquals($videoFile['expect'], $output->videoFile);
    }

    public function dataProviderFiles(): array
    {
        return [
            [
                'thumbFile' => ['value' => ['tmp' => 'tmp/thumb.jpg'], 'expect' => 'path/fileReceived.png'],
                'thumbHalfFile' => ['value' => ['tmp' => 'tmp/thumb.jpg'], 'expect' => 'path/fileReceived.png'],
                'bannerFile' => ['value' => ['tmp' => 'tmp/thumb.jpg'], 'expect' => 'path/fileReceived.png'],
                'trailerFile' => ['value' => ['tmp' => 'tmp/thumb.png'], 'expect' => 'path/fileReceived.png'],
                'videoFile' => ['value' => ['tmp' => 'tmp/thumb.png'], 'expect' => 'path/fileReceived.png'],
                'timesCallStorageStore' => 5,
                'timesCallEventManagerDispatch' => 1
            ],
            [
                'thumbFile' => ['value' => ['tmp' => 'tmp/thumb.png'], 'expect' => 'path/fileReceived.png'],
                'thumbHalfFile' => ['value' => null, 'expect' => null],
                'bannerFile' => ['value' => null, 'expect' => null],
                'trailerFile' => ['value' => null, 'expect' => null],
                'videoFile' => ['value' => null, 'expect' => null],
                'timesCallStorageStore' => 1,
                'timesCallEventManagerDispatch' => 0,
            ],
            [
                'thumbFile' => ['value' => null, 'expect' => null],
                'thumbHalfFile' => ['value' => ['tmp' => 'tmp/thumb.png'], 'expect' => 'path/fileReceived.png'],
                'bannerFile' => ['value' => null, 'expect' => null],
                'trailerFile' => ['value' => null, 'expect' => null],
                'videoFile' => ['value' => null, 'expect' => null],
                'timesCallStorageStore' => 1,
                'timesCallEventManagerDispatch' => 0,
            ],
            [
                'thumbFile' => ['value' => null, 'expect' => null],
                'thumbHalfFile' => ['value' => null, 'expect' => null],
                'bannerFile' => ['value' => ['tmp' => 'tmp/thumb.png'], 'expect' => 'path/fileReceived.png'],
                'trailerFile' => ['value' => null, 'expect' => null],
                'videoFile' => ['value' => null, 'expect' => null],
                'timesCallStorageStore' => 1,
                'timesCallEventManagerDispatch' => 0
            ],
            [
                'thumbFile' => ['value' => null, 'expect' => null],
                'thumbHalfFile' => ['value' => null, 'expect' => null],
                'bannerFile' => ['value' => null, 'expect' => null],
                'trailerFile' => ['value' => ['tmp' => 'tmp/thumb.png'], 'expect' => 'path/fileReceived.png'],
                'videoFile' => ['value' => null, 'expect' => null],
                'timesCallStorageStore' => 1,
                'timesCallEventManagerDispatch' => 0
            ],
            [
                'thumbFile' => ['value' => null, 'expect' => null],
                'thumbHalfFile' => ['value' => null, 'expect' => null],
                'bannerFile' => ['value' => null, 'expect' => null],
                'trailerFile' => ['value' => null, 'expect' => null],
                'videoFile' => ['value' => ['tmp' => 'tmp/thumb.png'], 'expect' => 'path/fileReceived.png'],
                'timesCallStorageStore' => 1,
                'timesCallEventManagerDispatch' => 1
            ],
            [
                'thumbFile' => ['value' => null, 'expect' => null],
                'thumbHalfFile' => ['value' => null, 'expect' => null],
                'bannerFile' => ['value' => null, 'expect' => null],
                'trailerFile' => ['value' => null, 'expect' => null],
                'videoFile' => ['value' => null, 'expect' => null],
                'timesCallStorageStore' => 0,
                'timesCallEventManagerDispatch' => 0
            ],
        ];
    }

    protected function createMockRepository(
        int $timesCallAction,
        int $timesCallUpdateMedia,
    ) {
        $entity = $this->createEntity();
        $repository = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $repository->shouldReceive($this->getActionRepository())
            ->times($timesCallAction)
            ->andReturn($entity);
        $repository->shouldReceive('findById')
            ->andReturn($entity);
        $repository->shouldReceive('updateMedia')
            ->times($timesCallUpdateMedia);
        return $repository;
    }

    protected function createMockTransaction(
        int $timesCallCommit,
        int $timesCallCallBack,
    ) {
        $transaction = Mockery::mock(stdClass::class, TransactionInterface::class);
        $transaction->shouldReceive('commit')->times($timesCallCommit);
        $transaction->shouldReceive('rollback')->times($timesCallCallBack);
        return $transaction;
    }

    protected function createMockFileStorage(
        int $timesCallStoreStorage,
    ) {
        $fileStorage = Mockery::mock(stdClass::class, FileStorageInterface::class);
        $fileStorage->shouldReceive('store')
            ->times($timesCallStoreStorage)
            ->andReturn('path/fileReceived.png');
        return $fileStorage;
    }

    protected function createMockEventManager(int $timesCall)
    {
        $eventManager = Mockery::mock(stdClass::class, VideoEventManagerInterface::class);
        $eventManager->shouldReceive('dispatch')
            ->times($timesCall);
        return $eventManager;
    }

    protected function createMockRepositoryCategory(array $categoriesId = [])
    {
        $mockCategoryRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockCategoryRepository->shouldReceive('getIdsListIds')
            ->andReturn($categoriesId);
        return $mockCategoryRepository;
    }

    protected function createMockRepositoryGenre(array $genresId = [])
    {
        $mockGenreRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockGenreRepository->shouldReceive('getIdsListIds')
            ->andReturn($genresId);
        return $mockGenreRepository;
    }

    protected function createMockRepositoryCastMember(array $castMembersId = [])
    {
        $mockCastMemberRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockCastMemberRepository->shouldReceive('getIdsListIds')
            ->andReturn($castMembersId);
        return $mockCastMemberRepository;
    }

    protected function createMockEntity()
    {
        $entity = Mockery::mock(Video::class, [
            "New Video",
            "Video Description",
            2023,
            10,
            true,
            Rating::RATE10,
        ]);
        return $entity;
    }

    protected function createEntity(): Video
    {
        $entity = new Video(
            title: 'New Video',
            description: 'Description',
            yearLaunched: 123,
            duration: 123,
            opened: true,
            rating: Rating::L,
//            id: Uuid::random(),publish: true
        );
        return $entity;
    }
}
