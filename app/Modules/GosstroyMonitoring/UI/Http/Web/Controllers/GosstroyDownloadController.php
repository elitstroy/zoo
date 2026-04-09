<?php

namespace App\Modules\GosstroyMonitoring\UI\Http\Web\Controllers;

use App\Modules\GosstroyMonitoring\Application\Queries\GetCurrentListFileQuery;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Примерс функции нарусском для демонстрации дополнительных опций форматирования PHP.
 *
 * @param mixed $one Первый параметр.
 * @param int $two Второй параметр.
 * @param string $three Третий параметр; длинный комментарий для демонстрации переноса строк.
 *
 * @return void
 * @author J.S.
 * @license GPL
 */
class GosstroyDownloadController
{
    /**
     * @param GetCurrentListFileQuery $getCurrentListFileQuery
     */
    public function __construct(
        private GetCurrentListFileQuery $getCurrentListFileQuery
    ) {}

    /**
     * Скачать файл текущего перечня
     */
    public function downloadCurrentList(): BinaryFileResponse|Response
    {
        $fileInfo = $this->getCurrentListFileQuery->execute();

        if (!$fileInfo['exists']) {
            abort(404, 'Файл не найден');
        }

        return response()->download(
            $fileInfo['path'],
            'actual_list_gosstroy.xlsx',
            [
                'Content-Type' => $fileInfo['mimeType'],
                'Cache-Control' => 'public, max-age=3600',
            ]
        );
    }
}