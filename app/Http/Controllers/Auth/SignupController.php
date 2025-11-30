<?php

namespace App\Http\Controllers\Auth;

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

        $attributes = $request->validated();
        $attributes['profile_photo'] = $pathToStoredImage ? $pathToStoredImage : null;
        $user = User::create($attributes);

        $response = ['user' => $user];
        if (! $pathToStoredImage) {
            $response['message'] = 'Signup successful, but image storage failed. You can try again in the profile menu';
        }
        return response()->json($response);
    }
}