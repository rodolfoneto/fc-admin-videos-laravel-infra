<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\UuidTrait;

class Media extends Model
{
    use HasFactory, UuidTrait;

    protected $table = 'medias_video';

    public $incrementing = false;

    protected $fillable = [
        'file_path',
        'encoded_path',
        'media_status',
        'type',
    ];

    protected $casts = [
        'id' => 'string',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }
}
