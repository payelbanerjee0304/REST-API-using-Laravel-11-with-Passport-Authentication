<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('create-user',[UserController::class,'createUser']);

Route::put('update-user/{id}',[UserController::class,'updateUser']);
Route::delete('delete-user/{id}',[UserController::class,'deleteUser']);

Route::post('login',[UserController::class,'login']);

Route::get('unauthenticate',[UserController::class,'unauthenticate'])->name('unauthenticate');

// Secure routes within auth middleware
Route::middleware('auth:api')->group (function() {
    Route::get('get-user',[UserController::class,'getUsers']);
    Route::get('get-user-detail/{id}',[UserController::class,'getUserDetail']);
    Route::post('logout',[UserController::class,'logout']);
});
