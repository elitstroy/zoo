<?php

namespace App\Modules\GosstroyMonitoring;

use App\Modules\GosstroyMonitoring\Application\Queries\GetCurrentListFileQuery;
use App\Modules\GosstroyMonitoring\Application\Services\ExcelCompareService;
use App\Modules\GosstroyMonitoring\Application\Services\SyncService;
use App\Modules\GosstroyMonitoring\Domain\Contracts\FileStorageInterface;
use App\Modules\GosstroyMonitoring\Domain\Contracts\GosstroyApiClientInterface;
use App\Modules\GosstroyMonitoring\Infrastructure\Clients\GosstroyApiClient;
use App\Modules\GosstroyMonitoring\Infrastructure\Persistence\LocalFileStorage;
use App\Modules\GosstroyMonitoring\UI\Console\Commands\GosstroyCompareCommand;
use App\Modules\GosstroyMonitoring\UI\Console\Commands\GosstroyRunCommand;
use App\Modules\GosstroyMonitoring\UI\Console\Commands\GosstroySyncCommand;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class GosstroyMonitoringServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Загрузка конфигурации
        $this->mergeConfigFrom(
            __DIR__ . '/Resources/config/gosstroy-monitoring.php',
            'gosstroy-monitoring'
        );

        // Регистрация Guzzle клиента для Gosstroy API
        $this->app->bind(ClientInterface::class, function ($app) {
            $config = $app->make('config')->get('gosstroy-monitoring');
            return new Client([
                'base_uri' => $config['api_url'],
                'timeout' => 120,
                'connect_timeout' => 30,
            ]);
        });

        // Регистрация Gosstroy API клиента
        $this->app->bind(
            GosstroyApiClientInterface::class,
            GosstroyApiClient::class
        );

        // Регистрация SyncService
        $this->app->bind(SyncService::class, function ($app) {
            return new SyncService(
                $app->make(GosstroyApiClientInterface::class),
                $app->make(LoggerInterface::class),
                $app->make('config')->get('gosstroy-monitoring')
            );
        });

        // Регистрация ExcelCompareService
        $this->app->bind(ExcelCompareService::class, function ($app) {
            return new ExcelCompareService(
                $app->make(LoggerInterface::class)
            );
        });

        // Регистрация FileStorage
        $this->app->bind(FileStorageInterface::class, LocalFileStorage::class);

        // Регистрация GetCurrentListFileQuery
        $this->app->bind(GetCurrentListFileQuery::class, function ($app) {
            return new GetCurrentListFileQuery(
                $app->make(FileStorageInterface::class),
                $app->make('config')->get('gosstroy-monitoring.actual_list_filename', 'actual_list_2026.xlsx')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Публикация конфигурации
        $this->publishes([
            __DIR__ . '/Resources/config/gosstroy-monitoring.php' => config_path('gosstroy-monitoring.php'),
        ], 'gosstroy-config');

        // Загрузка роутов
        $this->loadRoutesFrom(__DIR__ . '/Resources/routes/web.php');

        // Регистрация Artisan-команд
        if ($this->app->runningInConsole()) {
            $this->commands([
                GosstroySyncCommand::class,
                GosstroyCompareCommand::class,
                GosstroyRunCommand::class,
            ]);
        }
    }
}
