<?php

namespace App\Modules\GosstroyMonitoring\Application\DTOs;

use JsonException;

/**
 * Data Transfer Object для учётных данных аутентификации
 */
readonly class AuthData
{
    /**
     * @param string $login
     * @param string $password
     */
    public function __construct(
        public string $login,
        public string $password
    ) {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'login' => $this->login,
            'password' => $this->password,
        ];
    }

    /**
     * @return string
     * @throws JsonException
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }
}