<?php
// App\Http\Controllers\Services
$router->get('services', [ 'as' => 'services.index', 'uses' => 'Services@all' ]);
$router->post('services', [ 'as' => 'services.store', 'uses' => 'Services@add' ]);
$router->get('services/{id}', [ 'as' => 'services.show', 'uses' => 'Services@get' ]);
$router->put('services/{id}', [ 'as' => 'services.update', 'uses' => 'Services@put' ]);
$router->patch('services/{id}', [ 'as' => 'services.update', 'uses' => 'Services@put' ]);
$router->delete('services/{id}', [ 'as' => 'services.destroy', 'uses' => 'Services@remove' ]);
