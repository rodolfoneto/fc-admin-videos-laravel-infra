<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\Genre;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Notification\NotificationException;
use Core\Domain\ValueObject\Uuid;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Illuminate\Support\Str;
use DateTime;

class GenreUnitTest extends TestCase
{

    public function test_attributes()
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $createdAt = date('Y-m-d H:i:s');

        $genre = new Genre(
            name: "New Genre",
            id: new Uuid($uuid),
            isActive: false,
            createdAt: new DateTime($createdAt),
        );

        $this->assertEquals($uuid, $genre->id());
        $this->assertEquals("New Genre", $genre->name);
        $this->assertFalse($genre->isActive);
        $this->assertEquals($createdAt, $genre->createdAt());
    }

    public function test_attributes_create()
    {
        $genre = new Genre(
            name: "New Genre",
        );

        $this->assertNotEmpty($genre->id());
        $this->assertEquals("New Genre", $genre->name);
        $this->assertTrue($genre->isActive);
    }

    public function test_activate()
    {
        $genre = new Genre(
            name: "New Genre",
            isActive: false
        );

        $this->assertFalse($genre->isActive);
        $genre->activate();
        $this->assertTrue($genre->isActive);
    }

    public function test_deactivate()
    {
        $genre = new Genre(
            name: "New Genre"
        );

        $this->assertTrue($genre->isActive);
        $genre->deactivate();
        $this->assertFalse($genre->isActive);
    }

    public function test_create_with_too_short_name()
    {
        $this->expectException(NotificationException::class);
        new Genre(
            name: "N"
        );
    }

    public function test_create_with_name_too_large()
    {
        $this->expectException(NotificationException::class);
        new Genre(
            name: Str::random(256),
        );
    }

    public function test_update_name()
    {
        $genre = new Genre(
            name: "New Genre"
        );

        $genre->update(
            name: "Name updated"
        );

        $this->assertEquals("Name updated", $genre->name);
    }

    public function test_update_with_too_short_name()
    {
        $genre = new Genre(
            name: "New Genre"
        );
        $this->expectException(NotificationException::class);
        $genre->update(name: "N");
    }

    public function test_update_with_name_too_large()
    {
        $genre = new Genre(
            name: "New Genre",
        );
        $this->expectException(NotificationException::class);
        $genre->update(name: Str::random(256));
    }

    public function test_add_category_to_genre()
    {
        $categoryId = RamseyUuid::uuid4()->toString();
        $genre = new Genre(
            name: "New Genre",
        );

        $this->assertIsArray($genre->categoriesId);
        $this->assertCount(0, $genre->categoriesId);

        $genre->addCategory(categoryId: $categoryId);
        $genre->addCategory(categoryId: $categoryId);

        $this->assertCount(2, $genre->categoriesId);
    }

    public function test_remove_category_to_genre()
    {
        $categoryId = RamseyUuid::uuid4()->toString();
        $categoryId2 = RamseyUuid::uuid4()->toString();
        $genre = new Genre(
            name: "New Genre",
            categoriesId: [
                $categoryId,
                $categoryId2,
            ]
        );

        $this->assertCount(2, $genre->categoriesId);
        $genre->removeCategory(categoryId: $categoryId2);
        $this->assertCount(1, $genre->categoriesId);
        $this->assertEquals($categoryId, $genre->categoriesId[0]);
    }

    public function test_remove_category_with_id_not_exist()
    {
        $categoryId = RamseyUuid::uuid4()->toString();
        $genre = new Genre(
            name: "New Genre",
            categoriesId: [
                $categoryId,
            ]
        );
        $this->expectException(NotFoundException::class);
        $genre->removeCategory(categoryId: 'FAKE_ID');
    }
}
