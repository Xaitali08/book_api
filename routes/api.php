<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('register/',[AuthController::class,'register']);
Route::post('login/',[AuthController::class,'login']);
Route::get('show/{id}',[AuthController::class,'show']);
Route::apiResource('book/',BookController::class);

