<?php

namespace App\Modules\GosstroyMonitoring\Application\Services;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Сервис для сравнения Excel файлов и выделения новых записей
 */
readonly class ExcelCompareService
{
    private const string CODE_COLUMN = 'A'; // Колонка с кодом ККМ
    private const string YELLOW_COLOR = 'FFFF00'; // Желтый цвет для выделения

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        private LoggerInterface $logger
    ) {}

    /**
     * Сравнить template файл с актуальным перечнем и выделить новые записи
     *
     * @param string $templatePath Путь к файлу-шаблону
     * @param string $actualListPath Путь к файлу с актуальным перечнем
     * @return string Путь к обновленному файлу
     * @throws RuntimeException
     */
    public function compareAndHighlight(string $templatePath, string $actualListPath): string
    {
        $this->logger->info('Начало сравнения файлов');
        
        // Имя листа = текущая дата
        $sheetName = date('dmy');
        $this->logger->info("Имя нового листа: $sheetName");

        // Загружаем файлы
        $templateSpreadsheet = IOFactory::load($templatePath);
        $actualSpreadsheet = IOFactory::load($actualListPath);

        // Проверяем, существует ли лист с текущей датой
        $sheetNames = $actualSpreadsheet->getSheetNames();
        if (in_array($sheetName, $sheetNames, true)) {
            $this->logger->info("Лист '$sheetName' уже существует - пропускаем обработку");
            return $actualListPath;
        }

        // Получаем данные из template
        $templateSheet = $templateSpreadsheet->getActiveSheet();
        $templateData = $this->readSheetData($templateSheet);
        $this->logger->info('Прочитано строк из template: ' . count($templateData));

        // Получаем последний лист для сравнения
        $lastSheetName = $this->getLastSheetName($actualSpreadsheet);
        $this->logger->info("Последний лист для сравнения: $lastSheetName");

        // Получаем коды из последнего листа
        $previousCodes = $this->getCodesFromSheet($actualSpreadsheet, $lastSheetName);
        $this->logger->info('Кодов в последнем листе: ' . count($previousCodes));

        // Создаем новый лист
        $newSheet = $this->createNewSheet($actualSpreadsheet, $sheetName);

        // Копируем данные из template
        $this->copyData($templateSheet, $newSheet);
        $this->logger->info('Данные скопированы на новый лист');

        // Находим новые коды и выделяем их
        $newCodesCount = $this->highlightNewRecords($newSheet, $templateData, $previousCodes);
        $this->logger->info("Новых записей выделено: $newCodesCount");

        // Сохраняем файл
        $writer = IOFactory::createWriter($actualSpreadsheet, IOFactory::identify($actualListPath));
        $writer->save($actualListPath);
        $this->logger->info("Файл сохранен: $actualListPath");

        return $actualListPath;
    }

    /**
     * Получить имя последнего листа в файле
     *
     * @param Spreadsheet $spreadsheet Файл Excel
     * @return string Имя последнего листа или пустую строку, если файл пустой
     */
    private function getLastSheetName(Spreadsheet $spreadsheet): string
    {
        $sheetNames = $spreadsheet->getSheetNames();
        
        if (empty($sheetNames)) {
            throw new RuntimeException('В файле нет листов');
        }
        
        // Возвращаем имя последнего листа
        return end($sheetNames);
    }

    /**
     * Прочитать данные из листа
     *
     * @param Worksheet $sheet Лист Excel
     * @return array Данные из листа
     */
    private function readSheetData(Worksheet $sheet): array
    {
        $data = [];
        $highestRow = $sheet->getHighestRow();
        $highestColumnIndex = Coordinate::columnIndexFromString($sheet->getHighestColumn());

        for ($row = 1; $row <= $highestRow; $row++) {
            $rowData = [];
            for ($colIndex = 1; $colIndex <= $highestColumnIndex; $colIndex++) {
                $col = Coordinate::stringFromColumnIndex($colIndex);
                $rowData[$col] = $sheet->getCell($col . $row)->getValue();
            }
            $data[$row] = $rowData;
        }

        return $data;
    }

    /**
     * Получить множество кодов ККМ из листа
     *
     * @param Spreadsheet $spreadsheet Файл Excel
     * @param string $sheetName Имя листа
     * @return array Множество кодов ККМ
     */
    private function getCodesFromSheet(Spreadsheet $spreadsheet, string $sheetName): array
    {
        $sheet = $spreadsheet->getSheetByName($sheetName);
        if ($sheet === null) {
            throw new RuntimeException("Лист '$sheetName' не найден");
        }

        $codes = [];
        $highestRow = $sheet->getHighestRow();

        // Начинаем со строки 2 (пропускаем заголовок)
        for ($row = 2; $row <= $highestRow; $row++) {
            $code = $sheet->getCell(self::CODE_COLUMN . $row)->getValue();
            if ($code !== null && $code !== '') {
                $codes[(string)$code] = true;
            }
        }

        return $codes;
    }

    /**
     * Создать новый лист с указанным именем
     *
     * @param Spreadsheet $spreadsheet Файл Excel
     * @param string $sheetName Имя нового листа
     * @return Worksheet Новый лист Excel
     */
    private function createNewSheet(Spreadsheet $spreadsheet, string $sheetName): Worksheet
    {
        // Создаем новый лист
        $newSheet = new Worksheet($spreadsheet, $sheetName);
        
        // Добавляем лист в конец
        $spreadsheet->addSheet($newSheet);

        return $newSheet;
    }

    /**
     * Скопировать данные из одного листа в другой (только колонки A-E)
     *
     * @param Worksheet $source Исходный лист Excel
     * @param Worksheet $target Целевой лист Excel
     */
    private function copyData(Worksheet $source, Worksheet $target): void
    {
        $highestRow = $source->getHighestRow();
        // Копируем только 5 колонок: A, B, C, D, E
        $maxColumns = 5;

        for ($row = 1; $row <= $highestRow; $row++) {
            for ($colIndex = 1; $colIndex <= $maxColumns; $colIndex++) {
                $col = Coordinate::stringFromColumnIndex($colIndex);
                $cell = $source->getCell($col . $row);
                $target->setCellValue($col . $row, $cell->getValue());
            }
        }

        // Автоматическая ширина для колонок A-E
        foreach (range('A', 'E') as $col) {
            $target->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Выделить новые записи желтым цветом
     *
     * @param Worksheet $sheet Лист Excel
     * @param array $templateData Данные из template файла
     * @param array $previousCodes Коды из последнего листа
     * @return int Количество новых записей
     */
    private function highlightNewRecords(Worksheet $sheet, array $templateData, array $previousCodes): int
    {
        $newCodesCount = 0;

        foreach ($templateData as $rowNum => $rowData) {
            if ($rowNum === 1) {
                // Пропускаем заголовок
                continue;
            }

            $code = $rowData[self::CODE_COLUMN] ?? null;
            
            if ($code !== null && $code !== '' && !isset($previousCodes[(string)$code])) {
                // Это новый код - выделяем строку желтым (только колонки A-E)
                $this->highlightRow($sheet, $rowNum);
                $newCodesCount++;
            }
        }

        return $newCodesCount;
    }

    /**
     * Выделить строку желтым цветом (колонки A-E)
     *
     * @param Worksheet $sheet Лист Excel
     * @param int $row Номер строки для выделения
     */
    private function highlightRow(Worksheet $sheet, int $row): void
    {
        $range = 'A' . $row . ':E' . $row;
        
        $sheet->getStyle($range)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::YELLOW_COLOR],
            ],
        ]);
    }
}
