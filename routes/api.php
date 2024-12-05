<?php

use App\Http\Controllers\Api\MapController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'getUser']);
    Route::put('/user', [UserController::class, 'updateUser']);


    Route::get('/maps', [MapController::class, 'index']);
    Route::get('/maps/{id}', [MapController::class, 'show']);
    Route::post('/maps', [MapController::class, 'store']);
    Route::put('/maps/{id}', [MapController::class, 'update']);
    Route::delete('/maps/{id}', [MapController::class, 'destroy']);


    Route::post('/incrementUserPoints', [MapController::class, 'incrementUserPoints']);
});