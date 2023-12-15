<?php
// App\Http\Controllers\Activities
$router->get('{targetType}/{targetId}/activities', ['as' => 'activities.index', 'uses' => 'Activities@allOf']);
$router->post('{targetType}/{targetId}/activities', ['as' => 'activities.store', 'uses' => 'Activities@addTo']);
$router->get('activities/{id}', ['as' => 'activities.show', 'uses' => 'Activities@get']);
$router->put('activities/{id}', ['as' => 'activities.update', 'uses' => 'Activities@put']);
$router->patch('activities/{id}', ['as' => 'activities.update', 'uses' => 'Activities@put']);
$router->delete('activities/{id}', ['as' => 'activities.destroy', 'uses' => 'Activities@remove']);
$router->get('satisfactions', ['as' => 'satisfactions', 'uses' => 'Activities@satisfactions']);
