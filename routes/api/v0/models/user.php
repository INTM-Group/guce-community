<?php
// App\Http\Controllers\Users
$router->get('users', ['as' => 'users.index', 'uses' => 'Users@all']);
$router->post('users', ['as' => 'users.store', 'uses' => 'Users@add']);
$router->get('users/{id}', ['as' => 'users.show', 'uses' => 'Users@get']);
$router->put('users/{id}', ['as' => 'users.update', 'uses' => 'Users@put']);
$router->patch('users/{id}', ['as' => 'users.update', 'uses' => 'Users@put']);
$router->delete('users/{id}', ['as' => 'users.destroy', 'uses' => 'Users@remove']);
