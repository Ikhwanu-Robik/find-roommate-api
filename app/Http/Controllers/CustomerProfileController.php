<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
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
        $attributes = $request->except(['profile_photo', '_method']);
        $profilePhoto = $request->file('profile_photo');
        $oldImagePath = null;
        $newImagePath = null;

        if ($profilePhoto) {
            $newImagePath = $this->storeImageOrThrow($profilePhoto);
            $oldImagePath = $customerProfile->profile_photo;

            $attributes['profile_photo'] = $newImagePath;
        }

        $this->updateWithoutSaving($customerProfile, $attributes);

        try {
            if ($customerProfile->isDirty('bio')) {
                $newTags = $tagsGenerator->generate($customerProfile->bio);
                $customerProfile->syncTags($newTags);
            }

            $customerProfile->save();
            
            // for some reason, isDirty('profile_photo') doesn't work
            if ($newImagePath) {
                Storage::delete($oldImagePath);
            }

            return response()->json([
                'customer_profile' => $customerProfile
            ]);
        } catch (Exception $e) {
            Storage::delete($newImagePath);

            $customerProfile->refresh();
            $oldTags = $tagsGenerator->generate($customerProfile->bio);
            $customerProfile->syncTags($oldTags);

            throw $e;
        }
    }

    private function updateWithoutSaving(
        CustomerProfile $customerProfile,
        array $attributes
    ) {
        foreach ($attributes as $key => $value) {
            $customerProfile[$key] = $value;
        }
    }

    private function storeImageOrThrow(UploadedFile $profilePhoto)
    {
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

    public function getSelf(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('profile'),
        ]);
    }
}
