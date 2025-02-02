<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerPhoto;
use App\Models\Trip;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class UploadCustomerPhotoController extends Controller
{
    public function __invoke(Trip $trip, Customer $customer, Request $request)
    {

        $request->validate(['photo' => 'required|image|mimes:jpeg,png,jpg|max:10000']);

        try {
            $manager = new ImageManager(new Driver());

            $photo = $request->file('photo');

            $imageName = Str::uuid() . '.' . $photo->extension();

            if (!Storage::disk('public')->exists('profile_photo')) {
                Storage::disk('public')->makeDirectory('profile_photo');
            }

            $image = $manager->read($photo->path());

            if ($image->width() > 1000 || $image->height() > 1000) {
                $image->scaleDown(1000, 1000);
            }

            $processedImage= $image->toJpeg(80);

            Storage::disk('public')->put('profile_photo/'.$imageName, $processedImage);

            $url = Storage::disk('public')->url('profile_photo/'.$imageName);


            CustomerPhoto::create([
                'filename' => $imageName,
                'mime_type' => 'jpeg',
                'photo_url' => $url,

            ]);

            return response()->json([
                'url' => $url,
                'message' => 'Image uploaded successfully'
            ], 201, [], JSON_UNESCAPED_SLASHES);


        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);

        }
    }
}
