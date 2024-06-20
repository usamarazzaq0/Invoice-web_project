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
 * App\Models\QuizUser
 *
 * @property int $id
 * @property int $quiz_id
 * @property int $user_id
 * @property int|null $marks
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read MediaCollection|Media[] $media
 * @property-read int|null $media_count
 * @property-read Quiz $quiz
 * @property-read User $user
 * @method static Builder|QuizUser newModelQuery()
 * @method static Builder|QuizUser newQuery()
 * @method static Builder|QuizUser query()
 * @method static Builder|QuizUser whereCreatedAt($value)
 * @method static Builder|QuizUser whereId($value)
 * @method static Builder|QuizUser whereMarks($value)
 * @method static Builder|QuizUser whereQuizId($value)
 * @method static Builder|QuizUser whereUpdatedAt($value)
 * @method static Builder|QuizUser whereUserId($value)
 * @mixin Eloquent
 */
class QuizUser extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
      'quiz_id',
      'user_id',
      'marks',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
