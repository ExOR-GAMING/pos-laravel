<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'verification_code' => ['string', 'required']
        ]);

        $user = $request->user();
        $cachedCode = Cache::get('email_verification_' . $user->id);

        if ($user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'verification_code' => ['Your email already verified'],
            ]);
        }

        if ($cachedCode && ($cachedCode == $request->verification_code)) {
            $user->markEmailAsVerified();

            event(new Verified($user));

            Cache::forget('email_verification_' . $user->id);

            return response()->json(['message' => 'Email verified'], 200);
        }

        throw ValidationException::withMessages([
            'verification_code' => ['Invalid code'],
        ]);
    }
}
