<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\DetailController;
use App\Http\Controllers\RequestListController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\Admin\ListController as AdminListController;
use App\Http\Controllers\Admin\DetailController as AdminDetailController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\ApproveController;
use App\Http\Controllers\Admin\CsvDownloadController;

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
});

Route::middleware('auth')->group(function () {
    Route::middleware('admin')->group(function () {
        Route::get('/admin/attendance/list', [AdminListController::class, 'getAttendanceList'])->name('admin.list');
        Route::get('/admin/staff/list', [StaffController::class, 'getStaffList']);
        Route::get('/admin/attendance/staff/{id}', [StaffController::class, 'getAttendance'])->name('admin.attendance');
        Route::post('/attendance/csv', [CsvDownloadController::class, 'downloadCsv']);
        Route::put('/attendance/correction/update', [AdminDetailController::class, 'update'])->name('attendance.update');
        Route::get('/stamp_correction_request/approve/{attendance_correct_request}', [ApproveController::class, 'getCorrection'])
            ->name('admin.correction');
        Route::put('/stamp_correction_request/approve/update', [ApproveController::class, 'update']);
    });

    Route::middleware('verified')->group(function () {
        Route::get('/attendance', [AttendanceController::class, 'getAttendance']);
        Route::post('/attendance/work-start', [AttendanceController::class, 'workStart']);
        Route::patch('/attendance/work-end', [AttendanceController::class, 'workEnd']);
        Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart']);
        Route::patch('/attendance/break-end', [AttendanceController::class, 'breakEnd']);
        Route::get('/attendance/list', [ListController::class, 'getList'])->name('attendance.list');
        Route::post('/attendance/correction', [DetailController::class, 'correction'])->name('attendance.correction');
    });
});

Route::middleware('inject.role')->group(function () {
    Route::get('/attendance/{id}', [DetailController::class, 'getDetail'])->name('attendance.detail');
    Route::get('/stamp_correction_request/list', [RequestListController::class, 'getRequestList']);
});