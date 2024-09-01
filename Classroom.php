<?php

namespace App\Models;

use Database\Factories\ClassroomFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Classroom
 *
 * @property int $id
 * @property int $user_id
 * @property string $uid
 * @property string $name
 * @property string $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|ClassroomUser[] $classroomUsers
 * @property-read int|null $classroom_users_count
 * @property-read User $user
 * @method static ClassroomFactory factory(...$parameters)
 * @method static Builder|Classroom newModelQuery()
 * @method static Builder|Classroom newQuery()
 * @method static Builder|Classroom query()
 * @method static Builder|Classroom whereCreatedAt($value)
 * @method static Builder|Classroom whereDescription($value)
 * @method static Builder|Classroom whereId($value)
 * @method static Builder|Classroom whereName($value)
 * @method static Builder|Classroom whereUid($value)
 * @method static Builder|Classroom whereUpdatedAt($value)
 * @method static Builder|Classroom whereUserId($value)
 * @mixin Eloquent
 */
class Classroom extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
      'user_id',
      'uid',
      'name',
      'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classroomUsers(): HasMany
    {
        return $this->hasMany(ClassroomUser::class);
    }
}
