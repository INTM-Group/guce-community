<?php


$router->get('/', [
  'uses' => 'Controller@info'
]);

$router->group([
  'prefix' => 'v'.APP_API,
], function ($router) {
    require __DIR__.'/api/v'.APP_API.'.php';
});
