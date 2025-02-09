<?php

namespace App\Http\Controllers\Photo;

use App\Models\Trip;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PhotoStoreRequest;
use App\Models\Photo;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class UploadPhotoController extends Controller
{
    public function __invoke(Trip $trip, Customer $customer, PhotoStoreRequest $request)
    {

        $file = $request->file('photo');

        $photoType = $request->input('type');

        $fileName = time() . '_' . uniqid() . '.jpg';

        if (!Storage::disk('local')->exists('photos')) {
            Storage::disk('local')->makeDirectory('photos');
        }

        $manager = new ImageManager(new Driver());

        $image = $manager->read($file);

        switch ($photoType) {
            case 'profile_photo':
            case 'transfer_slip':
                $resizeWidth = 500;
                break;
            case 'passport_photo':
            case 'agreement_photo':
                $resizeWidth = 1000;
                break;
            default:
                $resizeWidth = 1000;
        }

        $image->scale(width: $resizeWidth);

        $destinationPath = Storage::disk('local')->path('photos/' . $fileName);

        $image->save($destinationPath);

        Photo::create([
            'trip_id' => $trip->id,
            'customer_id' => $customer->id,
            'type' => 'profile_photo',
            'file_name' => $fileName,
            'mime_type' => $file->getClientMimeType(),
            'path' => 'photos/' . $fileName,
        ]);

        return response()->json([
            'message' => 'ފޮޓޯ އަޕްލޯޑު ކުރެވިއްޖެ',
        ]);



    }

}
