<?php

namespace App\Http\Controllers;

use App\Contracts\RestController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Users extends RestController
{
    const MODEL = User::class;

    /**
     * @param  Request  $request
     *
     * @return \Illuminate\Http\JsonResponse|Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function add(Request $request)
    {
        $this->syncRoles($request);
        return parent::add($request);
    }

    /**
     * @param  Request  $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function put(Request $request, $id)
    {
        $this->syncRoles($request);
        return parent::put($request, $id);
    }

    protected function syncRoles(Request $request)
    {
        /** @var $m User */
        $m = self::MODEL;
        $m::$rolesForSync = is_array($request->roles) ? $request->roles : [$request->roles];
    }
}
