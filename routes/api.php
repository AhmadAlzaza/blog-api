<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TagController;
use App\Models\Tag;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::apiResource('post',PostController::class)->only(['show','index']);
    Route::get('/tags',[TagController::class,'index']);
    Route::apiResource('posts.comments', CommentController::class)->only(['index', 'show']);
    Route::apiResource('posts.tags',TagController::class)->only(['index','show']);
    Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('post',PostController::class)->only(['store','update','destroy']);
    Route::apiResource('posts.comments', CommentController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('posts.tags', TagController::class)->only(['store', 'update', 'destroy']);

});
