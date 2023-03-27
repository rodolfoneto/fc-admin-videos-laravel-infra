<?php

namespace Tests\Unit\UseCase\Category;

use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\ListCategoryUseCase;
use Core\UseCase\DTO\Category\{CategoryInputDto, CategoryOutputDto};
use DateTime;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class ListCategoryUseCaseUnitTest extends TestCase
{

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }


    public function testGetById()
    {
        $uuid = (string) Uuid::uuid4()->toString();
        $nameCategory = "new cat";

        $mockEntity = Mockery::mock(Category::class, [
            $uuid,
            $nameCategory,
        ]);
        $mockEntity->shouldReceive('id')->andReturn($uuid);
        $mockEntity->shouldReceive('createdAt')->andReturn((new DateTime())->format('Y-m-d H:i:s'));

        $mockRepo = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')
            ->with($uuid)
            ->andReturn($mockEntity);
        
        $mockInputDto = Mockery::mock(CategoryInputDto::class, [
            $uuid
        ]);

        $useCase = new ListCategoryUseCase($mockRepo);
        $outputDto = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(CategoryOutputDto::class, $outputDto);
        $this->assertEquals($uuid, $outputDto->id);
        $this->assertEquals($nameCategory, $outputDto->name);
    }

    public function testMethodGetByIdWillCall()
    {
        $uuid = Uuid::uuid4()->toString();
        $categoryName = "category name";
        $categoryMocked = Mockery::mock(Category::class, [
            $uuid,
            $categoryName
        ]);
        $categoryMocked->shouldReceive('id')->andReturn($uuid);
        $categoryMocked->shouldReceive('createdAt')->andReturn((new DateTime())->format('Y-m-d H:i:s'));

        $categoryInputDto = new CategoryInputDto($uuid);

        $spy = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $spy->shouldReceive('findById')->andReturn($categoryMocked);

        $useCase = new ListCategoryUseCase($spy);
        $outputDto = $useCase->execute($categoryInputDto);
        $spy->shouldHaveReceived('findById');

        $this->assertInstanceOf(CategoryOutputDto::class, $outputDto);
    }
}
