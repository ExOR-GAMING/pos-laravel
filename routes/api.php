<?php

use App\Http\Controllers\API\V1\Auth\AuthenticatedSessionController;
use App\Http\Controllers\API\V1\Auth\RegisteredUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/v1/user', function (Request $request) {
    return new \App\Http\Resources\V1\User($request->user());
})->middleware('auth:sanctum');

Route::post('/v1/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');

Route::post('/v1/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');
    
Route::post('/v1/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum');