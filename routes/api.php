<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware(["auth:api"])->group(function () {
    Route::get("user", [UserController::class, 'profile']);
    Route::get("logout", [UserController::class, 'logout']);

    Route::get("foo", [UserController::class, 'foo'])->middleware([IsAdmin::class]);

    Route::resource("questions", QuestionController::class);
    Route::resource("categories", CategoryController::class);

    Route::get("questions/{questionId}/categories", [CategoryController::class, 'getFromQuestion']);
    Route::post("questions/{questionId}/categories", [CategoryController::class, 'setToQuestion']);
    Route::delete("questions/{questionId}/categories/{categoryId}", [CategoryController::class, 'removeFromQuestion']);

    Route::resource("questions/{questionId}/answers", AnswerController::class);

    Route::get('user/questions', [UserController::class, 'questions']);
    Route::get('user/answers', [UserController::class, 'answers']);
});
