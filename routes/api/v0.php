<?php

use Laravel\Lumen\Routing\Router;

/** @var Router $router */
$router->get('/', ['uses' => 'Controller@version']);
if (env('APP_DEBUG', true)) {
    require __DIR__ . '/general/test.php';
}
require __DIR__ . '/general/auth.php';
$router->post('/upload/{path}', [
    'middleware' => 'auth',
    'uses' => 'Controller@uploadFile'
]);
$router->group([
    'middleware' => 'auth',
], function ($router) {
    require __DIR__ . '/v' . APP_API . '/models/role.php';
    require __DIR__ . '/v' . APP_API . '/models/services.php';
    require __DIR__ . '/v' . APP_API . '/models/user.php';
    require __DIR__ . '/v' . APP_API . '/models/ticket.php';
    require __DIR__ . '/v' . APP_API . '/models/project.php';
    require __DIR__ . '/v' . APP_API . '/models/activity.php';
});
