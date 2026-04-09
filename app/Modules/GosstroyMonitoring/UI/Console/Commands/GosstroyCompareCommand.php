<?php

namespace App\Modules\GosstroyMonitoring\UI\Console\Commands;

use App\Modules\GosstroyMonitoring\Application\Services\ExcelCompareService;
use Illuminate\Console\Command;
use RuntimeException;

/**
 * Artisan-команда для сравнения Excel файлов и выделения новых записей
 */
class GosstroyCompareCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'gosstroy:compare 
        {--template= : Путь к template файлу} 
        {--actual= : Путь к файлу актуального перечня}';

    /**
     * @var string
     */
    protected $description = 'Сравнение Excel файлов и выделение новых записей';

    /**
     * @param ExcelCompareService $compareService
     */
    public function __construct(
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
        $config = config('gosstroy-monitoring');
        $downloadDir = $config['download_dir'] ?? storage_path('app/gosstroy/downloads');

        try {
            // Определяем пути к файлам
            $templatePath = $this->option('template') 
                ?? $downloadDir . '/' . ($config['template_filename'] ?? 'template.xlsx');
            $actualListPath = $this->option('actual') 
                ?? $downloadDir . '/' . ($config['actual_list_filename'] ?? 'current_list_2026.xlsx');

            // Проверяем существование файлов
            if (!file_exists($templatePath)) {
                throw new RuntimeException("Template файл не найден: $templatePath");
            }
            if (!file_exists($actualListPath)) {
                throw new RuntimeException("Файл актуального перечня не найден: $actualListPath");
            }

            $this->info("Template файл: $templatePath");
            $this->info("Актуальный перечень: $actualListPath");
            $this->line(str_repeat('-', 50));

            $resultPath = $this->compareService->compareAndHighlight($templatePath, $actualListPath);

            $this->line(str_repeat('-', 50));
            $this->info("Готово! Файл обновлен: $resultPath");

            return Command::SUCCESS;

        } catch (RuntimeException $e) {
            $this->error('Ошибка: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}