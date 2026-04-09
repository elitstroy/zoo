<?php

use App\Providers\AppServiceProvider;
use App\Modules\Dashboard\DashboardServiceProvider;
use App\Modules\GosstroyMonitoring\GosstroyMonitoringServiceProvider;

return [
    AppServiceProvider::class,
    DashboardServiceProvider::class,
    GosstroyMonitoringServiceProvider::class,
];
