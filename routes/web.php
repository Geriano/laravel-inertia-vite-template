<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::prefix('/superuser')->name('superuser.')->group(function () {
        Route::resource('permission', App\Http\Controllers\Superuser\PermissionController::class)->only([
            'index', 'store', 'update', 'destroy',
        ]);

        Route::resource('role', App\Http\Controllers\Superuser\RoleController::class)->only([
            'index', 'store', 'update', 'destroy',
        ]);

        Route::patch('/role/{role}/detach/{permission}', [App\Http\Controllers\Superuser\RoleController::class, 'detach'])->name('role.detach');

        Route::resource('user', App\Http\Controllers\Superuser\UserController::class)->only([
            'index', 'store', 'update', 'destroy',
        ]);

        Route::prefix('/user/{user}')->name('user.')->controller(App\Http\Controllers\Superuser\UserController::class)->group(function () {
            Route::patch('/role/{role}/detach', 'detachRole')->name('role.detach');
            Route::patch('/permission/{permission}/detach', 'detachPermission')->name('permission.detach');
        });

        Route::resource('menu', App\Http\Controllers\Superuser\MenuController::class)->only([
            'index', 'store', 'update', 'destroy',
        ]);

        Route::prefix('/menu/{menu}')->name('menu.')->controller(App\Http\Controllers\Superuser\MenuController::class)->group(function () {
            Route::patch('/up', 'up')->name('up');
            Route::patch('/down', 'down')->name('down');
            Route::patch('/left', 'left')->name('left');
            Route::patch('/right', 'right')->name('right');
        });
    });
});