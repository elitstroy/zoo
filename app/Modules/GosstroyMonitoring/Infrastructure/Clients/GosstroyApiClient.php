<?php

namespace App\Modules\GosstroyMonitoring\Infrastructure\Clients;

use App\Modules\GosstroyMonitoring\Application\DTOs\AuthData;
use App\Modules\GosstroyMonitoring\Domain\Contracts\GosstroyApiClientInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use RuntimeException;

/**
 * HTTP клиент для API GosstroyMonitoring (Guzzle)
 */
readonly class GosstroyApiClient implements GosstroyApiClientInterface
{
    /**
     * @param ClientInterface $client
     */
    public function __construct(
        private ClientInterface $client
    ) {}

    /**
     * Аутентификация в API
     *
     * @param AuthData $authData Учётные данные
     * @return string JWT токен
     * @throws RuntimeException|JsonException При ошибке аутентификации
     */
    public function login(AuthData $authData): string
    {
        try {
            $response = $this->client->post('/api/account/login', [
                'headers' => [
                    'Accept' => 'application/json, text/plain, */*',
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'GosstroyMonitoring Sync Client/1.0',
                ],
                'body' => $authData->toJson(),
            ]);

            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RuntimeException('Failed to parse login response: ' . json_last_error_msg());
            }

            if (!isset($data['token'])) {
                throw new RuntimeException('Token not found in login response');
            }

            return $data['token'];

        } catch (GuzzleException $e) {
            throw new RuntimeException('Login failed: ' . $e->getMessage(), 0, $e);
        } catch (JsonException $e) {
            throw new RuntimeException('Failed to parse login response: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Скачивание файла с авторизацией
     *
     * @param string $token JWT токен
     * @param string $savePath Путь для сохранения файла
     * @return string Путь к сохранённому файлу
     * @throws RuntimeException При ошибке скачивания
     */
    public function downloadFile(string $token, string $savePath): string
    {
        // Создаём директорию если не существует
        $dir = dirname($savePath);
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }

        try {
            $this->client->get('/api/reOrgConstructionMaterialPricePeriodChange/getExampleFile', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json, text/plain, */*',
                    'User-Agent' => 'GosstroyMonitoring Sync Client/1.0',
                ],
                'sink' => $savePath,
            ]);

            return $savePath;

        } catch (GuzzleException $e) {
            // Удаляем частично скачанный файл при ошибке
            if (file_exists($savePath)) {
                unlink($savePath);
            }
            throw new RuntimeException('File download failed: ' . $e->getMessage(), 0, $e);
        }
    }
}