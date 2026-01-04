<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditProfileRequest;
use App\Models\CustomerProfile;
use App\Services\TextTagsGenerator;

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

        if ($pathToStoredImage !== null) {
            $attributes['profile_photo'] = $pathToStoredImage;
        }

        $customerProfile->update($attributes);

        $tags = $tagsGenerator->generate($customerProfile->bio);
        $customerProfile->syncTags($tags);

        return response()->json([
            'customer_profile' => $customerProfile
        ]);
    }
}
