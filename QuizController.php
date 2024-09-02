<?php

namespace App\Http\Controllers;

use App\Http\Requests\Quiz\MarkQuizRequest;
use App\Http\Requests\Quiz\StoreQuizRequest;
use App\Http\Requests\Quiz\SubmitQuizRequest;
use App\Http\Requests\Quiz\UpdateQuizRequest;
use App\Http\Resources\QuizResource;
use App\Models\Quiz;
use App\Models\QuizUser;
use App\Models\Classroom;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class QuizController extends Controller
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
        $this->authorize(config('permission.defined.quiz.index.permission'));

        $query = Quiz::where('classroom_id', $classroom->id)->orderBy('created_at', 'ASC');

        $resource = QuizResource::collection($query->get());

        return response($resource, ResponseStatus::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Classroom $classroom
     * @param StoreQuizRequest $request
     * @return Response
     * @throws AuthorizationException
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function store(Classroom $classroom, StoreQuizRequest $request): Response
    {
        $this->authorize(config('permission.defined.quiz.store.permission'));

        $input = $request->validated();
        $input['classroom_id'] = $classroom->id;

        $query = Quiz::create($input);
        $query->clearMediaCollection('quiz');
        $query->addMedia($request->file('document'))->toMediaCollection('quiz', 'quiz');
        $resource = QuizResource::make($query);

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
        $this->authorize(config('permission.defined.quiz.show.permission'));

        $query = Quiz::where('classroom_id', $classroom->id)->findOrFail($id);

        $resource = QuizResource::make($query);

        return response($resource, ResponseStatus::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Classroom $classroom
     * @param UpdateQuizRequest $request
     * @param int $id
     * @return Response
     * @throws AuthorizationException
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function update(Classroom $classroom, UpdateQuizRequest $request, int $id): Response
    {
        $this->authorize(config('permission.defined.quiz.update.permission'));

        $input = $request->validated();

        $query = Quiz::where('classroom_id', $classroom->id)->findOrFail($id);
        $query->update($input);

        if ($request->file('document') !== null) {
            $query->clearMediaCollection('quiz');
            $query->addMedia($request->file('document'))->toMediaCollection('quiz', 'quiz');
        }

        $resource = QuizResource::make($query);

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
        $this->authorize(config('permission.defined.quiz.destroy.permission'));

        $query = Quiz::where('classroom_id', $classroom->id)->findOrFail($id);
        $query->clearMediaCollection('quiz');
        $query->delete();

        return response(null, ResponseStatus::HTTP_NO_CONTENT);
    }

    /**
     * Submit the specified resource from storage.
     *
     * @param SubmitQuizRequest $request
     * @param int $id
     * @return Response
     * @throws AuthorizationException
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function submit(SubmitQuizRequest $request, int $id): Response
    {
        $this->authorize(config('permission.defined.quiz.submit.permission'));

        $quiz = Quiz::findOrFail($id);

        $query = QuizUser::where('quiz_id', $quiz->id)->where('user_id', Auth::user()->id)->first();

        if (!$query)
            $query = QuizUser::create([
                'quiz_id' => $id,
                'user_id' => Auth::user()->id
            ]);

        $query->clearMediaCollection('quizUser');
        $query->addMedia($request->file('document'))->toMediaCollection('quizUser', 'quizUser');

        return response(null, ResponseStatus::HTTP_NO_CONTENT);
    }

    /**
     * Mark the specified resource from storage.
     *
     * @param MarkQuizRequest $request
     * @param int $id
     * @return Response
     * @throws AuthorizationException
     */
    public function mark(MarkQuizRequest $request, int $id): Response
    {
        $this->authorize(config('permission.defined.quiz.mark.permission'));

        $quiz = Quiz::findOrFail($id);

        $input = $request->validated();

        $query = QuizUser::where('quiz_id', $quiz->id)->findOrFail($input['id']);

        $query->marks = $input['marks'];
        $query->save();

        return response(null, ResponseStatus::HTTP_NO_CONTENT);
    }
}
