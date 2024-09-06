<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginAuthRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class AuthController extends Controller
{
    /**
     * Login the resource.
     *
     * @param LoginAuthRequest $request
     * @return Response
     */
    public function login(LoginAuthRequest $request): Response
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $user['token'] = $user->createToken('user')->accessToken;

            return response(UserResource::make($user), ResponseStatus::HTTP_OK);
        }

        return abort(ResponseStatus::HTTP_FORBIDDEN, 'Unauthorised.');
    }
}
