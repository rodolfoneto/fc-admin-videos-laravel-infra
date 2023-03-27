<?php

namespace Tests\Unit\UseCase\Category;

use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\CreateCategoryUseCase;
use Core\UseCase\DTO\Category\CategoryCreateInputDto;
use Core\UseCase\DTO\Category\CategoryCreateOutputDto;
use DateTime;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class CreateCategoryUseCaseUnitTest extends TestCase
{
    private $mockEntity;
    private $mockRepo;
    private $mockInputDto;
    private $uuid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = (string) Uuid::uuid4()->toString();
        $categoryName = 'name cat';

        $this->mockEntity = Mockery::mock(Category::class, [
            $this->uuid,
            $categoryName,
        ]);
        $this->mockEntity->shouldReceive('id')->andReturn($this->uuid);
        $this->mockEntity->shouldReceive('createdAt')->andReturn((new DateTime())->format('Y-m-d H:i:s'));

        $this->mockRepo = Mockery::mock(stdclass::class, CategoryRepositoryInterface::class);
        $this->mockRepo->shouldReceive('insert')->andReturn($this->mockEntity);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        $this->mockEntity = null;
        $this->mockRepo = null;
        $this->mockInputDto = null;
        $this->uuid = null;
        Mockery::close();
    }

    public function testCreateNewCategory()
    {
        $categoryName = 'name cat';
        $this->mockInputDto = Mockery::mock(CategoryCreateInputDto::class, [
            $categoryName,
        ]);
        $this->mockInputDto->shouldReceive('createdAt')->andReturn('asdasd');

        $useCase = new CreateCategoryUseCase($this->mockRepo);
        $responseUseCase = $useCase->execute($this->mockInputDto);

        $this->assertInstanceOf(CategoryCreateOutputDto::class, $responseUseCase);
        $this->assertEquals($this->uuid, $responseUseCase->id);
        $this->assertEquals($categoryName, $responseUseCase->name);
        $this->assertEmpty($responseUseCase->description);
    }

    public function testVerifyInsertdMethodCalled()
    {
        $categoryName = "new cat";
        $this->mockInputDto = Mockery::mock(CategoryCreateInputDto::class, [
            $categoryName,
        ]);

        $spy = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $spy->shouldReceive('insert')->andReturn($this->mockEntity);
        
        $useCase = new CreateCategoryUseCase($spy);
        $responseUseCase = $useCase->execute($this->mockInputDto);
        $spy->shouldHaveReceived('insert');
        $this->assertInstanceOf(CategoryCreateOutputDto::class, $responseUseCase);
    }
}
