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
        $pathToStoredImage = $this->storeImage($request);
        $user = $this->createUser($request);
        $customerProfile = $this->createCustomerProfile($request, $pathToStoredImage);

        $customerProfile->user()->save($user);

        $customerUser = $this->combineCustomerAttributesTo($user);

        $response = $this->createResponse($customerUser, $pathToStoredImage);
        return response()->json($response);
    }

    private function storeImage($request)
    {
        $profilePhotoFile = $request->file('profile_photo');
        $pathToStoredImage = Storage::disk('public')->putFile('profile_pics', $profilePhotoFile);
        return $pathToStoredImage;
    }

    private function createUser($request)
    {
        $userAttributes = $request->safe(['name', 'phone', 'password']);
        $user = User::create($userAttributes);
        return $user;
    }

    private function createCustomerProfile($request, $profilePhotoPath)
    {
        $customerProfileAttributes = $request->safe(['birthdate', 'gender', 'address', 'bio']);
        $customerProfileAttributes['full_name'] = $request->validated('name');
        $customerProfileAttributes['profile_photo'] = $profilePhotoPath ? $profilePhotoPath : null;
        $customerProfile = CustomerProfile::create($customerProfileAttributes);
        return $customerProfile;
    }

    private function combineCustomerAttributesTo($user)
    {
        $user = User::with('profile')->find($user->id);

        return collect([
            'id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
            'gender' => $user->profile->gender,
            'birthdate' => $user->profile->birthdate,
            'address' => $user->profile->address,
            'bio' => $user->profile->bio,
            'profile_photo' => $user->profile->profile_photo,
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