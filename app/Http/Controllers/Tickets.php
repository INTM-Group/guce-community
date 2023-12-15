<?php

namespace App\Http\Controllers;

use App\Contracts\RestController;
use App\Models\Ticket;

class Tickets extends RestController
{
    const MODEL = Ticket::class;
}
