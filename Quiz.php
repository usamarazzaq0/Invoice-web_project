<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * App\Models\Quiz
 *
 * @property int $id
 * @property int $classroom_id
 * @property string $name
 * @property int $marks
 * @property string $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Classroom $classroom
 * @property-read MediaCollection|Media[] $media
 * @property-read int|null $media_count
 * @property-read Collection|QuizUser[] $quizzes
 * @property-read int|null $quizzes_count
 * @method static Builder|Quiz newModelQuery()
 * @method static Builder|Quiz newQuery()
 * @method static Builder|Quiz query()
 * @method static Builder|Quiz whereClassroomId($value)
 * @method static Builder|Quiz whereCreatedAt($value)
 * @method static Builder|Quiz whereDescription($value)
 * @method static Builder|Quiz whereId($value)
 * @method static Builder|Quiz whereMarks($value)
 * @method static Builder|Quiz whereName($value)
 * @method static Builder|Quiz whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Quiz extends Model implements HasMedia
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
        'marks',
        'description',
    ];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(QuizUser::class);
    }
}
