<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SerieController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/search', [SerieController::class, 'search']);
Route::post('/series', [SerieController::class, 'store']);
Route::get('/series', [SerieController::class, 'index']);
Route::put('/seasons/{id}', [SerieController::class, 'updateSeason']);
Route::delete('/series/{id}', [SerieController::class, 'destroy']);

