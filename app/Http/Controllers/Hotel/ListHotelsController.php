<?php

namespace App\Http\Controllers\Hotel;

use App\Models\Hotel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ListHotelsController extends Controller
{
    public function __invoke(Hotel $hotels)
    {
       
        return response()->json($hotels->get(), 200);
    }
}
