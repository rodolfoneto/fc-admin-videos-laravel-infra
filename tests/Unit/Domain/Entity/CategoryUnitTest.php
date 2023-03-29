<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\Category;
use Core\Domain\Exception\EntityValidationException;
use DateTime;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CategoryUnitTest extends TestCase
{
    public function testAttributes()
    {
        $uuid = Uuid::uuid4();
        $category = new Category(
            id: $uuid,
            name: "New Cat",
            description: "New desc",
            isActive: true,
            createdAt: (new DateTime())->format('Y-m-d H:i:s'),
        );

        $this->assertNotEmpty($category->createdAt());
        $this->assertEquals("New Cat", $category->name);
        $this->assertEquals("New Cat", $category->name);
        $this->assertEquals("New desc", $category->description);
        $this->assertTrue($category->isActive);
    }

    public function testActivated()
    {
        $category = new Category(
            name: "New Cat",
            isActive: false,
        );
        $this->assertFalse($category->isActive);
        $category->activate();
        $this->assertTrue($category->isActive);
    }

    public function testDeactivated()
    {
        $category = new Category(
            name: "New Cat",
            isActive: true,
        );
        $this->assertTrue($category->isActive);
        $category->deactivate();
        $this->assertFalse($category->isActive);
    }

    public function testUpdate()
    {
        $uuid = (string) Uuid::uuid4()->toString();
        $category = new Category(
            id: $uuid,
            name: "New Cat",
            description: "New desc",
            createdAt: '2023-01-12 22:18:10'
        );

        $category->update(
            name: "new_name",
            description: "new_desc",
        );

        $this->assertEquals('2023-01-12 22:18:10', $category->createdAt());
        $this->assertEquals($uuid, $category->id());
        $this->assertEquals("new_name", $category->name);
        $this->assertEquals("new_desc", $category->description);
    }

    public function testCreateNewCategoryWithInvalidName()
    {
        $this->expectException(EntityValidationException::class);
        new Category(
            name: "N",
            description: "New desc",
        );
    }

    public function testCreateNewCategoryWithDescriptionNull()
    {
        $category = new Category(
            name: "New Category",
            description: "",
        );
        $this->assertEquals("", $category->description);
    }

    public function testCreateNewCategoryWithInvalidDescription()
    {
        $this->expectException(EntityValidationException::class);
        new Category(
            name: "New Category",
            description: random_bytes(256),
        );
    }

    public function testCreateNewCategoryWithValidDescription()
    {
        $category = new Category(
            name: "New Category",
            description: "A valid description",
        );
        $this->assertEquals("A valid description", $category->description);
    }

    public function testCreateNewCategoryWithValidCreatedAt()
    {
        $format = 'Y-m-d H:i:s';
        $createdAt = new DateTime();
        $category = new Category(
            name: "New Category",
            description: "A valid description",
            createdAt: $createdAt->format($format),
        );
        $this->assertEquals($createdAt->format($format), $category->createdAt($format));
    }
}
