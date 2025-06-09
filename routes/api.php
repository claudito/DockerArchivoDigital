<?php

use App\Http\Controllers\DigitalFileController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group([

    'middleware' => ['api'],
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('me', [AuthController::class, 'me']);
    Route::post('create', [DigitalFileController::class, 'create'])->name('create');
    Route::post('create_temp', [DigitalFileController::class, 'createTemp'])->name('createTemp');
    Route::post('tracking', [DigitalFileController::class, 'tracking'])->name('tracking');
});
