<?php

use App\Http\Controllers\API\TranslationController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);


    //Translation routes
    Route::get('translations/export', [TranslationController::class, 'export'])->name('translations.export');
    Route::resource('translations', TranslationController::class);

});


