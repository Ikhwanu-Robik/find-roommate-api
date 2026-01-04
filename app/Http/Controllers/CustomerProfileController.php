<?php

namespace App\Http\Controllers;

use App\Models\CustomerProfile;
use App\Services\TextTagsGenerator;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\EditProfileRequest;

class CustomerProfileController extends Controller
{
    public function update(
        EditProfileRequest $request,
        CustomerProfile $customerProfile,
        TextTagsGenerator $tagsGenerator
    ) {
        $attributes = $request->except('profile_photo');

        $profilePhoto = $request->file('profile_photo');
        $pathToStoredImage = null;
        if ($profilePhoto !== null) {
            $pathToStoredImage = $profilePhoto->store('profile_pics');
        }

        if ($pathToStoredImage === false) {
            return response()->json([
                'message' => 'Image storage failed'
            ], 500);
        }

        if ($pathToStoredImage !== null) {
            $attributes['profile_photo'] = $pathToStoredImage;

            $oldImagePath = $customerProfile->profile_photo;
            Storage::delete($oldImagePath);
        }

        $customerProfile->update($attributes);

        $tags = $tagsGenerator->generate($customerProfile->bio);
        $customerProfile->syncTags($tags);

        return response()->json([
            'customer_profile' => $customerProfile
        ]);
    }
}
