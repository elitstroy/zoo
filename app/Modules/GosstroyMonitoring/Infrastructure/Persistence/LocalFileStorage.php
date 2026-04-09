<?php

namespace App\Modules\GosstroyMonitoring\Infrastructure\Persistence;

use App\Modules\GosstroyMonitoring\Domain\Contracts\FileStorageInterface;

class LocalFileStorage implements FileStorageInterface
{
    /**
     * Базовый путь к хранилищу
     */
    private string $basePath;

    public function __construct()
    {
        $this->basePath = config('gosstroy-monitoring.download_dir', storage_path('app/gosstroy/downloads'));
    }

    /**
     * Проверить существование файла
     */
    public function exists(string $path): bool
    {
        return file_exists($this->getFullPath($path));
    }

    /**
     * Получить полный путь к файлу
     */
    public function getFullPath(string $path): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * Получить MIME-тип файла
     */
    public function getMimeType(string $path): string
    {
        $fullPath = $this->getFullPath($path);
        
        if (!file_exists($fullPath)) {
            return 'application/octet-stream';
        }

        $mimeTypes = [
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls'  => 'application/vnd.ms-excel',
            'pdf'  => 'application/pdf',
            'csv'  => 'text/csv',
        ];

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        
        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    /**
     * Получить размер файла в байтах
     */
    public function getSize(string $path): int
    {
        $fullPath = $this->getFullPath($path);
        
        if (!file_exists($fullPath)) {
            return 0;
        }

        return filesize($fullPath);
    }
}