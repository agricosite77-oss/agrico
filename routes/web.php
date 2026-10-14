<?php

use App\Http\Controllers\Auth\PasswordResetCodeController;
use App\Http\Controllers\Website\Auth\UserAuthController;
use App\Http\Controllers\Website\TrainingController;
use App\Http\Controllers\Website\WebsiteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WEBSITE USER AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/login', [UserAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserAuthController::class, 'login'])->name('login.store');
Route::get('/user/login', [UserAuthController::class, 'showLoginForm'])->name('user.login');
Route::post('/user/login', [UserAuthController::class, 'login'])->name('user.login.store');

Route::get('/register', [UserAuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [UserAuthController::class, 'register'])->name('register.store');
Route::get('/user/register', [UserAuthController::class, 'showRegisterForm'])->name('user.register');
Route::post('/user/register', [UserAuthController::class, 'register'])->name('user.register.store');

Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');
Route::post('/user/logout', [UserAuthController::class, 'logout'])->name('user.logout');
Route::post('/user/theme', [UserAuthController::class, 'updateTheme'])->name('user.update-theme');

// Suspended Account Reactivation Self-Request
Route::post('/account/request-reactivation', [UserAuthController::class, 'requestReactivation'])->name('account.request-reactivation');

// Real-time AJAX Email Existence Check
Route::get('/auth/check-email', [UserAuthController::class, 'checkEmail'])->name('auth.check-email');

// Password Reset Flow (6-digit code)
Route::get('/forgot-password', [PasswordResetCodeController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password/send-code', [PasswordResetCodeController::class, 'sendResetCode'])->name('password.send-code');
Route::get('/forgot-password/verify-code', [PasswordResetCodeController::class, 'showVerifyCodeForm'])->name('password.verify');
Route::post('/forgot-password/verify-code', [PasswordResetCodeController::class, 'verifyCode'])->name('password.verify-code');
Route::get('/forgot-password/reset', [PasswordResetCodeController::class, 'showResetPasswordForm'])->name('password.reset.form');
Route::post('/forgot-password/reset', [PasswordResetCodeController::class, 'resetPassword'])->name('password.reset.submit');
Route::post('/reset-password', [PasswordResetCodeController::class, 'resetPassword'])->name('password.update');


/*
|--------------------------------------------------------------------------
| PUBLIC WEBSITE PORTAL ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('splash');
})->name('splash');

Route::get('/verify', function () {
    return view('verify', ['next' => request('next', 'login')]);
})->name('verify');

Route::redirect('/main', '/home')->name('home');

Route::prefix('')->name('website.')->group(function () {
    Route::get('/home', [WebsiteController::class, 'home'])->name('home');
    Route::get('/scan', [WebsiteController::class, 'scan'])->name('scan');
    Route::post('/scan/analyze', [WebsiteController::class, 'analyzeScan'])->name('scan.analyze');
    Route::post('/scan/store', [WebsiteController::class, 'storeScan'])->name('scan.store');
    Route::post('/scan/pin', [WebsiteController::class, 'pinLocation'])->name('scan.pin');
    Route::get('/scan/progress/{scanId}', [WebsiteController::class, 'scanProgress'])->name('scan.progress');
    Route::get('/training', [TrainingController::class, 'index'])->name('training.index');
    Route::post('/training/save', [TrainingController::class, 'save'])->name('training.save');
    Route::get('/map', [WebsiteController::class, 'map'])->name('map');
    Route::get('/munsell', [WebsiteController::class, 'munsell'])->name('munsell');
    Route::get('/faq', [WebsiteController::class, 'faq'])->name('faq');
    Route::get('/download', [WebsiteController::class, 'downloadApp'])->name('download');

    Route::get('/profile', [WebsiteController::class, 'profile'])
        ->middleware('auth:web,admin')
        ->name('profile');

    Route::post('/profile/update', [WebsiteController::class, 'updateProfile'])
        ->middleware('auth:web,admin')
        ->name('profile.update');

    Route::post('/profile/verify-password', [WebsiteController::class, 'verifyPassword'])
        ->middleware('auth:web,admin')
        ->name('profile.verify-password');

    Route::get('/notifications', [\App\Http\Controllers\Mobile\Api\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Mobile\Api\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\Mobile\Api\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

require __DIR__.'/admin.php';

