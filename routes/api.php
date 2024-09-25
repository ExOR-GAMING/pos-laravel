<?php

use App\Http\Controllers\API\V1\Auth\RegisteredUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/v1/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest')
    ->name('api.register');
