<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\SpinnerController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WithdrawController;
use App\Http\Middleware\isAdminMiddleware;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\RefreshTokenMiddleware;
use Illuminate\Support\Facades\Route;

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

    Route::get('/referrals', [ReferralController::class, 'index']); // All referrals
    Route::get('/referrals/{userId}', [ReferralController::class, 'show']); // Specific user referrals
    Route::get('/referrals/tree/{userId}', [ReferralController::class, 'getReferralNodes']); // Specific user referrals

    Route::delete('transactions/all-delete', [TransactionController::class, 'deleteMultiple'])->middleware([isAdminMiddleware::class]);
    Route::apiResource('transactions', TransactionController::class);


    Route::get('/commissions', [CommissionController::class, 'index']); // All commissions
    Route::get('/commissions/{userId}', [CommissionController::class, 'show']); // User commission earnings


    Route::get('/withdraws', [WithdrawController::class, 'index']);
    Route::post('/withdraws', [WithdrawController::class, 'store']);
    Route::post('/withdraws/{id}/approve', [WithdrawController::class, 'approve'])->middleware([isAdminMiddleware::class]);

    Route::apiResource('spinner', SpinnerController::class);
    Route::apiResource('users', CustomerController::class)->middleware([isAdminMiddleware::class]);
    Route::patch('spinner-items', [SpinnerController::class, 'updateItems']);
    Route::post('spinner-items', [SpinnerController::class, 'storeItems']);


});

Route::get('/leaderboard', function () {
    return response()->json([
        "status" => "success",
        "message" => "Leaderboard data retrieved successfully.",
        "data" => [
            "meta" => [
                "id" => 1,
                "page" => 1,
                "limit" => 10,
                "total" => 92,
                "totalPage" => 10
            ],
            "entries" => [
                [
                    "id" => 1,
                    "rank" => 1,
                    "user" => [
                        "id" => 101,
                        "name" => "John Doe",
                        "avatar" => "https://cdn.example.com/avatars/101.png"
                    ],
                    "points" => 500,
                    "reward" => "Amazon Gift Card $10",
                    "spin_id" => 205,
                    "timestamp" => "2025-02-28T12:00:00Z"
                ],
                [
                    "id" => 2,
                    "rank" => 2,
                    "user" => [
                        "id" => 102,
                        "name" => "Alice Smith",
                        "avatar" => "https://cdn.example.com/avatars/102.png"
                    ],
                    "points" => 450,
                    "reward" => "Discount Coupon 20%",
                    "spin_id" => 208,
                    "timestamp" => "2025-02-28T11:45:00Z"
                ],
                [
                    "id" => 3,
                    "rank" => 3,
                    "user" => [
                        "id" => 103,
                        "name" => "Michael Lee",
                        "avatar" => "https://cdn.example.com/avatars/103.png"
                    ],
                    "points" => 430,
                    "reward" => "Cashback $5",
                    "spin_id" => 210,
                    "timestamp" => "2025-02-28T11:30:00Z"
                ],
                [
                    "id" => 4,
                    "rank" => 4,
                    "user" => [
                        "id" => 104,
                        "name" => "Sarah Khan",
                        "avatar" => "https://cdn.example.com/avatars/104.png"
                    ],
                    "points" => 400,
                    "reward" => "Free Shipping Coupon",
                    "spin_id" => 215,
                    "timestamp" => "2025-02-28T11:15:00Z"
                ],
                [
                    "id" => 5,
                    "rank" => 5,
                    "user" => [
                        "id" => 105,
                        "name" => "David Wong",
                        "avatar" => "https://cdn.example.com/avatars/105.png"
                    ],
                    "points" => 380,
                    "reward" => "Gift Voucher $5",
                    "spin_id" => 220,
                    "timestamp" => "2025-02-28T11:00:00Z"
                ],
                [
                    "id" => 6,
                    "rank" => 6,
                    "user" => [
                        "id" => 106,
                        "name" => "Emma Brown",
                        "avatar" => "https://cdn.example.com/avatars/106.png"
                    ],
                    "points" => 350,
                    "reward" => "Extra Spin Chance",
                    "spin_id" => 225,
                    "timestamp" => "2025-02-28T10:45:00Z"
                ],
                [
                    "id" => 7,
                    "rank" => 7,
                    "user" => [
                        "id" => 107,
                        "name" => "James Wilson",
                        "avatar" => "https://cdn.example.com/avatars/107.png"
                    ],
                    "points" => 340,
                    "reward" => "Amazon Gift Card $5",
                    "spin_id" => 230,
                    "timestamp" => "2025-02-28T10:30:00Z"
                ],
                [
                    "id" => 8,
                    "rank" => 8,
                    "user" => [
                        "id" => 108,
                        "name" => "Sophia Patel",
                        "avatar" => "https://cdn.example.com/avatars/108.png"
                    ],
                    "points" => 330,
                    "reward" => "Discount Coupon 10%",
                    "spin_id" => 235,
                    "timestamp" => "2025-02-28T10:15:00Z"
                ],
                [
                    "id" => 9,
                    "rank" => 9,
                    "user" => [
                        "id" => 109,
                        "name" => "Daniel Green",
                        "avatar" => "https://cdn.example.com/avatars/109.png"
                    ],
                    "points" => 320,
                    "reward" => "Free E-book",
                    "spin_id" => 240,
                    "timestamp" => "2025-02-28T10:00:00Z"
                ],
                [
                    "id" => 10,
                    "rank" => 10,
                    "user" => [
                        "id" => 110,
                        "name" => "Olivia Kim",
                        "avatar" => "https://cdn.example.com/avatars/110.png"
                    ],
                    "points" => 300,
                    "reward" => "Exclusive Access Pass",
                    "spin_id" => 245,
                    "timestamp" => "2025-02-28T09:45:00Z"
                ]
            ]
        ]
    ]);
});

// role based route system has to be integrated
