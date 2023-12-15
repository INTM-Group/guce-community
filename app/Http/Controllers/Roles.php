<?php

namespace App\Http\Controllers;

use App\Contracts\RestController;
use App\Models\Role;

class Roles extends RestController
{
    const MODEL = Role::class;
}
