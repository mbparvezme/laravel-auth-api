<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use App\Traits\AppTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{

    use AppTrait;

    public function index(Request $request)
    {
        $user = $request->user();
        $keys = $user->apiKeys()->get(['id', 'key', 'expires_at', 'created_at']);
        return $this->apiResponse(success: true, message: __('app.API_KEY_ALL'), data: $keys);
    }

    public function storeByUser(Request $request)
    {
        $validator = Validator::make($request->all(), ['abilities' => 'nullable|array', 'name' => 'required|string|max:64']);

        if ($validator->fails()) {
            return $this->apiResponse(success: false, message: $validator->errors(), code: 422);
        }

        $expires = $request->expires_at ? Carbon::parse($request->expires_at) : Carbon::now()->addDays(90);
        $apiKey = ApiKey::generateForUser($request->user()->id, $request->name, $request->abilities, $expires);

        return $this->apiResponse(success: true, message: __('app.API_KEY_CREATE'), data: [
            'id' => $apiKey->id,
            'name' => $apiKey->name,
            'key' => $apiKey->key,
            'secret' => $apiKey->plain_secret,
            'expires_at' => $apiKey->expires_at
        ]);
    }

    public function regenerate(Request $request, $id)
    {
        $user = $request->user();
        $apiKey = ApiKey::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        $plainSecret = Str::random(64);
        $apiKey->secret = hash('sha256', $plainSecret);
        $apiKey->save();

        return $this->apiResponse(success: true, message: __('app.API_KEY_REGENERATE'), data: [
            'key' => $apiKey->key,
            'secret' => $plainSecret,
            'abilities' => $apiKey->abilities,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $apiKey = ApiKey::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $apiKey->delete();
        return $this->apiResponse(success: true, message: __('app.API_KEY_DELETE'));
    }

}
