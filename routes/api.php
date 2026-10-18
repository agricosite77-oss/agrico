<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ManageApplicationController;
use App\Http\Controllers\Admin\ManageSoilController;
use App\Http\Controllers\Admin\ManageUserController;
use App\Http\Controllers\Mobile\Api\AuthController;
use App\Http\Controllers\Mobile\Api\DashboardController;
use App\Http\Controllers\Mobile\Api\DownloadController;
use App\Http\Controllers\Mobile\Api\FAQController;
use App\Http\Controllers\Mobile\Api\LocationController;
use App\Http\Controllers\Mobile\Api\MunsellColorController;
use App\Http\Controllers\Mobile\Api\RealSoilController;
use App\Http\Controllers\Mobile\Api\SoilController;
use App\Http\Controllers\Mobile\Api\SoilScanController;
use App\Http\Controllers\Mobile\Api\UserRequestController;
use App\Http\Controllers\Mobile\Api\AppVersionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC MOBILE API ROUTES
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Real Soil Dataset APIs
Route::get('/real-soil/images', [RealSoilController::class, 'getImages']);
Route::get('/real-soil/file/{path}', [RealSoilController::class, 'serveFile'])->where('path', '.*');

// Live Email Existence Check
Route::get('/auth/check-email', [\App\Http\Controllers\Website\Auth\UserAuthController::class, 'checkEmail']);
Route::get('/check-email', [\App\Http\Controllers\Website\Auth\UserAuthController::class, 'checkEmail']);

// Forgot Password Flow (6-digit code)
Route::post('/forgot-password/send-code', [\App\Http\Controllers\Auth\PasswordResetCodeController::class, 'sendResetCode']);
Route::post('/forgot-password/verify-code', [\App\Http\Controllers\Auth\PasswordResetCodeController::class, 'verifyCode']);
Route::post('/forgot-password/reset', [\App\Http\Controllers\Auth\PasswordResetCodeController::class, 'resetPassword']);
Route::get('/soils', [SoilController::class, 'index']);
Route::get('/soils/most-scanned', [SoilController::class, 'mostScanned']);
Route::get('/soils/{soil}', [SoilController::class, 'show']);
Route::get('/munsell-colors', [MunsellColorController::class, 'index']);
Route::get('/faqs', [FAQController::class, 'index']);
Route::get('/locations', [LocationController::class, 'index']);
Route::post('/locations/pin', [LocationController::class, 'storePin']);
Route::get('/soil-scans', [SoilScanController::class, 'index']);
Route::post('/soil-scan', [SoilScanController::class, 'store']);
Route::get('/app-version', [AppVersionController::class, 'checkVersion']);

// Notifications API (Mobile & Web)
Route::get('/notifications', [\App\Http\Controllers\Mobile\Api\NotificationController::class, 'index']);
Route::post('/notifications/read-all', [\App\Http\Controllers\Mobile\Api\NotificationController::class, 'markAllAsRead']);
Route::post('/notifications/{id}/read', [\App\Http\Controllers\Mobile\Api\NotificationController::class, 'markAsRead']);

/*
|--------------------------------------------------------------------------
| USER + MOBILE APPLICATION API ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json([
            'status' => 'success',
            'data'   => new \App\Http\Resources\UserResource($request->user()),
        ]);
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/user/profile-photo', [AuthController::class, 'updateProfilePhoto']);
    Route::post('/user/profile', [AuthController::class, 'updateProfile']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/downloads', [DownloadController::class, 'store']);
    Route::post('/requests', [UserRequestController::class, 'store']);

    // Asynchronous Scan Pipeline (Mobile)
    Route::post('/scan/analyze', [SoilScanController::class, 'analyzeAsync']);
    Route::get('/scan/progress/{scanId}', [SoilScanController::class, 'progress']);
});


/*
|--------------------------------------------------------------------------
| ADMIN API ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);

    // MANAGE SOIL
    Route::get('/soils', [ManageSoilController::class, 'index']);
    Route::post('/soils', [ManageSoilController::class, 'store']);
    Route::get('/soils/{soil}', [ManageSoilController::class, 'show']);
    Route::put('/soils/{soil}', [ManageSoilController::class, 'update']);
    Route::delete('/soils/{soil}', [ManageSoilController::class, 'destroy']);

    // MANAGE USERS
    Route::get('/users', [ManageUserController::class, 'index']);
    Route::get('/users/{user}', [ManageUserController::class, 'show']);
    Route::put('/users/{user}', [ManageUserController::class, 'update']);
    Route::delete('/users/{user}', [ManageUserController::class, 'destroy']);

    // APPLICATION MANAGEMENT
    Route::get('/applications', [ManageApplicationController::class, 'index']);
    Route::post('/applications', [ManageApplicationController::class, 'store']);
    Route::put('/applications/{application}', [ManageApplicationController::class, 'update']);
    Route::delete('/applications/{application}', [ManageApplicationController::class, 'destroy']);

    // USER REQUEST MANAGEMENT
    Route::get('/requests', [UserRequestController::class, 'index']);
    Route::put('/requests/{request}', [UserRequestController::class, 'update']);
});
