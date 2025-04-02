<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MeController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleRegistrationController;
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

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('mobile-login', 'mobileLogin');
});

Route::middleware('auth:sanctum', 'throttle:60,1')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', MeController::class);

    Route::get('/auth/user', [AuthController::class, 'user']);

    Route::get('/users', [UserController::class, 'index']);
    Route::prefix('/user')->group(function () {
        Route::put('/{id}', [UserController::class, 'update']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{user}', [UserController::class, 'show']);
        Route::delete('/{user}', [UserController::class, 'destroy']);
    });

    Route::get('/vehicles', [VehicleController::class, 'index']);
    Route::prefix('/vehicle')->group(function () {
        Route::put('/{id}', [VehicleController::class, 'update']);
        Route::post('/', [VehicleController::class, 'store']);
        Route::get('/{vehicle}', [VehicleController::class, 'show']);
        Route::delete('/{vehicle}', [VehicleController::class, 'destroy']);

        Route::get('/{userId}/user', [VehicleController::class, 'getVehiclesByUserId']);
    });

    Route::prefix('/vehicle-registration')->group(function () {
        Route::post('/', [VehicleRegistrationController::class, 'store']);
    });

    Route::get('/semesters', [SemesterController::class, 'index']);
    Route::prefix('/semester')->group(function () {
        Route::put('/{id}', [SemesterController::class, 'update']);
        Route::post('/', [SemesterController::class, 'store']);
        Route::get('/{semester}', [SemesterController::class, 'show']);
        Route::delete('/{semester}', [SemesterController::class, 'destroy']);
    });

    Route::get('/records', [RecordController::class, 'index']);
    Route::prefix('/record')->group(function () {
        Route::post('/scan-qr-code', [RecordController::class, 'scanQRCode']);
        Route::get('/vehicle/{vehicleId}', [RecordController::class, 'showRecordsWithVehicleId']);
    });
});
