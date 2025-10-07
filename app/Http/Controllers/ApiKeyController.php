<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();
        $keys = $user->apiKeys()->get(['id', 'key', 'abilities', 'expires_at', 'created_at']);
        return response()->json($keys);
    }

}
