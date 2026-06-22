<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class AuthController extends Controller
{
    public function login(LoginRequest $loginRequest): JsonResponse
    {
        $validated = $loginRequest->validated();

        if (! Auth::attempt($validated)) {

            return Response::failure(
                message: 'Invalid credentials',
                status: 401
            );
        }

        $user = Auth::user();

        $token = $user->createToken('auth-token')->plainTextToken;

        return Response::success(
            data: [
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
            message: 'You are logged in successfully.',
        );
    }
}
