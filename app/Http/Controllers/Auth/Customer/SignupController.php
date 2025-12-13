<?php

namespace App\Http\Controllers\Auth\Customer;

use App\Models\CustomerProfile;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Auth\SignupRequest;

class SignupController extends Controller
{
    public function __invoke(SignupRequest $request)
    {
        $profilePhotoFile = $request->file('profile_photo');
        $pathToStoredImage = Storage::disk('public')->putFile('profile_pics', $profilePhotoFile);

        $userAttributes = $request->safe(['name', 'phone', 'password']);
        $user = User::create($userAttributes);

        $customerProfileAttributes = $request->safe(['birthdate', 'gender', 'address', 'bio']);
        $customerProfile = $this->createCustomerProfile(
            $user,
            $customerProfileAttributes,
            $pathToStoredImage
        );

        $customerUser = $this->combineCustomerAttributes($customerProfile, $user);

        $response = $this->createResponse($customerUser, $pathToStoredImage);
        return response()->json($response);
    }

    private function createCustomerProfile($user, $attributes, $profilePhotoPath)
    {
        $attributes['full_name'] = $user->name;
        $attributes['profile_photo'] = $profilePhotoPath ? $profilePhotoPath : null;
        $customerProfile = CustomerProfile::create($attributes);

        $customerProfile->user()->save($user);
        
        return $customerProfile;
    }

    private function combineCustomerAttributes($customerProfile, $user)
    {
        return collect([
            'id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
            'gender' => $customerProfile->gender,
            'birthdate' => $customerProfile->birthdate,
            'address' => $customerProfile->address,
            'bio' => $customerProfile->bio,
            'profile_photo' => $customerProfile->profile_photo,
        ]);
    }

    private function createResponse($customerUser, $pathToStoredImage)
    {
        $response = ['user' => $customerUser];
        if (!$pathToStoredImage) {
            $response['message'] = 'Signup successful, but image storage failed. You can try again in the profile menu';
        }
        return $response;
    }
}