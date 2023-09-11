<?php

namespace Tests\Unit\App\Models;

use App\Models\{
    ImageVideo,
    Traits\UuidTrait,
};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ImageVideoUnitTest extends ModelTestCase
{
    protected function model(): Model
    {
        return new ImageVideo();
    }

    protected function traits(): array
    {
        return [
            HasFactory::class,
            UuidTrait::class,
        ];
    }

    protected function fillable(): array
    {
        return [
            'file_path',
            'type',
        ];
    }

    protected function incrementing(): bool
    {
        return false;
    }

    protected function casting(): array
    {
        return [
            'id' => 'string',
            'is_active' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }
}
