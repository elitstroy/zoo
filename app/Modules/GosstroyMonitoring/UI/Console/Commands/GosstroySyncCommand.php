<?php

namespace App\Modules\GosstroyMonitoring\UI\Console\Commands;

use App\Modules\GosstroyMonitoring\Application\Services\SyncService;
use Illuminate\Console\Command;

/**
 * Artisan-команда для синхронизации с GosstroyMonitoring
 */
class GosstroySyncCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'gosstroy:sync {--filename= : Имя файла для скачивания}';

    /**
     * @var string
     */
    protected $description = 'Синхронизация с GosstroyMonitoring API (скачивание файла)';

    /**
     * @param SyncService $syncService
     */
    public function __construct(
        private readonly SyncService $syncService
    ) {
        parent::__construct();
    }

    /**
     * Выполнить команду
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('Начало синхронизации с GosstroyMonitoring...');

        $filename = $this->option('filename');

        $result = $this->syncService->sync($filename);

        if ($result !== null) {
            $this->info("Файл успешно скачан: $result");
            return Command::SUCCESS;
        }

        $this->error('Ошибка синхронизации. Проверьте лог.');
        return Command::FAILURE;
    }
}