<?php

use App\Enums\Role;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\RequestController;
use App\Http\Controllers\API\UserController;
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

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout')->middleware('auth:api');
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('users')->controller(UserController::class)
        ->middleware('role:' . Role::MAKER->value . '|' . Role::CHECKER->value)
        ->group(function () {
            Route::get('', 'index');
            Route::post('create', 'create');
            Route::post('{user}/update', 'update');
            Route::post('{user}/delete', 'delete');
        });

    Route::prefix('requests')->controller(RequestController::class)->group(function () {
        Route::get('', 'index')
            ->middleware('role:' . Role::MAKER->value . '|' . Role::CHECKER->value);
        Route::middleware('role:' . Role::CHECKER->value)->group(function () {
            Route::post('{request}/approve', 'approve');
            Route::post('{request}/decline', 'decline');
        });
    });
});
