<?php
//php artisan tinker ./tests/update-stats.php

$targets = App\Models\Ticket::all();

foreach ($targets as $target) {
    $target->stats = App\Http\Controllers\Activities::updateTimers($target);
    $target->save();
    echo $target->id . PHP_EOL;
    echo json_encode($target->stats) . PHP_EOL;
}
