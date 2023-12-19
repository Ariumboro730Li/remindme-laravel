<?php

use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\ReminderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::post('session', [LoginController::class, 'login']);
Route::put('session', [LoginController::class, 'refreshSession']);
Route::get('test', [LoginController::class, 'test']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::prefix('reminders')->group(function () {
        Route::get('/', [ReminderController::class, 'fetch'])->name('get.reminder');
        Route::post('/', [ReminderController::class, 'store'])->name('post.reminder');
        Route::get('/{id}', [ReminderController::class, 'fetchById'])->name('get.reminder-by-id');
        Route::put('/{id}', [ReminderController::class, 'update'])->name('put.reminder-by-id');
        Route::delete('/{id}', [ReminderController::class, 'delete'])->name('delete.reminder-by-id');
    });
});
