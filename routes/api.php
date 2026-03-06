<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SerieController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/series', [SerieController::class, 'index']);
    Route::post('/series', [SerieController::class, 'store']);
    Route::delete('/series/{id}', [SerieController::class, 'destroy']);
    Route::put('/series/{id}/status', [SerieController::class, 'updateStatus']);
    Route::put('/seasons/{id}', [SerieController::class, 'updateSeason']);
    Route::get('/search', [SerieController::class, 'search']);
    Route::post('/series/{id}/seasons', [SerieController::class, 'addSeason']);
});
