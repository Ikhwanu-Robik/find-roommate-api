<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Rules\IndonesianPhoneNumber;

class UserController extends Controller
{
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes',
            'phone' => [
                'sometimes',
                new IndonesianPhoneNumber,
                'unique:users,phone'
            ],
            'email' => [
                'sometimes',
                'email',
                'unique:users,email'
            ]
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email
        ]);

        return response()->json([
            'user' => $user->refresh()
        ]);
    }
}