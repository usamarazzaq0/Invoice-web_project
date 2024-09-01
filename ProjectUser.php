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
 * App\Models\ProjectUser
 *
 * @property-read MediaCollection|Media[] $media
 * @property-read int|null $media_count
 * @property-read Project|null $project
 * @property-read User $user
 * @method static Builder|ProjectUser newModelQuery()
 * @method static Builder|ProjectUser newQuery()
 * @method static Builder|ProjectUser query()
 * @property int $id
 * @property int $project_id
 * @property int $user_id
 * @property int|null $marks
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ProjectUser whereCreatedAt($value)
 * @method static Builder|ProjectUser whereId($value)
 * @method static Builder|ProjectUser whereMarks($value)
 * @method static Builder|ProjectUser whereProjectId($value)
 * @method static Builder|ProjectUser whereUpdatedAt($value)
 * @method static Builder|ProjectUser whereUserId($value)
 * @mixin Eloquent
 */
class ProjectUser extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
      'project_id',
      'user_id',
      'marks',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
