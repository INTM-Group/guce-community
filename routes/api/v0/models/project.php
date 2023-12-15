<?php
// App\Http\Controllers\Projects
$router->get('projects', ['as' => 'projects.index', 'uses' => 'Projects@all']);
$router->post('projects', ['as' => 'projects.store', 'uses' => 'Projects@add']);
$router->get('projects/{id}', ['as' => 'projects.show', 'uses' => 'Projects@get']);
$router->put('projects/{id}', ['as' => 'projects.update', 'uses' => 'Projects@put']);
$router->patch('projects/{id}', ['as' => 'projects.update', 'uses' => 'Projects@put']);
$router->delete('projects/{id}', ['as' => 'projects.destroy', 'uses' => 'Projects@remove']);
