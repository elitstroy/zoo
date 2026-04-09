<?php

namespace App\Modules\GosstroyMonitoring\Domain\Contracts;

use App\Modules\GosstroyMonitoring\Application\DTOs\AuthData;
use RuntimeException;

/**
 * Интерфейс клиента для API GosstroyMonitoring
 */
interface GosstroyApiClientInterface
{
    /**
     * Аутентификация в API
     *
     * @param AuthData $authData Учётные данные
     * @return string JWT токен
     * @throws RuntimeException При ошибке аутентификации
     */
    public function login(AuthData $authData): string;

    /**
     * Скачивание файла с авторизацией
     *
     * @param string $token JWT токен
     * @param string $savePath Путь для сохранения файла
     * @return string Путь к сохранённому файлу
     * @throws RuntimeException При ошибке скачивания
     */
    public function downloadFile(string $token, string $savePath): string;
}