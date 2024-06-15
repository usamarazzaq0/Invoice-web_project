<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     * @throws AuthorizationException
     */
    public function index(): Response
    {
        $this->authorize(config('permission.defined.user.index.permission'));

        $query = User::orderBy('created_at', 'ASC');
        $resource = UserResource::collection($query->get());

        return response($resource, ResponseStatus::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserRequest $request
     * @return Response
     * @throws AuthorizationException
     */
    public function store(StoreUserRequest $request): Response
    {
        $this->authorize(config('permission.defined.user.store.permission'));

        $input = $request->validated();
        $input['password'] = bcrypt($input['password']);
        $input['remember_token'] = Str::random(10);

        $query = User::create($input);
        $query->assignRole([$input['role']]);
        $resource = UserResource::make($query);

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
        $this->authorize(config('permission.defined.user.show.permission'));

        $query = User::findOrFail($id);
        $resource = UserResource::make($query);

        return response($resource, ResponseStatus::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @param int $id
     * @return Response
     * @throws AuthorizationException
     */
    public function update(UpdateUserRequest $request, int $id): Response
    {
        $this->authorize(config('permission.defined.user.update.permission'));

        $input = $request->validated();

        if ($request->has('password'))
          $input['password'] = bcrypt($input['password']);

        $query = User::findOrFail($id);
        $query->update($input);
        $resource = UserResource::make($query);

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
        $this->authorize(config('permission.defined.user.destroy.permission'));

        $query = User::findOrFail($id);
        $query->delete();

        return response(null, ResponseStatus::HTTP_NO_CONTENT);
    }
}
