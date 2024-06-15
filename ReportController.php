<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Http\Requests\Report\StoreReportRequest;
use App\Models\Assignment;
use App\Models\AssignmentUser;
use App\Models\Classroom;
use App\Models\ClassroomUser;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\Quiz;
use App\Models\QuizUser;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param int $classroomId
     * @param int|null $userId
     * @return Response
     * @throws AuthorizationException
     */
    public function index(int $classroomId, int $userId = null): Response
    {
        $this->authorize(config('permission.defined.report.index.permission'));

        $classroom = Classroom::findOrFail($classroomId);

        if (Auth::user()->hasRole(RoleEnum::Student->value)) {
            $user = Auth::user();
        } else {
            $user = User::findOrFail($userId);
        }

        $classroomUser = ClassroomUser::whereClassroomId($classroomId)->whereUserId($user->id)->first();

        if(!$classroomUser)
            return abort(ResponseStatus::HTTP_NOT_FOUND, 'User has not joined selected classroom.');


        $data = [
            'assignment' => [
                'submitted' => AssignmentUser::whereUserId($user->id)
                    ->whereRelation('assignment', 'classroom_id', $classroom->id)
                    ->count(),
                'marked' => AssignmentUser::whereUserId($user->id)
                    ->whereRelation('assignment', 'classroom_id', $classroom->id)
                    ->whereNotNull('marks')
                    ->count(),
                'total' => Assignment::whereClassroomId($classroom->id)->count(),
                'obtained_marks' => AssignmentUser::whereUserId($user->id)
                    ->whereRelation('assignment', 'classroom_id', $classroom->id)
                    ->sum('marks'),
                'total_marks' => Assignment::whereClassroomId($classroom->id)->sum('marks'),
            ],
            'quiz' => [
                'submitted' => QuizUser::whereUserId($user->id)
                    ->whereRelation('quiz', 'classroom_id', $classroom->id)
                    ->count(),
                'marked' => QuizUser::whereUserId($user->id)
                    ->whereRelation('quiz', 'classroom_id', $classroom->id)
                    ->whereNotNull('marks')
                    ->count(),
                'total' => Quiz::whereClassroomId($classroom->id)->count(),
                'obtained_marks' => QuizUser::whereUserId($user->id)
                    ->whereRelation('quiz', 'classroom_id', $classroom->id)
                    ->sum('marks'),
                'total_marks' => Quiz::whereClassroomId($classroom->id)->sum('marks'),
            ],
            'project' => [
                'submitted' => ProjectUser::whereUserId($user->id)
                    ->whereRelation('project', 'classroom_id', $classroom->id)
                    ->count(),
                'marked' => ProjectUser::whereUserId($user->id)
                    ->whereRelation('project', 'classroom_id', $classroom->id)
                    ->whereNotNull('marks')
                    ->count(),
                'total' => Project::whereClassroomId($classroom->id)->count(),
                'obtained_marks' => ProjectUser::whereUserId($user->id)
                    ->whereRelation('project', 'classroom_id', $classroom->id)
                    ->sum('marks'),
                'total_marks' => Project::whereClassroomId($classroom->id)->sum('marks'),
            ],
            'mid_term' => [
                'submitted' => 'N/A',
                'marked' => 'N/A',
                'total' => 'N/A',
                'obtained_marks' => $classroomUser->mid_term_obtained_marks,
                'total_marks' => $classroomUser->mid_term_total_marks,
            ],
            'final_term' => [
                'submitted' => 'N/A',
                'marked' => 'N/A',
                'total' => 'N/A',
                'obtained_marks' => $classroomUser->final_term_obtained_marks,
                'total_marks' => $classroomUser->final_term_total_marks,
            ],
        ];


        return response($data, ResponseStatus::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Classroom $classroom
     * @param User $user
     * @param StoreReportRequest $request
     * @return Response
     * @throws AuthorizationException
     */
    public function store(Classroom $classroom, User $user, StoreReportRequest $request): Response
    {
        $this->authorize(config('permission.defined.report.store.permission'));

        $input = $request->validated();
        $classroomUser = ClassroomUser::whereClassroomId($classroom->id)->whereUserId($user->id)->first();

        if(!$classroomUser)
            return abort(ResponseStatus::HTTP_NOT_FOUND, 'User has not joined selected classroom.');

        $classroomUser->update($input);

        return response(null, ResponseStatus::HTTP_NO_CONTENT);
    }
}
