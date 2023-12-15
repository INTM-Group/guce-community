<?php
$router->post('auth/activation', [
    'uses' => 'AuthController@activation'
]);
$router->post('auth/reset/password', [
    'uses' => 'AuthController@resetPassword'
]);
$router->post('auth', [
    'uses' => 'AuthController@validation'
]);
$router->delete('auth', [
    'uses' => 'AuthController@logout'
]);
