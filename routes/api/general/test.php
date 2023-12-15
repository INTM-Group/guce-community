<?php
$router->get('/error', [
  'uses' => 'Controller@error'
]);
$router->post('/is/auth', [
  'middleware' => 'auth',
  'uses' => 'Controller@authTest'
]);
