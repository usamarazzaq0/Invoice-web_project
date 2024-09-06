<?php

namespace App\Http\Controllers;

use App\Http\Requests\Assignment\MarkAssignmentRequest;
use App\Http\Requests\Assignment\StoreAssignmentRequest;
use App\Http\Requests\Assignment\SubmitAssignmentRequest;
use App\Http\Requests\Assignment\UpdateAssignmentRequest;
use App\Http\Resources\AssignmentResource;
use App\Models\Assignment;
use App\Models\AssignmentUser;
use App\Models\Classroom;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Classroom $classroom
     * @return Response
     * @throws AuthorizationException
     */
    public function index(Classroom $classroom): Response
    {
        $this->authorize(config('permission.defined.assignment.index.permission'));

        $query = Assignment::where('classroom_id', $classroom->id)->orderBy('created_at', 'ASC');

        $resource = AssignmentResource::collection($query->get());

        return response($resource, ResponseStatus::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Classroom $classroom
     * @param StoreAssignmentRequest $request
     * @return Response
     * @throws AuthorizationException
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function store(Classroom $classroom, StoreAssignmentRequest $request): Response
    {
        $this->authorize(config('permission.defined.assignment.store.permission'));

        $input = $request->validated();
        $input['classroom_id'] = $classroom->id;

        $query = Assignment::create($input);
        $query->clearMediaCollection('assignment');
        $query->addMedia($request->file('document'))->toMediaCollection('assignment', 'assignment');
        $resource = AssignmentResource::make($query);

        return response($resource, ResponseStatus::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param Classroom $classroom
     * @param int $id
     * @return Response
     * @throws AuthorizationException
     */
    public function show(Classroom $classroom, int $id): Response
    {
        $this->authorize(config('permission.defined.assignment.show.permission'));

        $query = Assignment::where('classroom_id', $classroom->id)->findOrFail($id);

        $resource = AssignmentResource::make($query);

        return response($resource, ResponseStatus::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Classroom $classroom
     * @param UpdateAssignmentRequest $request
     * @param int $id
     * @return Response
     * @throws AuthorizationException
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function update(Classroom $classroom, UpdateAssignmentRequest $request, int $id): Response
    {
        $this->authorize(config('permission.defined.assignment.update.permission'));

        $input = $request->validated();

        $query = Assignment::where('classroom_id', $classroom->id)->findOrFail($id);
        $query->update($input);

        if ($request->file('document') !== null) {
            $query->clearMediaCollection('assignment');
            $query->addMedia($request->file('document'))->toMediaCollection('assignment', 'assignment');
        }

        $resource = AssignmentResource::make($query);

        return response($resource, ResponseStatus::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Classroom $classroom
     * @param int $id
     * @return Response
     * @throws AuthorizationException
     */
    public function destroy(Classroom $classroom, int $id): Response
    {
        $this->authorize(config('permission.defined.assignment.destroy.permission'));

        $query = Assignment::where('classroom_id', $classroom->id)->findOrFail($id);
        $query->clearMediaCollection('assignment');
        $query->delete();

        return response(null, ResponseStatus::HTTP_NO_CONTENT);
    }

    /**
     * Submit the specified resource from storage.
     *
     * @param SubmitAssignmentRequest $request
     * @param int $id
     * @return Response
     * @throws AuthorizationException
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function submit(SubmitAssignmentRequest $request, int $id): Response
    {
        $this->authorize(config('permission.defined.assignment.submit.permission'));

        $assignment = Assignment::findOrFail($id);

        $query = AssignmentUser::where('assignment_id', $assignment->id)->where('user_id', Auth::user()->id)->first();

        if (!$query)
            $query = AssignmentUser::create([
                'assignment_id' => $id,
                'user_id' => Auth::user()->id
            ]);

        $query->clearMediaCollection('assignmentUser');
        $query->addMedia($request->file('document'))->toMediaCollection('assignmentUser', 'assignmentUser');

        return response(null, ResponseStatus::HTTP_NO_CONTENT);
    }

    /**
     * Mark the specified resource from storage.
     *
     * @param MarkAssignmentRequest $request
     * @param int $id
     * @return Response
     * @throws AuthorizationException
     */
    public function mark(MarkAssignmentRequest $request, int $id): Response
    {
        $this->authorize(config('permission.defined.assignment.mark.permission'));

        $assignment = Assignment::findOrFail($id);

        $input = $request->validated();

        $query = AssignmentUser::where('assignment_id', $assignment->id)->findOrFail($input['id']);

        $query->marks = $input['marks'];
        $query->save();

        return response(null, ResponseStatus::HTTP_NO_CONTENT);
    }
}
