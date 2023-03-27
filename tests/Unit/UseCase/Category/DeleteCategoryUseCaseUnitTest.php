<?php

namespace Tests\Unit\Domain\UseCase\Category;

use Core\Domain\Entity\Category as EntityCategory;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\DeleteCategoryUseCase;
use Core\UseCase\DTO\Category\CategoryDeleteInputDto;
use Core\UseCase\DTO\Category\CategoryDeleteOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class DeleteCategoryUseCaseUnitTest extends TestCase
{
    public function testDeleteCategory()
    {
        $uuid = Uuid::uuid4()->toString();

        $categoryMocked = Mockery::mock(EntityCategory::class, [
            $uuid,
            "name",
            "description",
        ]);

        $mockRepo = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn($categoryMocked);
        $mockRepo->shouldReceive('delete')->andReturn(true);
        
        $useCase = new DeleteCategoryUseCase($mockRepo);
        $input = Mockery::mock(CategoryDeleteInputDto::class, [$uuid]);
        $outputDto = $useCase->execute($input);
        $this->assertInstanceOf(CategoryDeleteOutputDto::class, $outputDto);
        $this->assertTrue($outputDto->success);
    }

    public function testDeleteCategoryDeleteMethodWillCall()
    {
        $uuid = Uuid::uuid4()->toString();

        $categoryMocked = Mockery::mock(EntityCategory::class, [
            $uuid,
            "name",
            "description",
        ]);

        $mockRepo = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn($categoryMocked);
        $mockRepo->shouldReceive('delete')->andReturn(false);
        
        $useCase = new DeleteCategoryUseCase($mockRepo);
        $input = Mockery::mock(CategoryDeleteInputDto::class, [$uuid]);
        $outputDto = $useCase->execute($input);
        $mockRepo->shouldHaveReceived('delete');
        $this->assertFalse($outputDto->success);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
