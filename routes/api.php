<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('/v1')->name('api.')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', fn () => request()->user())->name('user');
});

Route::prefix('/translation/{locale?}')->name('api.translation.')->controller(App\Http\Controllers\TranslationController::class)->group(function () {
    Route::get('/', 'get')->name('get');
    Route::post('/', 'register')->name('register');
});