<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('auth:sanctum')->post('verify', [\App\Http\Controllers\VerificationController::class, 'verify'])->name('verify');

Route::post('/auth/register', [\App\Http\Controllers\AuthController::class, 'createUser']);
Route::post('/auth/login', [\App\Http\Controllers\AuthController::class, 'loginUser']);