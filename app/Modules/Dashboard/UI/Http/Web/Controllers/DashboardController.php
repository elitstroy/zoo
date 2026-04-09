<?php

namespace App\Modules\Dashboard\UI\Http\Web\Controllers;

use Illuminate\Contracts\View\View;

class DashboardController
{
    /**
     * Главная страница Dashboard
     */
    public function index(): View
    {
        return view('dashboard::home');
    }
}
