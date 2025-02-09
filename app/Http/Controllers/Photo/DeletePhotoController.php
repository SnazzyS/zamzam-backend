<?php

namespace App\Http\Controllers\Photo;

use App\Models\Trip;
use App\Models\Photo;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class DeletePhotoController extends Controller
{
    public function __invoke(Trip $trip, Customer $customer, Photo $photo)
    {
        if (Storage::disk('local')->exists($photo->path)) {
            Storage::disk('local')->delete($photo->path);
        }

        $photo->delete();

        return response()->json([
            'message' => 'ފޮޓޯ ޑެލީޓް ކުރެވިއްޖެ',
        ]);


    }
}
