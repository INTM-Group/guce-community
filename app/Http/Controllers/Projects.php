<?php

namespace App\Http\Controllers;

use App\Contracts\RestController;
use App\Models\Project;

class Projects extends RestController
{
    const MODEL = Project::class;
}
