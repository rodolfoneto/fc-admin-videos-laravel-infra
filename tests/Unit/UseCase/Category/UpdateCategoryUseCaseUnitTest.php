<?php

namespace Tests\Unit\UseCase\Category;

use Core\Domain\Entity\Category as EntityCategory;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\UpdateCategoryUseCase;
use Core\UseCase\DTO\Category\{
    CategoryUpdateInputDto,
    CategoryUpdateOutputDto,
};
use DateTime;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UpdateCategoryUseCaseUnitTest extends TestCase
{
    public function testRenameCategory()
    {
        $uuid = Uuid::uuid4()->toString();
        $entiryName = "name";
        $entityDescription = "description";
        
        $entityMock = Mockery::mock(EntityCategory::class, [
            $uuid,
            $entiryName,
            $entityDescription,
        ]);
        $entityMock->shouldReceive('update');
        $entityMock->shouldReceive('createdAt')->andReturn((new DateTime())->format('Y-m-d H:i:s'));

        $entityUpdatedMock = Mockery::mock(EntityCategory::class, [
            $uuid,
            "new name",
        ]);
        $entityUpdatedMock->shouldReceive('createdAt')->andReturn((new DateTime())->format('Y-m-d H:i:s'));

        $mockRepo = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepo->shouldReceive('findById')->andReturn($entityMock);
        $mockRepo->shouldReceive('update')->andReturn($entityUpdatedMock);

        $inputDto = Mockery::mock(CategoryUpdateInputDto::class, [
            $uuid,
            "new name"
        ]);

        $useCase = new UpdateCategoryUseCase($mockRepo);
        $outputDto = $useCase->execute($inputDto);

        $this->assertInstanceOf(CategoryUpdateOutputDto::class, $outputDto);
        $this->assertEquals("new name", $outputDto->name);

        /**
         * Spies
         */
        $mockRepoSpy = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepoSpy->shouldReceive('findById')->andReturn($entityMock);
        $mockRepoSpy->shouldReceive('update')->andReturn($entityUpdatedMock);
        $useCaseSpy = new UpdateCategoryUseCase($mockRepoSpy);
        $useCaseSpy->execute($inputDto);
        $mockRepoSpy->shouldHaveReceived('findById');
        $mockRepoSpy->shouldHaveReceived('update');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
