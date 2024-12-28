<?php

use App\Http\Controllers\AnimeController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\MangaLinkController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::resource('animes', AnimeController::class);

Route::get('authors/get-options', [AuthorController::class, 'getOptions']);
Route::resource('authors', AuthorController::class);

Route::post('/manga-links', [MangaLinkController::class, 'store']);
Route::get('/manga-links/{id}', [MangaLinkController::class, 'show']);
Route::put('/manga-links/{id}', [MangaLinkController::class, 'update']);
Route::delete('/manga-links/{id}', [MangaLinkController::class, 'destroy']);
