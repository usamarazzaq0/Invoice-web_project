<?php

namespace App\Http\Controllers;

use App\Http\Requests\Lecture\StoreLectureRequest;
use App\Http\Requests\Lecture\UpdateLectureRequest;
use App\Http\Resources\LectureResource;
use App\Models\Lecture;
use App\Models\Classroom;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class LectureController extends Controller
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
        $this->authorize(config('permission.defined.lecture.index.permission'));

        $query = Lecture::where('classroom_id', $classroom->id)->orderBy('created_at', 'ASC');

        $resource = LectureResource::collection($query->get());

        return response($resource, ResponseStatus::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Classroom $classroom
     * @param StoreLectureRequest $request
     * @return Response
     * @throws AuthorizationException
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function store(Classroom $classroom, StoreLectureRequest $request): Response
    {
        $this->authorize(config('permission.defined.lecture.store.permission'));

        $input = $request->validated();
        $input['classroom_id'] = $classroom->id;

        $query = Lecture::create($input);
        $query->clearMediaCollection('lecture');
        $query->addMedia($request->file('document'))->toMediaCollection('lecture', 'lecture');
        $resource = LectureResource::make($query);

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
        $this->authorize(config('permission.defined.lecture.show.permission'));

        $query = Lecture::where('classroom_id', $classroom->id)->findOrFail($id);

        $resource = LectureResource::make($query);

        return response($resource, ResponseStatus::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Classroom $classroom
     * @param UpdateLectureRequest $request
     * @param int $id
     * @return Response
     * @throws AuthorizationException
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function update(Classroom $classroom, UpdateLectureRequest $request, int $id): Response
    {
        $this->authorize(config('permission.defined.lecture.update.permission'));

        $input = $request->validated();

        $query = Lecture::where('classroom_id', $classroom->id)->findOrFail($id);
        $query->update($input);

        if ($request->file('document') !== null) {
            $query->clearMediaCollection('lecture');
            $query->addMedia($request->file('document'))->toMediaCollection('lecture', 'lecture');
        }

        $resource = LectureResource::make($query);

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
        $this->authorize(config('permission.defined.lecture.destroy.permission'));

        $query = Lecture::where('classroom_id', $classroom->id)->findOrFail($id);
        $query->clearMediaCollection('lecture');
        $query->delete();

        return response(null, ResponseStatus::HTTP_NO_CONTENT);
    }
}
