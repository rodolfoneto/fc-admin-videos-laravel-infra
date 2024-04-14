<?php

namespace Tests\Feature\App\Services;

use App\Services\Storage\FileStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileStorageTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_store()
    {
        $fakeFile    = UploadedFile::fake()->create('video.mp4', 1, 'video/mp4');
        $arrayFile   = $this->laravelUploadFileToFile($fakeFile);
        $fileStorage = new FileStorage();
        $path        = $fileStorage->store('videos', $arrayFile);

        Storage::assertExists($path);
        Storage::delete($path);
    }

    public function convertFileToLaravelFile(array $file, bool $test = false): UploadedFile
    {
        return new UploadedFile(
            path: $file['tmp_name'],
            originalName: $file['name'],
            mimeType: $file['type'],
            error: $file['error'],
            test: $test,
        );
    }

    public function test_delete()
    {
        $fakeFile    = UploadedFile::fake()->create('video.mp4', 1, 'video/mp4');
        $arrayFile   = $this->laravelUploadFileToFile($fakeFile);
        $fileStorage = new FileStorage();
        $path        = $fileStorage->store('videos', $arrayFile);

        Storage::assertExists($path);
        $fileStorage->delete($path);
        Storage::assertMissing($path);
    }

    protected function laravelUploadFileToFile(UploadedFile $laravelFile): array
    {
        return [
            'tmp_name' => $laravelFile->getRealPath(),
            'name'     => $laravelFile->getFilename(),
            'type'     => $laravelFile->getMimeType(),
            'error'    => $laravelFile->getError(),
        ];
    }
}
