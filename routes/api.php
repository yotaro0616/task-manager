<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
});

// 簡単なHello World API
Route::get('/hello', function () {
    return response()->json([
        'message' => 'Hello, World!',
    ]);
});

// パラメータを受け取るAPI
Route::get('/hello/{name}', function (string $name) {
    return response()->json([
        'message' => "Hello, {$name}!",
    ]);
});

// POSTリクエストを受け取るAPI
Route::post('/echo', function (Request $request) {
    return response()->json([
        'message' => $request->input('message'),
    ]);
});

// Route::apiResource('tasks', TaskController::class);

// Route::prefix('v1')->group(function () {
//     Route::apiResource('tasks', TaskController::class);
// });
Route::apiResource('users', UserController::class)->only(['index', 'store', 'show']);

// 認証不要のルート
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 認証が必要なルート
Route::middleware('auth:sanctum', 'throttle:60,1')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::apiResource('tasks', TaskController::class);
});
