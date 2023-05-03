<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Notification\NotificationException;
use Core\Domain\ValueObject\Image;
use Core\Domain\ValueObject\Media;
use DateTime;
use Core\Domain\Entity\Video;
use Core\Domain\Enum\Rating;
use Core\Domain\Enum\MediaStatus;
use Core\Domain\ValueObject\Uuid;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;

class VideoUnitTest extends TestCase
{
    public function test_attributes()
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $video = new Video(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            id: new Uuid($uuid),
            publish: false,
            createdAt: new DateTime(date('Y-m-d H:i:s')),
        );

        $this->assertEquals($uuid, $video->id());
        $this->assertEquals('New Video', $video->title );
        $this->assertEquals('New Description', $video->description);
        $this->assertEquals(2029, $video->yearLaunched);
        $this->assertEquals(12, $video->duration);
        $this->assertTrue($video->opened);
        $this->assertEquals(Rating::RATE12, $video->rating);
        $this->assertFalse($video->publish);
        $this->assertNotEmpty($video->createdAt());
    }

    public function test_attributes_without_id_and_created_at()
    {
        $video = new Video(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            publish: false,
        );

        $this->assertNotEmpty($video->id());
        $this->assertNotEmpty($video->createdAt());
    }

    public function test_add_categories()
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $categoryId = RamseyUuid::uuid4()->toString();
        $video = new Video(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            id: new Uuid($uuid),
            publish: false,
        );

        $video->addCategoryId(categoryId: $categoryId);
        $video->addCategoryId(categoryId: $categoryId);

        $this->assertCount(2, $video->categoriesId);
    }

    public function test_remove_categories()
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $categoryId = RamseyUuid::uuid4()->toString();
        $categoryId2 = RamseyUuid::uuid4()->toString();
        $categoryId3 = RamseyUuid::uuid4()->toString();
        $video = new Video(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            id: new Uuid($uuid),
            publish: false,
        );

        $video->addCategoryId(categoryId: $categoryId);
        $video->addCategoryId(categoryId: $categoryId2);
        $this->assertCount(2, $video->categoriesId);
        $video->removeCategoryId(categoryId: $categoryId);
        $video->removeCategoryId(categoryId: $categoryId3);
        $this->assertCount(1, $video->categoriesId);
        $video->removeCategoryId(categoryId: $categoryId2);
        $this->assertCount(0, $video->categoriesId);
    }

    public function test_add_genres()
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $genreId = RamseyUuid::uuid4()->toString();
        $genreId2 = RamseyUuid::uuid4()->toString();
        $video = new Video(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            id: new Uuid($uuid),
            publish: false,
        );

        $video->addGenreId(genreId: $genreId);
        $video->addGenreId(genreId: $genreId2);
        $this->assertCount(2, $video->genresId);
    }

    public function test_remove_genres()
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $genreId = RamseyUuid::uuid4()->toString();
        $genreId2 = RamseyUuid::uuid4()->toString();
        $genreId3 = RamseyUuid::uuid4()->toString();
        $video = new Video(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            id: new Uuid($uuid),
            publish: false,
        );

        $video->addGenreId(genreId: $genreId);
        $video->addGenreId(genreId: $genreId2);
        $this->assertCount(2, $video->genresId);
        $video->removeGenreId(genreId: $genreId);
        $video->removeGenreId(genreId: $genreId3);
        $this->assertCount(1, $video->genresId);
        $video->removeGenreId(genreId: $genreId2);
        $this->assertCount(0, $video->genresId);
    }

    public function test_add_castmember()
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $castMemberId = RamseyUuid::uuid4()->toString();
        $castMemberId2 = RamseyUuid::uuid4()->toString();
        $video = new Video(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            id: new Uuid($uuid),
            publish: false,
        );

        $video->addCastMemberId(castMemberId: $castMemberId);
        $video->addCastMemberId(castMemberId: $castMemberId2);
        $this->assertCount(2, $video->castMembersId);
    }

    public function test_remove_castmember()
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $castMemberId = RamseyUuid::uuid4()->toString();
        $castMemberId2 = RamseyUuid::uuid4()->toString();
        $castMemberId3 = RamseyUuid::uuid4()->toString();
        $video = new Video(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            id: new Uuid($uuid),
            publish: false,
        );

        $video->addCastMemberId(castMemberId: $castMemberId);
        $video->addCastMemberId(castMemberId: $castMemberId2);
        $this->assertCount(2, $video->castMembersId);
        $video->removeCastMemberId(castMemberId: $castMemberId);
        $video->removeCastMemberId(castMemberId: $castMemberId3);
        $this->assertCount(1, $video->castMembersId);
        $video->removeCastMemberId(castMemberId: $castMemberId2);
        $this->assertCount(0, $video->castMembersId);
    }

    public function test_value_object_image()
    {
        $video = new Video(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            publish: false,
            thumbFile: new Image(path: 'asdasdad/adsads.jpg'),
        );
        $this->assertNotEmpty($video->thumbFile());
        $this->assertInstanceOf(Image::class, $video->thumbFile());
        $this->assertEquals('asdasdad/adsads.jpg', $video->thumbFile()->path());
    }

    public function test_value_object_thumb_half()
    {
        $video = new Video(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            publish: false,
            thumbHalf: new Image(path: 'asdasdad/adsads.jpg'),
        );
        $this->assertNotEmpty($video->thumbHalfFile());
        $this->assertInstanceOf(Image::class, $video->thumbHalfFile());
        $this->assertEquals('asdasdad/adsads.jpg', $video->thumbHalfFile()->path());
    }

    public function test_value_object_to_banner_file()
    {
        $video = new Video(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            publish: false,
            bannerFile: new Image(path: 'imgs/banner.jpg'),
        );
        $this->assertNotEmpty($video->bannerFile());
        $this->assertInstanceOf(Image::class, $video->bannerFile());
        $this->assertEquals('imgs/banner.jpg', $video->bannerFile()->path());
    }

    public function test_value_object_media_trailer_file()
    {
        $mediaFile = new Media(
            path: 'path/video.mp4',
            mediaStatus: MediaStatus::PENDING,
            encodedPath: 'path/encoded.extension',
        );

        $video = new Video(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            publish: false,
            trailerFile: $mediaFile,
        );
        $this->assertNotEmpty($video->trailerFile());
        $this->assertInstanceOf(Media::class, $video->trailerFile());
        $this->assertEquals('path/video.mp4', $video->trailerFile()->path());
        $this->assertEquals(MediaStatus::PENDING, $video->trailerFile()->mediaStatus());
        $this->assertEquals('path/encoded.extension', $video->trailerFile()->encodedPath());
    }

    public function test_value_object_media_video_file()
    {
        $mediaFile = new Media(
            path: 'path/video.mp4',
            mediaStatus: MediaStatus::COMPLETE,
        );

        $video = new Video(
            title: 'New Video',
            description: 'New Description',
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            publish: false,
            videoFile: $mediaFile,
        );
        $this->assertNotEmpty($video->videoFile());
        $this->assertInstanceOf(Media::class, $video->videoFile());
        $this->assertEquals('path/video.mp4', $video->videoFile()->path());
        $this->assertEquals(MediaStatus::COMPLETE, $video->videoFile()->mediaStatus());
        $this->assertEmpty($video->videoFile()->encodedPath());
    }

    public function test_exception()
    {
        $this->expectException(NotificationException::class);
        new Video(
            title: "N",
            description: Str::random(300),
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            publish: false,
        );
    }

    public function test_update_exception()
    {
        $video = new Video(
            title: "New Video",
            description: Str::random(100),
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            publish: false,
        );
        $this->expectException(NotificationException::class);
        $video->update(
            title: 'N',
            description: 'D',
        );
    }

    public function test_update()
    {
        $video = new Video(
            title: "New Video",
            description: Str::random(100),
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            publish: false,
        );
        $video->update(
            title: 'Updated Video',
            description: 'New description',
        );
        $this->assertEquals('Updated Video', $video->title);
        $this->assertEquals('New description', $video->description);
    }

    public function test_update_without_change_description()
    {
        $description = Str::random(100);
        $video = new Video(
            title: "New Video",
            description: $description,
            yearLaunched: 2029,
            duration: 12,
            opened: true,
            rating: Rating::RATE12,
            publish: false,
        );
        $video->update(
            title: 'Updated Video',
        );
        $this->assertEquals('Updated Video', $video->title);
        $this->assertEquals($description, $video->description);
    }
}
