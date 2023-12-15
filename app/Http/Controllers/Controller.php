<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller as BaseController;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Tools;

class Controller extends BaseController
{
    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function info(Request $request)
    {
        $simple = Arr::only(APP_DEFINITION, [
            "description",
            "version"
        ]);
        Arr::set($simple, 'api', 'v' . APP_API);

        return $simple;
    }

    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function version(Request $request)
    {
        $result = [
            'error' => false,
            'data' => [
                'connected' => !!Auth::user(),
                'guest' => !$request->user(),
                'token' => $request->bearerToken(),
                'env' => env('APP_NAME'),
                'messages' => 'API ' . APP_VERSION
            ]
        ];
        // Mail::to(User::find(1))->send(new SimpleMail());

        return response()
            ->json($result)
            ->setCallback($request->input('callback'));
    }
    /**
     * @return JsonResponse
     */
    public function authTest()
    {
        return response()->json([
            'error' => false,
            'data' => [
                'connected' => !!Auth::user(),
                'user' => Auth::user(),
            ]
        ]);
    }
    /**
     * @return JsonResponse
     */
    public function uploadFile(Request $request, $path)
    {
        $files = $request->file('files');
        $error = false;
        $data = [
            'path'=>$path
        ];
        if ($files) {
            $destination = storage_path('private/guce/' . $path);
            if (!file_exists($destination)) {
                mkdir($destination, 0754, true);
            }
            foreach ($files as $file) {
                $fileName = Str::snake($file->getClientOriginalName());
                if (file_exists($destination . DIRECTORY_SEPARATOR . $fileName)) {
                    $fileName = date('Ymd-his-') . $fileName;
                }
                $file->move($destination, $fileName);
                $data['files'][] = $destination . DIRECTORY_SEPARATOR . $fileName;
            }
        }
        Tools::teamsAlert('Server (SNCF) upload: ' . $fileName);
        return response()->json([
            'error' => $error,
            'data' => $data
        ]);
    }
}
