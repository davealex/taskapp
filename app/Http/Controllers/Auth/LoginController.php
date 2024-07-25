<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Fortify\Contracts\LoginResponse;

class LoginController extends Controller implements LoginResponse
{
    /**
     * Override Fortify's user login logic
     * to utilize Sanctum token generator
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request): \Illuminate\Http\JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        return response()->json([
            'message' => 'Your login attempt was successful',
            'data' => [
                'token' => $user->createToken($request->email)->plainTextToken
            ],
        ]);
    }
}
