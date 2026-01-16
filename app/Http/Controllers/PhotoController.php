<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PhotoStoreRequest;
use App\Models\Customer;
use App\Models\Photo;
use App\Models\Trip;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PhotoController extends Controller
{
    public function store(Trip $trip, Customer $customer, PhotoStoreRequest $request)
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
            'type' => 'profile_photo', // Warning: Original hardcoded this to 'profile_photo'? Keeping original.
            'file_name' => $fileName,
            'mime_type' => $file->getClientMimeType(),
            'path' => 'photos/' . $fileName,
        ]);

        return redirect()
            ->back()
            ->with('success', 'ފޮޓޯ އަޕްލޯޑު ކުރެވިއްޖެ');
    }

    public function destroy(Trip $trip, Customer $customer, Photo $photo)
    {
        if (Storage::disk('local')->exists($photo->path)) {
            Storage::disk('local')->delete($photo->path);
        }

        $photo->delete();

        return redirect()
            ->back()
            ->with('success', 'ފޮޓޯ ޑެލީޓް ކުރެވިއްޖެ');
    }
}
