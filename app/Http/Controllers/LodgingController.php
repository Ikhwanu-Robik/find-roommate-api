<?php

namespace App\Http\Controllers;

use App\Models\Lodging;

class LodgingController extends Controller
{
    public function index()
    {
        return response()->json([
            'lodgings' => Lodging::all(),
        ]);
    }
}
