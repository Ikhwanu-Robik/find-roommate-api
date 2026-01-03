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
        $attributes = $request->validated();

        $customerProfile->update($attributes);

        $tags = $tagsGenerator->generate($customerProfile->bio);
        $customerProfile->syncTags($tags);

        return response()->json([
            'customer_profile' => $customerProfile
        ]);
    }
}
