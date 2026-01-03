<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditProfileRequest;
use App\Models\CustomerProfile;

class CustomerProfileController extends Controller
{
    public function update(EditProfileRequest $request, CustomerProfile $customerProfile)
    {
        $attributes = $request->validated();
        
        $customerProfile->update($attributes);

        return response()->json([
            'customer_profile' => $customerProfile
        ]);
    }
}
