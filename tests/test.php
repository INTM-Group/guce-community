<?php
//php artisan tinker ./tests/test.php
// $targets = App\Models\Ticket::all();
// foreach ($targets as $target) {
//     $target->stats = App\Http\Controllers\Activities::updateTimers($target);
//     print json_encode($target->stats).PHP_EOL;
//     print "---------------------------------------".PHP_EOL;
//     $target->save();
// }
$targets = App\Models\Ticket::find(156);
print "---------------------------------------".PHP_EOL;
print json_encode(App\Http\Controllers\Activities::updateTimers($targets), JSON_PRETTY_PRINT) . PHP_EOL;
exit;
/*
pasar por todas las actividades
si la actividad tiene una previa que tiene toStatus diferente catalogarla como cambio de estatus
Si el toStatus es menor que el formStatus, reducirlo un solo byte



1227-1230 >1232
*/
