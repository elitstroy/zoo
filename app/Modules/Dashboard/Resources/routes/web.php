<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Dashboard\UI\Http\Web\Controllers\DashboardController;

Route::name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');
});
