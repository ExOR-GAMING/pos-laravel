<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Your email already verified'], 422);
        }

        $cacheKey = 'email_verification_' . $user->id;

        $verification_code = random_int(100000, 999999);

        Cache::put($cacheKey, $verification_code, now()->addMinutes(5));

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification code sent']);
    }
}
