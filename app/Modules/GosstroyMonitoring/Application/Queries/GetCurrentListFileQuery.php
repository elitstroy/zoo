<?php

namespace App\Modules\GosstroyMonitoring\Application\Queries;

use App\Modules\GosstroyMonitoring\Domain\Contracts\FileStorageInterface;

readonly class GetCurrentListFileQuery
{
    /**
     * @param FileStorageInterface $fileStorage
     * @param string $filename
     */
    public function __construct(
        private FileStorageInterface $fileStorage,
        private string $filename
    ) {}

    /**
     * Получить информацию о файле текущего перечня
     */
    public function execute(): array
    {
        if (!$this->fileStorage->exists($this->filename)) {
            return [
                'exists' => false,
                'path' => null,
                'mimeType' => null,
                'size' => 0,
            ];
        }

        return [
            'exists' => true,
            'path' => $this->fileStorage->getFullPath($this->filename),
            'mimeType' => $this->fileStorage->getMimeType($this->filename),
            'size' => $this->fileStorage->getSize($this->filename),
        ];
    }
}