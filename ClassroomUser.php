<?php

namespace App\Models;

use Database\Factories\ClassroomUserFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\ClassroomUser
 *
 * @property int $id
 * @property int $classroom_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Classroom $classroom
 * @property-read User $user
 * @method static ClassroomUserFactory factory(...$parameters)
 * @method static Builder|ClassroomUser newModelQuery()
 * @method static Builder|ClassroomUser newQuery()
 * @method static Builder|ClassroomUser query()
 * @method static Builder|ClassroomUser whereClassroomId($value)
 * @method static Builder|ClassroomUser whereCreatedAt($value)
 * @method static Builder|ClassroomUser whereId($value)
 * @method static Builder|ClassroomUser whereUpdatedAt($value)
 * @method static Builder|ClassroomUser whereUserId($value)
 * @mixin Eloquent
 * @property int|null $mid_term_obtained_marks
 * @property int|null $mid_term_total_marks
 * @property int|null $final_term_obtained_marks
 * @property int|null $final_term_total_marks
 * @method static Builder|ClassroomUser whereFinalTermObtainedMarks($value)
 * @method static Builder|ClassroomUser whereFinalTermTotalMarks($value)
 * @method static Builder|ClassroomUser whereMidTermObtainedMarks($value)
 * @method static Builder|ClassroomUser whereMidTermTotalMarks($value)
 */
class ClassroomUser extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'classroom_id',
        'user_id',
        'mid_term_obtained_marks',
        'mid_term_total_marks',
        'final_term_obtained_marks',
        'final_term_total_marks',
    ];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
