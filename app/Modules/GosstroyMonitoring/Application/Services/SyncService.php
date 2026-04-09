<?php

namespace App\Modules\GosstroyMonitoring\Application\Services;

use App\Modules\GosstroyMonitoring\Application\DTOs\AuthData;
use App\Modules\GosstroyMonitoring\Domain\Contracts\GosstroyApiClientInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Сервис синхронизации с GosstroyMonitoring
 */
readonly class SyncService
{
    /**
     * @param GosstroyApiClientInterface $client
     * @param LoggerInterface $logger
     * @param array $config
     */
    public function __construct(
        private GosstroyApiClientInterface $client,
        private LoggerInterface $logger,
        private array $config
    ) {}

    /**
     * Выполнить синхронизацию: аутентификация + скачивание файла
     *
     * @param string|null $filename Опциональное имя файла (по умолчанию template.xlsx)
     * @return string|null Путь к скачанному файлу или null при ошибке
     */
    public function sync(?string $filename = null): ?string
    {
        $this->logger->info('Начало синхронизации с GosstroyMonitoring');

        try {
            // Создаём DTO для учётных данных
            $authData = new AuthData(
                $this->config['login'],
                $this->config['password']
            );

            // Аутентификация
            $this->logger->info('Аутентификация в GosstroyMonitoring API');
            $token = $this->client->login($authData);
            $this->logger->info('Аутентификация успешна');

            // Формируем путь для файла (фиксированное имя для перезаписи)
            $filename = $filename ?? $this->config['template_filename'] ?? 'template.xlsx';
            $savePath = $this->config['download_dir'] . '/' . $filename;

            // Скачиваем файл
            $this->logger->info("Скачивание файла: $savePath");
            $savedPath = $this->client->downloadFile($token, $savePath);
            
            $this->logger->info("Синхронизация завершена успешно: $savedPath");

            return $savedPath;

        } catch (RuntimeException $e) {
            $this->logger->error("Ошибка синхронизации: {$e->getMessage()}", [
                'exception' => $e,
            ]);
            return null;
        }
    }
}