<?php

namespace App\Models;

use App\Enums\ImageTypes;
use App\Enums\MediaTypes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration',
        'created_at'
    ];

    protected $casts = [
        'id' => 'string',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function castMembers(): BelongsToMany
    {
        return $this->belongsToMany(CastMember::class, 'cast_member_video');
    }

    public function media(): HasOne
    {
        return $this->hasOne(Media::class)
            ->where('type', MediaTypes::VIDEO->value);
    }

    public function trailer(): HasOne
    {
        return $this->hasOne(Media::class)
            ->where('type', MediaTypes::TRAILER->value);
    }

    public function banner(): HasOne
    {
        return $this->hasOne(ImageVideo::class)
            ->where('type', ImageTypes::BANNER->value);
    }

    public function thumb(): HasOne
    {
        return $this->hasOne(ImageVideo::class)
            ->where('type', ImageTypes::THUMB->value);
    }

    public function thumbHalf(): HasOne
    {
        return $this->hasOne(ImageVideo::class)
            ->where('type', ImageTypes::THUMB_HALF->value);
    }
}
