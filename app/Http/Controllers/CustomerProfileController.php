<?php

namespace App\Http\Controllers;

use App\Models\CustomerProfile;
use Illuminate\Http\Request;

class CustomerProfileController extends Controller
{
    public function update(Request $request, CustomerProfile $customerProfile)
    {
        if ($request->user()->profile->isNot($customerProfile)) {
            return response()->json([
                'message' => 'You can only edit your own profile'
            ], 403);
        }
    }
}
