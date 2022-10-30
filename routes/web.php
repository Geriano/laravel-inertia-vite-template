<?php

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
        ])->middleware(['permission:read permission']);

        Route::resource('role', App\Http\Controllers\Superuser\RoleController::class)->only([
            'index', 'store', 'update', 'destroy',
        ])->middleware(['permission:read role']);

        Route::patch('/role/{role}/detach/{permission}', [App\Http\Controllers\Superuser\RoleController::class, 'detach'])->name('role.detach')->middleware(['permission:update role']);

        Route::resource('user', App\Http\Controllers\Superuser\UserController::class)->only([
            'index', 'store', 'update', 'destroy',
        ])->middleware(['permission:read user']);

        Route::prefix('/user/{user}')->name('user.')->controller(App\Http\Controllers\Superuser\UserController::class)->middleware(['permission:update user'])->group(function () {
            Route::patch('/role/{role}/detach', 'detachRole')->name('role.detach');
            Route::patch('/permission/{permission}/detach', 'detachPermission')->name('permission.detach');
        });

        Route::patch('/menu/save', [App\Http\Controllers\Superuser\MenuController::class, 'save'])->name('menu.save')->middleware(['permission:update menu']);
        Route::resource('menu', App\Http\Controllers\Superuser\MenuController::class)->only([
            'index', 'store', 'update', 'destroy',
        ])->middleware(['permission:read menu']);
        Route::get('/menu/{menu}/counter', [App\Http\Controllers\Superuser\MenuController::class, 'counter'])->name('menu.counter');

        Route::prefix('/translation')->name('translation.')->controller(App\Http\Controllers\TranslationController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::patch('/', 'update')->name('update');
        });
        
        Route::get('/activity/login', [App\Http\Controllers\ActivityController::class, 'login'])->name('activity.login');

        Route::get('/user/{user}/menu', fn (App\Models\User $user) => $user->menus())->name('user.menu');
        Route::get('/permission/get', [App\Http\Controllers\Superuser\PermissionController::class, 'get'])->name('permission');
        Route::get('/role/get', [App\Http\Controllers\Superuser\RoleController::class, 'get'])->name('role');
        Route::post('/role/paginate', [App\Http\Controllers\Superuser\RoleController::class, 'paginate'])->name('role.paginate');
        Route::post('/user/paginate', [App\Http\Controllers\Superuser\UserController::class, 'paginate'])->name('user.paginate');
        Route::post('/activity/login', [App\Http\Controllers\ActivityController::class, 'logins'])->name('activity.login');
        Route::get('/menu/get', [App\Http\Controllers\Superuser\MenuController::class, 'get'])->name('menu');
    });
});