<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
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

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     * @throws AuthorizationException
     */
    public function index(): Response
    {
        $this->authorize(config('permission.defined.dashboard.index.permission'));

        $data = [
            'teachers' => User::with("roles")->whereHas("roles", function ($q) {
                $q->whereIn("name", [RoleEnum::Teacher->value]);
            })->count(),
            'students' => User::with("roles")->whereHas("roles", function ($q) {
                $q->whereIn("name", [RoleEnum::Student->value]);
            })->count(),
            'classrooms' => Classroom::count(),
        ];

        return response($data, ResponseStatus::HTTP_OK);
    }
}
