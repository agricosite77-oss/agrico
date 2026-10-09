<?php

use App\Http\Controllers\Admin\AdminWebsiteBridgeController;
use App\Http\Controllers\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Admin\RequestActionController;
use App\Livewire\Admin\Application\UploadApplication;
use App\Livewire\Admin\Dashboard\AdminDashboard;
use App\Livewire\Admin\Faq\ManageFaq;
use App\Livewire\Admin\Map\ManageMap;
use App\Livewire\Admin\Mapping\ManageMappingHistory;
use App\Livewire\Admin\MunsellColor\ManageMunsellColor;
use App\Livewire\Admin\Request\ManageRequest;
use App\Livewire\Admin\Soil\SoilScanHistory;
use App\Livewire\Admin\Student\StudentRecord;
use App\Livewire\Admin\User\ManageUser;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ADMIN AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', function () {
    return redirect()->route('login');
})->name('admin.login');
Route::post('/admin/login', [\App\Http\Controllers\Website\Auth\UserAuthController::class, 'login'])->name('admin.login.store');
Route::get('/admin/register', [AdminAuthController::class, 'showRegisterForm'])->name('admin.register');
Route::post('/admin/register', [AdminAuthController::class, 'register'])->name('admin.register.store');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::get('/admin/go-to-website', [AdminWebsiteBridgeController::class, 'redirectToWebsite'])->name('admin.go-to-website');
Route::get('/admin/go_to_website', [AdminWebsiteBridgeController::class, 'redirectToWebsite'])->name('admin.go_to_website');
Route::get('/admin/bridge', [AdminWebsiteBridgeController::class, 'redirectToWebsite'])->name('admin.bridge_to_web');

/*
|--------------------------------------------------------------------------
| ADMIN DASHBOARD & MANAGEMENT ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['admin'])->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('/soil', SoilScanHistory::class)->name('soil.manage');
    Route::get('/munsell-color', ManageMunsellColor::class)->name('munsell-color.manage');
    Route::get('/faqs', ManageFaq::class)->name('faqs.manage');
    Route::get('/mapping-history', ManageMappingHistory::class)->name('mapping.history');
    Route::get('/manage-map', ManageMap::class)->name('map.manage');
    Route::get('/users', ManageUser::class)->name('users.manage');
    Route::get('/students', StudentRecord::class)->name('students.record');
    Route::get('/students-view', StudentRecord::class)->name('student.record');
    Route::get('/user-requests', ManageRequest::class)->name('user.requests');
    Route::get('/upload-application', UploadApplication::class)->name('application.upload');

    // Fast JSON endpoints — bypass Livewire for instant approve/disapprove & status toggles
    Route::post('/user-requests/{id}/approve',    [RequestActionController::class, 'approve'])->name('requests.approve');
    Route::post('/user-requests/{id}/disapprove', [RequestActionController::class, 'disapprove'])->name('requests.disapprove');
    Route::post('/manage-map/{id}/toggle-status', [\App\Http\Controllers\Admin\ManageMapController::class, 'toggleStatus'])->name('map.toggle-status');
    Route::post('/manage-users/{id}/suspend-one-day', [\App\Http\Controllers\Admin\ManageUserController::class, 'suspendOneDay'])->name('users.suspend-one-day');
});

