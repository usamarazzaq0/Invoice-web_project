<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Http\Requests\Classroom\JoinClassroomRequest;
use App\Http\Requests\Classroom\LeaveClassroomRequest;
use App\Http\Requests\Classroom\StoreClassroomRequest;
use App\Http\Requests\Classroom\UpdateClassroomRequest;
use App\Http\Resources\ClassroomResource;
use App\Models\Classroom;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     * @throws AuthorizationException
     */
    public function index(): Response
    {
        $this->authorize(config('permission.defined.classroom.index.permission'));

        $query = Classroom::orderBy('created_at', 'ASC');

        if (Auth::user()->hasRole(RoleEnum::Teacher->value)) {
            $query->where('user_id', Auth::user()->id);
        } else if (Auth::user()->hasRole(RoleEnum::Student->value)) {
            $query->whereRelation('classroomUsers', 'user_id', Auth::user()->id);
        }

        $resource = ClassroomResource::collection($query->get());

        return response($resource, ResponseStatus::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreClassroomRequest $request
     * @return Response
     * @throws AuthorizationException
     */
    public function store(StoreClassroomRequest $request): Response
    {
        $this->authorize(config('permission.defined.classroom.store.permission'));

        $input = $request->validated();
        $input['user_id'] = Auth::user()->id;
        $input['uid'] = generateUniqueCode(Classroom::class, 'uid');

        $query = Classroom::create($input);
        $resource = ClassroomResource::make($query);

        return response($resource, ResponseStatus::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     * @throws AuthorizationException
     */
    public function show(int $id): Response
    {
        $this->authorize(config('permission.defined.classroom.show.permission'));

        if (Auth::user()->hasRole(RoleEnum::Teacher->value)) {
            $query = Classroom::where('user_id', Auth::user()->id)->findOrFail($id);
        } else if (Auth::user()->hasRole(RoleEnum::Student->value)) {
            $query = Classroom::whereRelation('classroomUsers', 'user_id', Auth::user()->id)->findOrFail($id);
        } else {
            $query = Classroom::findOrFail($id);
        }

        $resource = ClassroomResource::make($query);

        return response($resource, ResponseStatus::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateClassroomRequest $request
     * @param int $id
     * @return Response
     * @throws AuthorizationException
     */
    public function update(UpdateClassroomRequest $request, int $id): Response
    {
        $this->authorize(config('permission.defined.classroom.update.permission'));

        $input = $request->validated();

        $query = Classroom::findOrFail($id);
        $query->update($input);
        $resource = ClassroomResource::make($query);

        return response($resource, ResponseStatus::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     * @throws AuthorizationException
     */
    public function destroy(int $id): Response
    {
        $this->authorize(config('permission.defined.classroom.destroy.permission'));

        $query = Classroom::findOrFail($id);
        $query->delete();

        return response(null, ResponseStatus::HTTP_NO_CONTENT);
    }

    /**
     * Pluck a listing of the resource.
     *
     * @return Response
     * @throws AuthorizationException
     */
    public function list(): Response
    {
        $this->authorize(config('permission.defined.classroom.list.permission'));

        $query = Classroom::orderBy('created_at', 'ASC');

        if (Auth::user()->hasRole(RoleEnum::Teacher->value)) {
            $query->where('user_id', Auth::user()->id);
        } else if (Auth::user()->hasRole(RoleEnum::Student->value)) {
            $query->whereRelation('classroomUsers', 'user_id', Auth::user()->id);
        }

        $list = $query->pluck('name', 'id');

        return response($list, ResponseStatus::HTTP_OK);
    }

    /**
     * Pluck a listing of the resource.
     *
     * @param int $id
     * @return Response
     * @throws AuthorizationException
     */
    public function listClassroomUsers(int $id): Response
    {
        $this->authorize(config('permission.defined.classroom.listClassroomUsers.permission'));

        $query = Classroom::findOrFail($id);
        $query = $query->classroomUsers()
            ->join('users', 'classroom_users.user_id', '=', 'users.id')
            ->pluck('users.email', 'users.id')
            ->all();

        return response($query, ResponseStatus::HTTP_OK);
    }

    /**
     * Join a resource in storage.
     *
     * @param JoinClassroomRequest $request
     * @return Response
     * @throws AuthorizationException
     */
    public function join(JoinClassroomRequest $request): Response
    {
        $this->authorize(config('permission.defined.classroom.join.permission'));

        $input = $request->validated();
        $query = Classroom::where('uid', $input['uid'])->first();

        if ($query->classroomUsers()->where('user_id', Auth::user()->id)->exists())
            return abort(ResponseStatus::HTTP_CONFLICT, 'Classroom already joined.');

        $query->classroomUsers()->create([
            'user_id' => Auth::user()->id
        ]);

        return response(null, ResponseStatus::HTTP_NO_CONTENT);
    }

    /**
     * Leave a resource in storage.
     *
     * @param LeaveClassroomRequest $request
     * @return Response
     * @throws AuthorizationException
     */
    public function leave(LeaveClassroomRequest $request): Response
    {
        $this->authorize(config('permission.defined.classroom.leave.permission'));

        $input = $request->validated();
        $query = Classroom::findOrFail($input['id']);

        if (!$query->classroomUsers()->where('user_id', Auth::user()->id)->exists())
            return abort(ResponseStatus::HTTP_NOT_FOUND, 'Classroom not joined.');

        $query->classroomUsers()->where('user_id', Auth::user()->id)->delete();

        return response(null, ResponseStatus::HTTP_NO_CONTENT);
    }
}
