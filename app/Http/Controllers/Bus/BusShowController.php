<?php

namespace App\Http\Controllers\Bus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BusShowController extends Controller
// show is used for single bus, not to all buses


{
    public function __invoke()
    {
        dd('BusShowController');
    }
}
