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

Route::prefix('/v1')->name('api.v1.')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/user/{user}/menu', fn (App\Models\User $user) => $user->menus())->name('user.menu');

    Route::name('superuser.')->group(function () {
        Route::get('/superuser/permission', [App\Http\Controllers\Superuser\PermissionController::class, 'get'])->name('permission');
        Route::get('/superuser/role', [App\Http\Controllers\Superuser\RoleController::class, 'get'])->name('role');
        Route::post('/superuser/role/paginate', [App\Http\Controllers\Superuser\RoleController::class, 'paginate'])->name('role.paginate');
        Route::post('/superuser/user/paginate', [App\Http\Controllers\Superuser\UserController::class, 'paginate'])->name('user.paginate');
        Route::post('/superuser/activity/login', [App\Http\Controllers\ActivityController::class, 'logins'])->name('activity.login');
        Route::get('/superuser/menu', [App\Http\Controllers\Superuser\MenuController::class, 'get'])->name('menu');
    });

    Route::get('/user', fn () => request()->user());
});