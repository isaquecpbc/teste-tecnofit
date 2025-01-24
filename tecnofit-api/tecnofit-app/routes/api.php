<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\PersonalRecordController;

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

// Schedules
// Route::resource('schedules', ScheduleController::class);
// Route::get('schedules/get_all_by_user/list', [ScheduleController::class, 'get_all_by_user']);
// Route::post('schedules/{id}/update_status', [ScheduleController::class, 'update_status']);
// Users
Route::resource('users', UserController::class);
// Movements
Route::resource('movements', MovementController::class);
// Personal Records
Route::resource('personal-records', PersonalRecordController::class);
Route::get('personal-records/ranking/list', [PersonalRecordController::class, 'ranking']);