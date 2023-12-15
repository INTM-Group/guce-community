<?php

namespace App\Contracts;

use Illuminate\Http\JsonResponse;

/**
 * Class SuccessApiResponse
 *
 * Réponse standardisée
 *
 */
class SuccessApiResponse extends JsonResponse
{
    /**
     * Constructor.
     *
     * @param  mixed  $data
     * @param  int  $status
     * @param  array  $headers
     * @param  int  $options
     * @return void
     */
    public function __construct($data = null, $status = 200, $headers = [], $options = 0)
    {
        $this->encodingOptions = $options;

        parent::__construct($data, $status, $headers);
    }
}
