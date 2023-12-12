<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AnswerSheetController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\GameLogsController;
use App\Http\Controllers\LeaderboardController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [UserController::class, 'store']);

Route::post('/login', [UserController::class, 'login']);

Route::get('/game', [AnswerSheetController::class, 'index']);
Route::get('/game-settings/index', [AnswerSheetController::class, 'gameSetting']);
Route::post('/game', [AnswerSheetController::class, 'game']);

Route::post('/game-settings/store', [AnswerSheetController::class, 'store']);
Route::post('/game-settings/show', [AnswerSheetController::class, 'show']);
Route::post('/game-settings/update', [AnswerSheetController::class, 'update']);
Route::post('/game-settings/delete', [AnswerSheetController::class, 'delete']);

Route::get('/student/create', [StudentController::class, 'store']);
Route::post('/student/validation', [StudentController::class, 'student']);
Route::post('/student/update', [StudentController::class, 'update']);

Route::get('/teacher', [GameLogsController::class, 'index']);
Route::post('/logs/save', [GameLogsController::class, 'store']);
Route::post('/logs/check', [GameLogsController::class, 'show']);

Route::prefix('leaderboard')->controller(LeaderboardController::class)->group(function () {
    Route::get('/index', 'index');
});