<?php

namespace Tests\Unit\Domain\Builder\Video;

use Core\Domain\Builder\Video\BuilderInterface;
use Core\Domain\Builder\Video\CreateVideoBuilder;
use Core\Domain\Entity\Video;
use Core\Domain\Enum\MediaStatus;
use Core\Domain\Enum\Rating;
use Core\UseCase\Video\Create\Dto\CreateVideoInputDto;
use Mockery;
use PHPUnit\Framework\TestCase;

class CreateVideoBuilderUnitTest extends TestCase
{
    protected $inputDto;
    protected $video;
    protected $builder;
    
    public function test_verify_interface_implementation()
    {
        $builder = new CreateVideoBuilder();
        $this->assertInstanceOf(BuilderInterface::class, $builder);
    }

    public function test_verify_video_instance()
    {
        $this->startMock();
        $this->assertInstanceOf(Video::class, $this->video);
        $this->assertEquals($this->inputDto->title, $this->video->title);
        $this->assertEquals($this->inputDto->description, $this->video->description);
        $this->assertEquals($this->inputDto->yearLaunched, $this->video->yearLaunched);
        $this->assertEquals($this->inputDto->duration, $this->video->duration);
        $this->assertEquals($this->inputDto->opened, $this->video->opened);
        $this->assertEquals($this->inputDto->rating, $this->video->rating);
        $this->assertEquals($this->inputDto->categories, $this->video->categoriesId);
        $this->assertEquals($this->inputDto->genres, $this->video->genresId);
        $this->assertEquals($this->inputDto->castMembers, $this->video->castMembersId);
    }

    public function test_verify_video_instance_with_video_media_file()
    {
        $this->startMock();
        $this->builder->addMediaVideo('path_of_media.mp4', MediaStatus::PENDING);
        $this->assertEquals(MediaStatus::PENDING, $this->video->videoFile()->mediaStatus());
        $this->assertEquals('path_of_media.mp4', $this->video->videoFile()->path());
    }

    public function test_verify_video_instance_with_thumb_media_file()
    {
        $this->startMock();
        $this->builder->addThumb('path_of_media.jpg', MediaStatus::PENDING);
        $this->assertEquals('path_of_media.jpg', $this->video->thumbFile()->path());
    }

    public function test_verify_video_instance_with_thumb_half_media_file()
    {
        $this->startMock();
        $this->builder->addThumbHalf('path_of_media.jpg');
        $this->assertEquals('path_of_media.jpg', $this->video->thumbHalfFile()->path());
    }

    public function test_verify_video_instance_with_banner_media_file()
    {
        $this->startMock();
        $this->builder->addBanner('path_of_media.jpg');
        $this->assertEquals('path_of_media.jpg', $this->video->bannerFile()->path());
    }

    public function test_verify_video_instance_with_trailer_media_file()
    {
        $this->startMock();
        $this->builder->addTrailer('path_of_media_trailer.mp4');
        $this->assertEquals('path_of_media_trailer.mp4', $this->video->trailerFile()->path());
    }

    private function createMockInput(
        array $categoriesId = [],
        array $genresId = [],
        array $castMembers = []
    ): object {
        return Mockery::mock(CreateVideoInputDto::class, [
            'new video',
            'video description',
            2010,
            20,
            true,
            Rating::RATE10,
            $categoriesId,
            $genresId,
            $castMembers,
        ]);
    }

    protected function startMock(
        array $categoriesId = [],
        array $genresId = [],
        array $castMembers = []
    ) {
        $this->builder = new CreateVideoBuilder();
        $this->inputDto = $this->createMockInput($categoriesId, $genresId, $castMembers);
        $this->builder->createEntity($this->inputDto);
        $this->video = $this->builder->getEntity();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
