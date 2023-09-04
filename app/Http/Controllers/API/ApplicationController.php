<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

class ApplicationController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'success']);
    }
}
