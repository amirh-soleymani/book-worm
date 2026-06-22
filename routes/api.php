<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function (Request $request) {
    dd('test application');
});

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->prefix('books')->group(function () {
    Route::post('{book}/add-to-library', [BookController::class, 'addToLibrary']);
    Route::post('{book}/open', [BookController::class, 'open']);
    Route::post('{book}/turn-page', [BookController::class, 'turnPage']);
});
