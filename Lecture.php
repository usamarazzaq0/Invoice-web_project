<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * App\Models\Lecture
 *
 * @property int $id
 * @property int $classroom_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Classroom $classroom
 * @property-read MediaCollection|Media[] $media
 * @property-read int|null $media_count
 * @method static Builder|Lecture newModelQuery()
 * @method static Builder|Lecture newQuery()
 * @method static Builder|Lecture query()
 * @method static Builder|Lecture whereClassroomId($value)
 * @method static Builder|Lecture whereCreatedAt($value)
 * @method static Builder|Lecture whereId($value)
 * @method static Builder|Lecture whereName($value)
 * @method static Builder|Lecture whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Lecture extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'classroom_id',
        'name',
    ];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }
}
