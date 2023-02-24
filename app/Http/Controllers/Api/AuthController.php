<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthFormRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
    /**
     * @param AuthFormRequest $request
     * @return JsonResponse
     */
    public function register(AuthFormRequest $request): JsonResponse
    {
        try {
            User::create(array_merge(
                $request->only('name', 'email'),
                [
                    'password' => bcrypt($request->password),
                ]
            ));
            $message = 'You were successfully registered.';
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
        }

        return $this->response($message);
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
                    'message' => 'You cannot sign with those credentials',
                    'errors' => 'Unauthorised',
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
                'token' => $token->accessToken,
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
