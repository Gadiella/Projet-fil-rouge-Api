<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/store', [AuthController::class, 'store']);
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::put('/users/{id}', [AuthController::class, 'update']);
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::delete('users/{id}', [AuthController::class, 'destroy']);
Route::post('send-otp', [AuthController::class, 'sendOtpCode']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtpCode']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/users/excluding-admin', [AuthController::class, 'getUsersExcludingAdmin']);
Route::post('/change-password', [AuthController::class, 'changePassword']);
