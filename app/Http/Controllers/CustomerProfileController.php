<?php

namespace App\Http\Controllers;

use App\Models\CustomerProfile;
use Illuminate\Http\UploadedFile;
use App\Services\TextTagsGenerator;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\EditProfileRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomerProfileController extends Controller
{
    public function update(
        EditProfileRequest $request,
        CustomerProfile $customerProfile,
        TextTagsGenerator $tagsGenerator
    ) {
        $attributes = $request->except('profile_photo');

        $profilePhoto = $request->file('profile_photo');
        if ($profilePhoto) {
            $pathToStoredImage = $this->storeImageOrThrow($profilePhoto);
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

    private function storeImageOrThrow(UploadedFile $profilePhoto) {
        $pathToStoredImage = $profilePhoto->store('profile_pics');

        // I didn't configure filesystem to throw,
        // because Signup also store image
        // and it must still returns even if its
        // image storage failed
        if ($pathToStoredImage === false) {
            throw new HttpResponseException(response()->json([
                'message' => 'Image storage failed'
            ], 500));
        }

        return $pathToStoredImage;
    }
}
