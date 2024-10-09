<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\V1\User as UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;


class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming login request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $user = $request->user();

        $user->tokens->each(function ($value) {
            if (in_array('basic:full-access', $value->abilities)) {
                $value->delete();
            }
        });

        $token = $user->createToken($request->device_name, ['basic:full-access'])->plainTextToken;

        return response()->json(['token' => $token]);
    }

    /**
     * Handle an incoming logout request.
     */
    public function destroy(Request $request): JsonResponse
    {
        $isDeletionSuccess = $request->user()->currentAccessToken()->delete();

        if (!$isDeletionSuccess) {
            return response()->json(['message' => 'Error when logging out user'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([], 204);
    }
}
