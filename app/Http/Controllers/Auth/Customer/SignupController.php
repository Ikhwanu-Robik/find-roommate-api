<?php

namespace App\Http\Controllers\Auth\Customer;

use App\Http\Requests\Auth\SignupAndCreateProfileRequest;
use App\Models\User;
use App\Models\CustomerProfile;
use App\Services\TextTagsGenerator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignupRequest;

class SignupController extends Controller
{
    public function signupAndCreateProfile(
        SignupAndCreateProfileRequest $request,
        TextTagsGenerator $tagsGenerator
    ) {
        $pathToStoredImage = $request->file('profile_photo')->store('profile_pics');

        $userAttributes = $request->safe(['name', 'phone', 'password']);
        $user = User::make($userAttributes);

        $profileAttributes = $request->safe(['birthdate', 'gender', 'address', 'bio']);
        $profileAttributes['full_name'] = $user->name;
        $profileAttributes['profile_photo'] = $pathToStoredImage ?: null;
        $customerProfile = CustomerProfile::make($profileAttributes);

        $tags = $tagsGenerator->generate($customerProfile->bio);

        $customerProfile->save();
        $customerProfile->attachTags($tags);
        $user->save();
        $customerProfile->user()->save($user);

        $response = ['user' => $user->load('profile')];
        if (!$pathToStoredImage) {
            $response['message'] = 'Signup successful, but image storage failed. You can try again in the profile menu';
        }

        return response()->json($response);
    }

    public function signup(SignupRequest $request)
    {
        $user = User::create($request->validated());

        return response()->json([
            'user' => $user
        ]);
    }
}
