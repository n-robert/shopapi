<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthFormRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends ShopApiController
{
    /**
     * @param AuthFormRequest $request
     * @return JsonResponse
     */
    public function register(AuthFormRequest $request): JsonResponse
    {
        try {
            User::create(
                [
                    ...$request->only('name', 'email'),
                    'password' => bcrypt($request->password),
                ]
            );

            return $this->response(
                [
                    'message'  => 'You were successfully registered.',
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'password' => $request->password,
                ]
            );
        } catch (\Exception $exception) {
            return $this->response(
                [
                    'message' => 'Registration failed.',
                    'errors'   => $exception->getMessage(),
                ],
                401
            );
        }
    }

    /**
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
        $credentials = $this->request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return $this->response(
                [
                    'message' => 'You cannot sign in with those credentials. Are you sure you are already registered?',
                    'errors'  => 'Unauthorised',
                ],
                401
            );
        }

        $token = Auth::user()->createToken(config('app.name'));
        $token->token->expires_at = $this->request->remember_me ?
            Carbon::now()->addMonth() :
            Carbon::now()->addDay();

        $token->token->save();

        return $this->response(
            [
                'token'      => $token->accessToken,
                'expires_at' => Carbon::parse($token->token->expires_at)->toDateTimeString(),
            ]
        );
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->request->user()->token()->revoke();

        return $this->response('You are successfully logged out');
    }

    /**
     * @return JsonResponse
     */
    public function user(): JsonResponse
    {
        return $this->response($this->request->user());
    }
}
