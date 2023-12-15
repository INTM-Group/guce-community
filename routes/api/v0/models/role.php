<?php
// App\Http\Controllers\Roles
$router->get('roles', ['as' => 'roles.index', 'uses' => 'Roles@all']);
$router->post('roles', ['as' => 'roles.store', 'uses' => 'Roles@add']);
$router->get('roles/{id}', ['as' => 'roles.show', 'uses' => 'Roles@get']);
$router->put('roles/{id}', ['as' => 'roles.update', 'uses' => 'Roles@put']);
$router->patch('roles/{id}', ['as' => 'roles.update', 'uses' => 'Roles@put']);
$router->delete('roles/{id}', ['as' => 'roles.destroy', 'uses' => 'Roles@remove']);
