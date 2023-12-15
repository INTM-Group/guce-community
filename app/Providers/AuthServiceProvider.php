<?php

namespace App\Providers;

use App\Models\Persistence;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\UnauthorizedException;
use Laravel\Lumen\Http\Request;
use Ramsey\Uuid\Codec\TimestampFirstCombCodec;
use Ramsey\Uuid\FeatureSet;
use Ramsey\Uuid\Generator\CombGenerator;
use Ramsey\Uuid\UuidFactory;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            $credentials = $request->header('Authorization', '');
            if ($credentials) {
                $jwt = $request->bearerToken();
            } else {
                return null;
            }
            if (!$jwt) {
                throw new AuthenticationException();
            }
            $jwtObject = JWT::decode($jwt, env('APP_KEY', 'gucu'), ['HS256']);
            try {
                $persistence = Persistence::findOrFail($jwtObject->jti);
                $dayDiff = !$persistence->updated_at->isCurrentDay();
                if ($dayDiff) {
                    $persistence->connected++;
                    if ($dayDiff == 1)
                        $persistence->consecutive++;
                    else
                        $persistence->consecutive = 0;
                    $persistence->save();
                }
                /** @var User $user */
                $user = $persistence->user;
                $dayDiff = !$user->updated_at->isCurrentDay();
                if ($dayDiff) {
                    $user->connections++;
                    $user->last_login = $user->updated_at->now();
                    $user->save();
                }
            } catch (ModelNotFoundException $e) {
                throw new AuthenticationException();
            }
            $permission = Arr::get($request->route(), '1.as');
            $permissions = $user->hasPermissions;
            if (!Arr::get($permissions, 'root', false)) {
                switch ($permission) {
                    case 'satisfactions':
                        $permission = 'activities.index';
                    default:
                        $can = Arr::has($permissions, $permission, false);
                }
                if (!$can) {
                    throw new UnauthorizedException('You do not have permission for this action');
                }
            }
            return $user;
        });
    }

    public static function uuidFactory()
    {
        $factory = new UuidFactory(new FeatureSet());
        $codec = new TimestampFirstCombCodec($factory->getUuidBuilder());
        $factory->setCodec($codec);
        $factory->setRandomGenerator(new CombGenerator(
            $factory->getRandomGenerator(),
            $factory->getNumberConverter()
        ));
        return $factory;
    }

    public static function parseUuid($uuid)
    {
        return self::uuidFactory()->fromString($uuid);
    }
}
