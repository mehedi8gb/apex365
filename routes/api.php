<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\WithdrawController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\RefreshTokenMiddleware;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

Route::group(['prefix' => 'auth'], function () {
    // Authentication routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Password management
    Route::post('forget', [AuthController::class, 'forget']);
    Route::post('validate', [AuthController::class, 'validateCode']);
    Route::post('reset', [AuthController::class, 'resetPassword']);

    // Token management
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware([RefreshTokenMiddleware::class]);
    Route::post('logout', [AuthController::class, 'logout'])->middleware([JwtMiddleware::class]);
});

Route::middleware([JwtMiddleware::class])->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('image/upload', [ResourceController::class, 'uploadImage']);
    Route::apiResource('users', UserController::class);


        Route::get('/referrals', [ReferralController::class, 'index']); // All referrals
        Route::get('/referrals/{userId}', [ReferralController::class, 'show']); // Specific user referrals
        Route::get('/referrals/tree/{userId}', [ReferralController::class, 'getReferralNodes']); // Specific user referrals

        Route::get('/transactions', [TransactionController::class, 'index']); // All transactions
        Route::get('/transactions/{userId}', [TransactionController::class, 'show']); // Specific user transactions

        Route::get('/commissions', [CommissionController::class, 'index']); // All commissions
        Route::get('/commissions/{userId}', [CommissionController::class, 'show']); // User commission earnings

        Route::get('/withdraws', [WithdrawController::class, 'index']);
        Route::post('/withdraws', [WithdrawController::class, 'store']);
        Route::post('/withdraws/{id}/approve', [WithdrawController::class, 'approve']);
});

// role based route system has to be integrated
