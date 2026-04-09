<?php

use Illuminate\Support\Facades\Route;
use App\Modules\GosstroyMonitoring\UI\Http\Web\Controllers\GosstroyDownloadController;

Route::name('gosstroy.')->group(function () {
    Route::get('/download/current-list', [GosstroyDownloadController::class, 'downloadCurrentList'])->name('download.current-list');
});