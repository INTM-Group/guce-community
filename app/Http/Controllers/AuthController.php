<?php

namespace App\Http\Controllers;

use App\Contracts\SuccessApiResponse;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Models\Persistence;
use App\Tools;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Messages\ResetPasswordMail;

class AuthController extends BaseController
{
    /**
     * Génération de jeton pour l'accès API
     *
     * @param Request $request Request
     * @return SuccessApiResponse
     * @throws AuthenticationException
     **/
    public function validation(Request $request)
    {
        $this->validate($request, [
            'email' => 'email|required|string',
            'password' => 'required|string',
        ]);
        $credentials = $request->only(['email']);
        /** @var User $user */
        try {
            $user = User::where($credentials)->where('type', '!=', 0)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new AuthenticationException();
        }
        if (!Hash::check($request->input('password'), $user->password)) {
            throw new AuthenticationException();
        }

        return $this->userLogued($user);
    }

    /**
     * Activation de compte et connexion
     *
     * @param Request $request Request
     * @return SuccessApiResponse
     * @throws AuthenticationException
     **/
    public function logout(Request $request)
    {
        $credentials = $request->header('Authorization', '');
        if ($credentials) {
            $jwt = $request->bearerToken();
            $jwtObject = JWT::decode($jwt, env('APP_KEY', 'gucu'), ['HS256']);
            /** @var Persistence $persistence */
            $persistence = Persistence::find($jwtObject->jti);
            $userId = $persistence->user_id;
            if ($persistence) {
                $persistence->delete();
                Persistence::where('updated_at', '<', Carbon::now()->subMonth())
                    ->where('user_id', $userId)
                    ->delete();
            }
        }
        return new SuccessApiResponse(true);
    }

    /**
     * Activation de compte et connexion
     *
     * @param Request $request Request
     * @return SuccessApiResponse
     * @throws AuthenticationException
     **/
    public function activation(Request $request)
    {
        $this->validate($request, [
            'code' => 'required|string', //min at max
            'password' => 'required|confirmed|string',
        ]);
        $credentials = ['remember_token' => $request->input('code')];
        /** @var User $user */
        try {
            $user = User::where($credentials)->where('type', '!=', 0)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new AuthenticationException();
        }
        if (!Hash::check($request->input('code'), $user->password)) {
            throw new AuthenticationException();
        }
        $user->password = $request->get('password');

        return $this->userLogued($user);
    }

    /**
     * Reinitialization mot de passe
     *
     * @param Request $request Request
     * @return SuccessApiResponse
     * @throws AuthenticationException
     **/
    public function resetPassword(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email'
        ]);
        /** @var User $user */
        try {
            $user = User::whereEmail($request->get("email", "none"))->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new AuthenticationException();
        }
        $user->remember_token = Str::random(8);
        $user->password = $user->remember_token;
        $user->save();
        try {
            Mail::to($user)->send(new ResetPasswordMail($user, Auth::user()));
        } catch (\Exception $exception) {
            Tools::teamsAlert('Server Mail error: ' . $exception->getMessage());
        }
        return new SuccessApiResponse(true);
    }

    /**
     * Génération de jeton d'accès pour l'utilisateur autorisé
     *
     * @param Request $request Request
     * @return SuccessApiResponse
     * @throws AuthenticationException
     **/
    private function userLogued(User $user)
    {
        $user->last_login = Carbon::now();
        $user->generateToken();
        $user->save();
        Auth::setUser($user);
        $key = env('APP_KEY', 'gucu');
        $payload = [
            "iss" => "gucu", //Issuer
            //"sub"=>'',//Subject
            "aud" => "user", //Audience
            "jti" => $user->remember_token, //JWT ID
            "iat" => $user->last_login->getTimestamp(), //Issued At
            //"exp"=>$user->lastLogin->getTimestamp(),//Expiration Time
            //"nbf" => $user->lastLogin->getTimestamp() //Not Before
        ];

        return new SuccessApiResponse([
            'token' => JWT::encode($payload, $key),
            'user' => Arr::only($user->toArray(), [
                'email',
                'type',
                'service_id',
                'first_name',
                'last_name',
                'preferences',
                'has_permissions',
            ]),
        ]);
    }
}
