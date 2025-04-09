<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\DetailController;
use App\Http\Controllers\CorrectionController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\Admin\ListController as AdminListController;
use App\Http\Controllers\Admin\DetailController as AdminDetailController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::prefix('admin')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthenticatedSessionController::class, 'create']);
        Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/attendance/list', [AdminListController::class, 'getAttendance'])->name('admin.list');
    });
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/attendance/{id}', [AdminDetailController::class, 'getDetail']);
    Route::post('/attendance/correction/update', [AdminDetailController::class, 'update'])->name('attendance.update');
    Route::get('/stamp_correction_request/list', [AdminCorrectionController::class, 'getRequestList']);
});

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'getAttendance']);
    Route::post('/attendance/work-start', [AttendanceController::class, 'workStart']);
    Route::patch('/attendance/work-end', [AttendanceController::class, 'workEnd']);
    Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart']);
    Route::patch('/attendance/break-end', [AttendanceController::class, 'breakEnd']);
    Route::get('/attendance/list', [ListController::class, 'getList'])->name('attendance.list');
    Route::get('/attendance/{id}', [DetailController::class, 'getDetail']);
    Route::post('/attendance/correction', [DetailController::class, 'correction'])->name('attendance.correction');
    Route::get('/stamp_correction_request/list', [CorrectionController::class, 'getRequestList']);
});