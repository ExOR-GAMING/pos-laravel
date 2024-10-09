<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class NewPasswordController extends Controller
{
    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::firstWhere('email', $request->email);

        if ($user) {
            $cachedToken = Cache::get('password_reset_token_' . $user->id);

            if ($cachedToken && ($cachedToken == $request->token)) {
                $user->forceFill([
                    'password' => Hash::make($request->string('password')),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));

                Cache::forget('password_reset_token_' . $user->id);

                return response()->json(['message' => 'Password updated']);
            } else {
                throw ValidationException::withMessages([
                    'token' => ['Invalid token'],
                ]);
            }
        }

        throw ValidationException::withMessages([
            'token' => ['Account not found'],
        ]);
    }
}
