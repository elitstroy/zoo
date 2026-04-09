<?php

namespace App\Modules\Dashboard;

use App\Modules\Dashboard\Application\Queries\GetCurrentListFileQuery;
use App\Modules\Dashboard\Domain\Contracts\FileStorageInterface;
use App\Modules\Dashboard\Infrastructure\Persistence\LocalFileStorage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class DashboardServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Регистрация контрактов и реализаций
        $this->app->bind(
            FileStorageInterface::class,
            LocalFileStorage::class
        );

        // Регистрация Query
        $this->app->bind(
            GetCurrentListFileQuery::class,
            function ($app) {
                return new GetCurrentListFileQuery(
                    $app->make(FileStorageInterface::class)
                );
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Загрузка роутов модуля (корневой путь)
        Route::middleware(['web'])
            ->group(__DIR__ . '/Resources/routes/web.php');

        // Загрузка views модуля
        $this->loadViewsFrom(
            __DIR__ . '/Resources/views',
            'dashboard'
        );
    }
}