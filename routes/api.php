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
    Route::post('tracking', [DigitalFileController::class, 'tracking'])->name('tracking');
    Route::post('update', [DigitalFileController::class, 'updateEstado'])->name('updateEstado');
    Route::post('copyFilesNetwork', [DigitalFileController::class, 'copyFilesNetwork'])->name('copyFilesNetwork');
    Route::post('sihce_download_json', [DigitalFileController::class, 'sihceDownloadJson'])->name('sihce_download_json');
});
