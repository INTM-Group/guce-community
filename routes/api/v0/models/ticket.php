<?php
// App\Http\Controllers\Tickets
$router->get('tickets', ['as' => 'tickets.index', 'uses' => 'Tickets@all']);
$router->post('tickets', ['as' => 'tickets.store', 'uses' => 'Tickets@add']);
$router->get('tickets/{id}', ['as' => 'tickets.show', 'uses' => 'Tickets@get']);
$router->put('tickets/{id}', ['as' => 'tickets.update', 'uses' => 'Tickets@put']);
$router->patch('tickets/{id}', ['as' => 'tickets.update', 'uses' => 'Tickets@put']);
$router->delete('tickets/{id}', ['as' => 'tickets.destroy', 'uses' => 'Tickets@remove']);
