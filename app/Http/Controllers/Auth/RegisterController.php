<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Fortify\Contracts\RegisterResponse;
use App\Models\User;

class RegisterController extends Controller implements RegisterResponse
{
    /**
     * Override Fortify's user registration logic
     * to utilize Sanctum token generator
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => 'Your account was successfully created',
            'data' => [
                'user' => $user = User::findByEmail($request->email),
                'token' => $user->createToken($request->email)->plainTextToken
            ],
        ], 201);
    }
}
