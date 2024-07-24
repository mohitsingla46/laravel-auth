<?php

use App\Http\Controllers\apis\AuthController;
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

Route::post('signup', [AuthController::class, 'signup']);
Route::post('signin', [AuthController::class, 'signin']);

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyAccount'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verify/resend', [AuthController::class, 'resendVerificationEmail'])
    ->middleware(['auth:api', 'throttle:6,1'])
    ->name('verification.send');

Route::group(['middleware' => ['auth:api','verified']], function () {
    Route::get('profile', [AuthController::class, 'profile']);
});
