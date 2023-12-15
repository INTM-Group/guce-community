<?php

namespace App\Contracts;

use Alograg\Abstracts\RESTControllerAbstract;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class RestController extends RESTControllerAbstract
{
    /**
     * @param $status
     * @param  array  $data
     *
     * @return \Illuminate\Http\JsonResponse|Response|\Laravel\Lumen\Http\ResponseFactory
     */
    protected function respond($status, $data = [])
    {
        if ($status == Response::HTTP_NOT_FOUND) {
            throw new NotFoundHttpException();
        }

        return new SuccessApiResponse($status == Response::HTTP_NO_CONTENT ? null : $data, $status);
    }
}
