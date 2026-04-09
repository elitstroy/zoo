<?php

namespace App\Modules\GosstroyMonitoring\Domain\Contracts;

interface FileStorageInterface
{
    /**
     * Проверить существование файла
     *
     * @param string $path
     * @return bool
     */
    public function exists(string $path): bool;

    /**
     * Получить полный путь к файлу
     *
     * @param string $path
     * @return string
     */
    public function getFullPath(string $path): string;

    /**
     * Получить MIME-тип файла
     *
     * @param string $path
     * @return string
     */
    public function getMimeType(string $path): string;

    /**
     * Получить размер файла в байтах
     *
     * @param string $path
     * @return int
     */
    public function getSize(string $path): int;
}