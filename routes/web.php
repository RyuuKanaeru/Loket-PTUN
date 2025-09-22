<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoketAdminController;
use App\Http\Controllers\UserInterfaceController;

// Redirect home to user interface
Route::get('/', [UserInterfaceController::class, 'index'])->name('home');

// User interface routes
Route::prefix('antrian')->group(function () {
    Route::get('/', [UserInterfaceController::class, 'index'])->name('antrian.index');
    Route::post('/create', [UserInterfaceController::class, 'createAntrian'])->name('antrian.create');
    Route::get('/status', [UserInterfaceController::class, 'getStatus'])->name('antrian.status');
});

// Admin routes
Route::prefix('admin')->group(function () {
    Route::get('/', [LoketAdminController::class, 'index'])->name('admin.index');
    Route::get('/loket/{loket}/set-active', [LoketAdminController::class, 'setActiveLoket'])->name('admin.set-active-loket');
    Route::get('/latest-data', [LoketAdminController::class, 'getLatestData'])->name('admin.latest-data');
    Route::post('/loket/{loket}/call-next', [LoketAdminController::class, 'callNext'])->name('admin.call-next');
    Route::post('/loket/{loket}/mark-done', [LoketAdminController::class, 'markAsDone'])->name('admin.mark-done');
    Route::put('/loket/{loket}', [LoketAdminController::class, 'updateLoket'])->name('admin.update-loket');
});
