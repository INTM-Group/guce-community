<?php
namespace App\Http\Controllers;

use App\Contracts\RestController;
use App\Models\Service;

class Services extends RestController
{
    const MODEL = Service::class;
}
