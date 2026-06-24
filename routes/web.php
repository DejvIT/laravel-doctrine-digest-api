<?php

use App\Http\Controllers\ArticleCategoryController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SubscriberController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'getVersion']);

Route::controller(AuthController::class)->group(function () {
    Route::post('/auth/login', 'login');
    Route::get('/auth/me', 'me')->middleware('jwt');
});

Route::middleware('jwt')->group(function () {
    Route::controller(ArticleController::class)->group(function () {
        Route::get('/articles', 'index');
        Route::post('/articles', 'store');
        Route::get('/articles/{uuid}', 'show');
        Route::put('/articles/{uuid}', 'update');
        Route::delete('/articles/{uuid}', 'destroy');
    });

    Route::controller(ArticleCategoryController::class)->group(function () {
        Route::get('/article-categories', 'index');
        Route::get('/article-categories/{uuid}', 'show');
    });

    Route::controller(SubscriberController::class)->group(function () {
        Route::get('/subscribers', 'index');
    });
});
