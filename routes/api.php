<?php

use App\Http\Controllers\Api\{
    CategoryController,
    GenreController,
    CastMemberController,
};
use Illuminate\Support\Facades\Route;

Route::get('/', function() {
    return response()->json(['message' => 'Video system']);
});

Route::apiResource('categories', CategoryController::class);
Route::apiResource('genre',      GenreController::class);
Route::apiResource('cast-member',      CastMemberController::class);
