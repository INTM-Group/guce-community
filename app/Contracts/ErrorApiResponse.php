<?php

namespace App\Contracts;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * Class ErrorApiResponse
 *
 * Réponse d'erreur standardisée
 *
 */
class ErrorApiResponse extends JsonResponse
{
    /**
     * Constructor.
     *
     * @param  Exception  $exception
     * @param  int  $status
     * @param  array  $headers
     * @param  int  $options
     * @return void
     */
    public function __construct($exception, $status = 500, $headers = [], $options = 0)
    {
        $errorType = Str::of(class_basename(get_class($exception)));

        $data = [
        'message' => utf8_encode($exception->getMessage()),
        'error' => $errorType
        ->kebab()
        ->replace(['\\-', '-'], '.'),
        ];

        if (env('APP_DEBUG', false)) {
            $data['full'] = get_class($exception);
            $data['trace'] = array_slice($exception->getTrace(), 0, 3);
        }

        $this->encodingOptions = $options;

        parent::__construct($data, $status, $headers);
    }
}
