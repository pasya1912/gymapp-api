<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LatihanController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\UsersController as AdminUsersController;
use App\Http\Controllers\Api\Admin\LatihanController as AdminLatihanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('login', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['middleware' => ['user']], function () {
        Route::get('user', [UserController::class, 'me'])->name('profile');
        Route::post('user', [UserController::class, 'update'])->name('profile.update');
        Route::post('latihan', [LatihanController::class, 'add'])->name('latihan.add');
        Route::get('latihan', [LatihanController::class, 'get'])->name('latihan.get');
    });
    Route::group(['middleware' => ['admin']], function () {
        Route::post('admin/users/add', [AuthController::class, 'register'])->name('register');

        Route::get('admin/latihan', [AdminLatihanController::class, 'get'])->name('latihan.getAll');

        Route::get('admin/dashboard', [DashboardController::class, 'get'])->name('admin.dashboard');
        Route::get('admin/users', [AdminUsersController::class, 'get'])->name('admin.users');
        Route::post('admin/users/{id}', [AdminUsersController::class, 'update'])->where('id', '[0-9]+')->name('admin.users.update');
    });
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
});
