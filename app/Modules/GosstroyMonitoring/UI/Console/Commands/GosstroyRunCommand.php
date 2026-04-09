<?php

namespace App\Modules\GosstroyMonitoring\UI\Console\Commands;

use App\Modules\GosstroyMonitoring\Application\Services\ExcelCompareService;
use App\Modules\GosstroyMonitoring\Application\Services\SyncService;
use Illuminate\Console\Command;
use RuntimeException;

/**
 * Artisan-команда для полного цикла синхронизации GosstroyMonitoring
 * Выполняет: скачивание файла → сравнение Excel
 */
class GosstroyRunCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'gosstroy:run';

    /**
     * @var string
     */
    protected $description = 'Полный цикл: скачивание файла и сравнение Excel';

    /**
     * @param SyncService $syncService
     * @param ExcelCompareService $compareService
     */
    public function __construct(
        private readonly SyncService $syncService,
        private readonly ExcelCompareService $compareService
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
        $this->info('=== Запуск полного цикла синхронизации GosstroyMonitoring ===');
        $this->newLine();

        $config = config('gosstroy-monitoring');
        $downloadDir = $config['download_dir'] ?? storage_path('app/gosstroy/downloads');
        $templateFilename = $config['template_filename'] ?? 'template.xlsx';
        $actualListFilename = $config['actual_list_filename'] ?? 'actual_list_2026.xlsx';

        // Шаг 1: Скачивание файла
        $this->info('[Шаг 1/2] Скачивание файла с API...');
        $this->line(str_repeat('-', 50));

        $templatePath = $this->syncService->sync($templateFilename);

        if ($templatePath === null) {
            $this->error('Ошибка скачивания файла. Проверьте логи.');
            return Command::FAILURE;
        }

        $this->info("Файл скачан: $templatePath");
        $this->newLine();

        // Шаг 2: Сравнение Excel
        $this->info('[Шаг 2/2] Сравнение Excel файлов...');
        $this->line(str_repeat('-', 50));

        $actualListPath = $downloadDir . '/' . $actualListFilename;

        try {
            if (!file_exists($actualListPath)) {
                throw new RuntimeException("Файл актуального перечня не найден: $actualListPath");
            }

            $resultPath = $this->compareService->compareAndHighlight($templatePath, $actualListPath);

            $this->newLine();
            $this->info('=== Синхронизация завершена успешно ===');
            $this->info("Файл обновлен: $resultPath");

            return Command::SUCCESS;

        } catch (RuntimeException $e) {
            $this->error('Ошибка сравнения: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}